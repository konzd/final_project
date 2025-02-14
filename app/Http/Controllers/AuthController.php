<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AuthController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admin,admin_email',
        ]);

        $status = Password::broker('admins')->sendResetLink(
            ['admin_email' => $request->email]
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent successfully!'])
            : response()->json(['message' => 'Unable to send reset link'], 400);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admin,admin_email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($admin, $password) {
                $admin->admin_password = Hash::make($password);
                $admin->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password has been reset successfully'], 200)
            : response()->json(['message' => 'Invalid token'], 400);
    }
}
