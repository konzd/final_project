<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\ResetPasswordModel;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminAuthController extends Controller
{
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user, please check your input',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password, 
            'role' => 'user'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User successfully created',
            'data' => $user
        ], 201);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admin_username' => 'required|string|unique:admin,admin_username',
            'admin_email' => 'required|string|email|unique:admin,admin_email',
            'admin_password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create an admin account, please check your input data',
                'errors' => $validator->errors()
            ], 400);
        }

        $admin = Admin::create([
            'admin_username' => $request->admin_username,
            'admin_email' => $request->admin_email,
            'admin_password' => $request->admin_password,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully created an admin account',
            'data' => $admin
        ], 201);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'token' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request. Please check your input.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $passwordReset = ResetPasswordModel::where('email', $request->email)
                                    ->where('token', $request->token)
                                    ->first();

        if (!$passwordReset) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token or email.',
            ], 400);
        }

        $user = User::where('email', $request->email)->first();
        $admin = Admin::where('admin_email', $request->email)->first();

        if (!$user && !$admin) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        if ($user) {
            $user->password = $request->new_password; 
            $user->save();
        }

        if ($admin) {
            $admin->admin_password = $request->new_password; 
            $admin->save();
        }

        $passwordReset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ], 200);
    }

    public function requestResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required_without:email|string',
            'email' => 'required_without:username|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request. Please check your input.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = User::where('username', $request->username)
                    ->orWhere('email', $request->email)
                    ->first();

        $admin = Admin::where('admin_username', $request->username)
                    ->orWhere('admin_email', $request->email)
                    ->first();

        if (!$user && !$admin) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $email = $user ? $user->email : $admin->admin_email;
        $resetToken = Str::random(40);

        ResetPasswordModel::updateOrCreate(
            ['email' => $email],
            ['token' => $resetToken]
        );

        Mail::raw("Your password reset token is: $resetToken", function ($message) use ($email) {
            $message->to($email)->subject('Password Reset Request');
        });

        return response()->json([
            'success' => true,
            'message' => 'Reset token has been sent to your email.',
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required_without:email|string',
            'email' => 'required_without:username|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to login, please check your input data',
                'errors' => $validator->errors()
            ], 400);
        }

        $admin = Admin::where('admin_username', $request->username)
                      ->orWhere('admin_email', $request->email)
                      ->first();

        if ($admin && $admin->admin_password === $request->password) {
            $token = Auth::guard('api')->login($admin);

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged in as admin',
                'data' => [
                    'role' => 'admin',
                    'username' => $admin->admin_username,
                    'email' => $admin->admin_email,
                ],
                'access_token' => $token
            ], 200);
        }

        $user = User::where('username', $request->username)
                    ->orWhere('email', $request->email)
                    ->first();

        if ($user && $user->password === $request->password) { 
            $token = Auth::guard('api')->login($user);

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged in as user',
                'data' => [
                    'role' => 'user',
                    'username' => $user->username,
                    'email' => $user->email,
                ],
                'access_token' => $token
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Wrong username/email or password',
        ], 400);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
            ]);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, there was an error in the internal server.',
                'errors' => $error->getMessage(),
            ], 500);
        }
    }

    public function me()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully fetched authenticated user',
            'data' => [
                'role' => $user->role ?? 'unknown',
                'username' => $user->username ?? $user->admin_username,
                'email' => $user->email ?? $user->admin_email,
            ]
        ], 200);
    }
}
