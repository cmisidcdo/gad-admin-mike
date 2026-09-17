<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use \Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePassword extends Component
{
    public $id;
    public $currentPassword, $newPassword, $confirmPassword;

    public function render()
    {
        return view('livewire.change-password');
    }

    // CHANGE PASSWORD
    public function changePass($id)
    {
        $rules = [
            'currentPassword'       =>      'required',
            'newPassword'           =>      'required',
            'confirmPassword'       =>      'required|same:newPassword',
        ];

        $messages = [
            'currentPassword.required'          =>          'Current Password is required',
            'newPassword.required'              =>          'New Password is required',
            'confirmPassword.required'          =>          'Confirm Password is required',
            'confirmPassword.same'              =>          'Confirm Password does not match.'
        ];

        $this->validate($rules, $messages);

        $user = Auth::user(); // Fetch user information.

        // password_verify() will check if the inputted current password matches the current password in the database.
        if (password_verify($this->currentPassword, $user->password)) {
            $changePassword = User::findOrFail($id);
            $changePassword->update([
                'password'          =>          Hash::make($this->newPassword),
            ]);

            session()->flash('status', 'Password updated successfully.');
            $this->redirect('/change-password');
        } else {
            session()->flash('error', 'Current Password is incorrect.');
        }

        $this->reset('currentPassword', 'newPassword', 'confirmPassword');
    }
}
