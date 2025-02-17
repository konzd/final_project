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
            'password' => bcrypt($request->password), // Gunakan bcrypt
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
            'admin_username' => 'required|string|unique:admins,admin_username',
            'admin_email' => 'required|string|email|unique:admins,admin_email',
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
            'admin_password' => bcrypt($request->admin_password), 
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully created an admin account',
            'data' => $admin
        ], 201);
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

        // Cek Admin
        $admin = Admin::where('admin_username', $request->username)
                      ->orWhere('admin_email', $request->email)
                      ->first();

        if ($admin && password_verify($request->password, $admin->admin_password)) {
            $token = JWTAuth::fromUser($admin);

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

        // Cek User
        $user = User::where('username', $request->username)
                    ->orWhere('email', $request->email)
                    ->first();

        if ($user && password_verify($request->password, $user->password)) {
            $token = JWTAuth::fromUser($user);

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
        ], 401);
    }

    public function logout()
    {
        try {
            Auth::guard('api')->logout();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to log out.',
                'error' => $e->getMessage(),
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
