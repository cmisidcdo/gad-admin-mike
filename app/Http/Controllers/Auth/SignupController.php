<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TblRefDepartment;
use App\Models\User;
use App\Services\UserPermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SignupController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the signup page (redirects to login page with tab=signup).
     */
    public function showForm()
    {
        return redirect()->route('login')->with('open_signup', true);
    }

    /**
     * Handle signup form submission.
     */
    public function store(Request $request)
    {
        // Validate captcha first
        $captchaAnswer = Session::get('signup_captcha_answer');
        if ((int) $request->captcha_answer !== (int) $captchaAnswer) {
            return back()
                ->withInput($request->except('password', 'password_confirmation', 'captcha_answer'))
                ->withErrors(['captcha_answer' => 'Incorrect answer. Please try again.'])
                ->with('open_signup', true);
        }

        // Validate fields
        $validator = Validator::make($request->all(), [
            'user_id'         => 'required|string|max:100|unique:users,user_id',
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255|unique:users,email',
            'office'          => 'required|string|exists:tbl_ref_department,department_id',
            'password'        => 'required|string|min:8|confirmed',
            'captcha_answer'  => 'required|integer',
        ], [
            'user_id.required'   => 'User ID is required.',
            'user_id.unique'     => 'This User ID is already taken. Please choose another.',
            'name.required'      => 'Full name is required.',
            'email.required'     => 'Email address is required.',
            'email.email'        => 'Please enter a valid email address.',
            'email.unique'       => 'This email address is already registered.',
            'office.required'    => 'Please select your office / department.',
            'office.exists'      => 'Invalid office/department selected.',
            'password.required'  => 'Password is required.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput($request->except('password', 'password_confirmation', 'captcha_answer'))
                ->withErrors($validator)
                ->with('open_signup', true);
        }

        // Regenerate captcha after use
        Session::forget('signup_captcha_answer');

        // Create user — inactive by default, pending admin approval
        $newUser = User::create([
            'user_id'      => $request->user_id,
            'name'         => $request->name,
            'email'        => $request->email,
            'user_role_id' => 4,          // Default: User role
            'office'       => $request->office,
            'is_active'    => 'N',        // Pending admin activation
            'password'     => Hash::make($request->password),
        ]);

        // Assign default module permissions (Archive: CRU; all others: none)
        UserPermissionService::assignDefault($newUser);

        return redirect()->route('login')
            ->with('signup_success', 'Your account has been successfully registered! Please wait for an administrator to activate your account before you can log in.');
    }

    /**
     * Generate a new math captcha and store the answer in session.
     * Returns JSON with the question string.
     */
    public function refreshCaptcha()
    {
        [$question, $answer] = $this->generateCaptcha();
        Session::put('signup_captcha_answer', $answer);

        return response()->json(['question' => $question]);
    }

    /**
     * Generate a simple arithmetic captcha.
     * Returns [questionString, answer].
     */
    public static function generateCaptcha(): array
    {
        $a = rand(1, 15);
        $b = rand(1, 15);
        $ops = ['+', '-', '×'];
        $op = $ops[array_rand($ops)];

        switch ($op) {
            case '+':
                $answer = $a + $b;
                break;
            case '-':
                // Ensure non-negative result
                if ($a < $b) [$a, $b] = [$b, $a];
                $answer = $a - $b;
                break;
            case '×':
                $a = rand(1, 9);
                $b = rand(1, 9);
                $answer = $a * $b;
                break;
            default:
                $answer = $a + $b;
                $op = '+';
        }

        return ["{$a} {$op} {$b}", $answer];
    }
}
