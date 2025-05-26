<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;

class AdminAuthController extends Controller
{
    /**
     * Admin Login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(message: $validator->errors()->first(), errors: $validator->errors());
        }

        $user = Admin::where('email', $request->email)->first();

        if(!$user){
            return $this->errorResponse(message: 'Invalid email or password');
        }

        if($user->is_blocked){
            return $this->errorResponse(message: 'Your account is blocked');
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse(message: 'Invalid email or password');
        }


        $token = $user->createToken('admin_token', ['admin'])->plainTextToken;

        $data = [
            'user' => $user->only(['name', 'email', 'phone', 'role']),
            'access_token' => $token,
        ];

        return $this->successResponse(message: 'Admin logged in successfully', data: $data);
    }

    /**
     * Admin Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(message: 'Successfully logged out');
    }

    public function me(Request $request)
    {
        return $this->successResponse(message: 'Admin details fetched successfully', data: $request->user());
    }
}
