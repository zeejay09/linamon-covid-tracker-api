<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserLoginRequest;

class AuthController extends Controller
{
    const SUCCESS_STATUS = 200;
    const BAD_REQUEST_STATUS = 400;
    const UNAUTHORIZED_STATUS = 401;

    // Login User
    public function login(UserLoginRequest $request) {
        $validated = $request->validated(); //validation
        
        $input = $request->input();

        $input['email'] = $request->request->get('email');
        $input['password'] = $request->request->get('password');
        
        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password']])) {
            $user = Auth::user();
            $roles = $user->role->pluck('id')->toArray();

            if (!empty($roles)) { 
                $success['token'] = 'Bearer ' . $user->createToken('COVID')->accessToken; 
                return response()->json(['success' => $success, 'role' => $user->role], self::SUCCESS_STATUS); 
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Access Denied.'
                ], self::UNAUTHORIZED_STATUS);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied.'
            ], self::UNAUTHORIZED_STATUS);
        }
    }

    //Logout User
    public function logout() {
        $accessToken = Auth::user()->token();
        $accessToken->revoke();
    
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out.'
        ], self::SUCCESS_STATUS);
    }
}