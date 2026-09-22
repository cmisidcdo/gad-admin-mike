<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected $username = 'user_id';

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the login form, pre-generating a fresh math captcha question.
     */
    public function showLoginForm()
    {
        [$question, $answer] = SignupController::generateCaptcha();
        Session::put('signup_captcha_answer', $answer);

        return view('auth.login', ['captchaQuestion' => $question]);
    }

    //Custom login. Instead of using the email for auth, we used user_id. When adding custom, make sure the login.blade is configured as well.
    // protected function login(Request $request)
    // {
    //     $request->validate([
    //         'user_id' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     try {
    //         if (Auth::attempt(['user_id' => $request->user_id, 'password' => $request->password])) {
    //             # code...
    //             return redirect()->route('dashboard');
    //         } else {
    //             return redirect("login");
    //         }
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //     }
    // }

    // protected function login(Request $request)
    // {
    //     $request->validate([
    //         'user_id' => 'required|string',
    //         'password' => 'required|string',
    //     ], [
    //         'user_id.required' => 'The user ID field is required.',
    //         'password.required' => 'The password field is required.',
    //     ]);

    //     try {
    //         $credentials = ['user_id' => $request->user_id, 'password' => $request->password];

    //         if (Auth::attempt($credentials)) {
    //             return redirect()->route('dashboard');
    //         } else {
    //             $user = User::where('user_id', $request->user_id)->first();

    //             if ($user) {
    //                 return redirect("login")->with('error', 'Invalid password. Please try again.');
    //             } else {
    //                 return redirect("login")->with('error', 'Invalid account. Please check your credentials.');
    //             }
    //         }
    //     } catch (\Throwable $th) {
    //         // Handle exceptions if needed  
    //     }
    // }

    protected function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
            'password' => 'required|string',
        ], [
            'user_id.required' => 'The user ID field is required.',
            'password.required' => 'The password field is required.',
        ]);

        try {
            $credentials = ['user_id' => $request->user_id, 'password' => $request->password];

            $user = User::where('user_id', $request->user_id)->first();

            if ($user && $user->is_active == 'Y' && Auth::attempt($credentials)) {
                return redirect()->route('home');
            } else {
                if (!$user) {
                    return redirect("login")->with('error', 'Invalid account. Please check your credentials.');
                } elseif ($user->is_active == 'N') {
                    return redirect("login")->with('error', 'Account is not active. Please contact support.');
                } else {
                    return redirect("login")->with('error', 'Invalid password. Please try again.');
                }
            }
        } catch (\Throwable $th) {
            // Handle exceptions if needed
        }
    }
}
