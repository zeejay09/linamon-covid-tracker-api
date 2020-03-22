<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserShowRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserDeleteRequest;

class UserController extends Controller
{
    const SUCCESS_STATUS = 200;
    const BAD_REQUEST_STATUS = 400;
    const UNAUTHORIZED_STATUS = 401;
    const INTERNAL_SERVER_STATUS = 500;

    // Show all users
    public function showAllUsers(Request $request) {
        if (!Auth::user()->authorizeRoles(1)) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied.'
            ], self::UNAUTHORIZED_STATUS); 
        }

        $users = User::all()->except(Auth::user()->id);

        foreach ($users as $user) {
            $user['role'] = $user->role;
            $user['barangay'] = $user->barangay;
        }

        return response()->json([
            'success' => true,
            'data' => $users
        ], self::SUCCESS_STATUS);
    }

    // Create User
    public function createUser(UserRegisterRequest $request) {
        if (!Auth::user()->authorizeRoles(1)) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied.'
            ], self::UNAUTHORIZED_STATUS); 
        }

        $validated = $request->validated(); //validation
        
        $input = $request->input();

        $input['first_name'] = $request->request->get('first_name'); 
        $input['last_name'] = $request->request->get('last_name');
        $input['email'] = $request->request->get('email');
        $input['password'] = $request->request->get('password');
        $input['c_password'] = $request->request->get('c_password');
        $input['department'] = $request->request->get('department');
        $input['position'] = $request->request->get('position');
        $input['barangay_id'] = $request->request->get('barangay_id');
        $input['role_id'] = $request->request->get('role_id');

        DB::beginTransaction();

        try {
            $input['password'] = bcrypt($input['password']);
            
            $user = User::create([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'department' => $input['department'],
                'position' => $input['position'],
                'barangay_id' => $input['barangay_id'],
                'role_id' => $input['role_id']
            ]);

            DB::commit();
    
            return response()->json([
                'success'=> true,
                'message'=> 'User registered successfully.'
            ], self::SUCCESS_STATUS); 
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success'=> false,
                'message'=> 'User registration failed.'
            ], self::INTERNAL_SERVER_STATUS); 
        }
    }

    // Show user via id
    public function showUser(UserShowRequest $request, $user_id) {
        $validated = $request->validated(); //validation
        
        if (!Auth::user()->authorizeRoles(1)) {
            if (!Auth::user()->verifyUser($user_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access Denied.'
                ], self::UNAUTHORIZED_STATUS); 
            } 
        }
    
        $user = Auth::user()->find($user_id);

        $user['role'] = $user->role;
        $user['barangay'] = $user->barangay;
        $user = $user->toArray();
        
        return response()->json([
            'success' => true,
            'data' => $user
        ], self::SUCCESS_STATUS);
    }

    // Update user via id
    public function updateUser(UserUpdateRequest $request, $user_id) {
        $validated = $request->validated(); //validation

        if (!Auth::user()->authorizeRoles(1)) {
            if (!Auth::user()->verifyUser($user_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access Denied.'
                ], self::UNAUTHORIZED_STATUS); 
            } 
        }

        if (!$request->has('role_id')) {
            $input = $request->input();

            if ($request->filled('first_name')) {
                $input['first_name'] = $request->request->get('first_name');
            }
            if ($request->filled('last_name')) {
                $input['last_name'] = $request->request->get('last_name');
            }
            if ($request->filled('email')) {
                $input['email'] = $request->request->get('email');
            }
            if ($request->filled('password')) {
                $input['password'] = $request->request->get('password');
            }
            if($request->filled('department')) {
                $input['department'] = $request->request->get('department');
            }
            if($request->filled('position')) {
                $input['position'] = $request->request->get('position'); 
            }
            if($request->filled('barangay_id')) {
                $input['barangay_id'] = $request->request->get('barangay_id'); 
            }

            DB::beginTransaction();

            try {
                $user = Auth::user()->find($user_id);

                if (empty($input['password'])) {
                    $user->fill(array_filter($input))->save();
                } else {
                    $input['password'] = bcrypt($input['password']);
                    $user->fill(array_filter($input))->save();
                }

                DB::commit();
        
                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully.'
                ], self::SUCCESS_STATUS);  
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'User update failed.'
                ], self::INTERNAL_SERVER_STATUS);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Input. Extra input detected.'
            ], self::BAD_REQUEST_STATUS);
        }
    }

    // Delete single/multiple user(s)
    public function deleteUser(UserDeleteRequest $request) {
        if (!Auth::user()->authorizeRoles(1)) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied.'
            ], self::UNAUTHORIZED_STATUS); 
        }

        $validated = $request->validated(); //validation

        $input = $request->input();

        if ($request->filled('user_id')) {
            $input['user_id'] = $request->request->get('user_id'); 
        }

        DB::beginTransaction();

        try {
            foreach ($input['user_id'] as $key) {
                $user_id = (integer)$key;
    
                User::destroy($user_id);
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ], self::SUCCESS_STATUS);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'User deletion failed.'
            ], self::INTERNAL_SERVER_STATUS);
        }
    }
}
