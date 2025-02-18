<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AdminAuthController extends Controller
{
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
            'admin_username' => 'required_without:admin_email|string',
            'admin_email' => 'required_without:admin_username|string|email',
            'admin_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to login, please check your input data',
                'errors' => $validator->errors()
            ], 400);
        }

        // Cek Admin
        $admin = Admin::where('admin_username', $request->admin_username)
                      ->orWhere('admin_email', $request->admin_email)
                      ->first();

        if ($admin && password_verify($request->admin_password, $admin->admin_password)) {
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

    public function refresh()
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();
            return response()->json([
                'access_token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expired, please log in again'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token not provided'], 401);
        }
    }
    
}
