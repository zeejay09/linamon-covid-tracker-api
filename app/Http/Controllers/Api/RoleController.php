<?php

namespace App\Http\Controllers\Api;

use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RoleShowRequest;
use App\Http\Requests\RoleRequest;
use App\Http\Requests\RoleDeleteRequest;

class RoleController extends Controller
{
    const SUCCESS_STATUS = 200;
    const BAD_REQUEST_STATUS = 400;
    const UNAUTHORIZED_STATUS = 401;
    const INTERNAL_SERVER_STATUS = 500;

    // Show all roles
    public function showAllRoles(Request $request) {
        if (!Auth::user()->authorizeRoles(1)) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied.'
            ], self::UNAUTHORIZED_STATUS); 
        }
        
        $roles = Role::all();

        return response()->json([
            'success' => true,
            'data' => $roles
        ], self::SUCCESS_STATUS);
    }

    // Show role via id
    public function showRole(RoleShowRequest $request, $role_id) {
        if (!Auth::user()->authorizeRoles(1)) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied.'
            ], self::UNAUTHORIZED_STATUS); 
        }

        $validated = $request->validated(); // validation

        $role = Role::find($role_id);
 
        return response()->json([
            'success' => true,
            'data' => $role->toArray()
        ], self::SUCCESS_STATUS);
    }

    // Create role
    public function createRole(RoleRequest $request) {
        if (!Auth::user()->authorizeRoles(1)) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied.'
            ], self::UNAUTHORIZED_STATUS); 
        }

        if ($request->filled('title')) {
            $validated = $request->validated(); // validation

            $input = $request->input();

            $input['title'] = $request->request->get('title');
    
            $role = new Role();
            $role->title = $input['title'];
    
            if ($role->save()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Role created successfully.',
                    'data' => $role->toArray()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Role creation failed.'
                ], self::INTERNAL_SERVER_STATUS);
            }
        }
    }

    // Update role via role id
    public function updateRole(RoleRequest $request, $role_id) {
        if (!Auth::user()->authorizeRoles(1)) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied.'
            ], self::UNAUTHORIZED_STATUS); 
        }

        $validated = $request->validated(); // validation
    
        $input = $request->input();

        $input['title'] = $request->request->get('title');

        $role = Role::find($role_id);

        $updated = $role->fill(array_filter($input))->save();

        if ($updated) {
            return response()->json([
                'success' => true,
                'message'=> 'Role updated successfully.'
            ], self::SUCCESS_STATUS);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Role update failed.'
            ], self::INTERNAL_SERVER_STATUS);
        }
    }

    // Delete single/multiple roles
    public function deleteRole(RoleDeleteRequest $request) {
        if (!Auth::user()->authorizeRoles(1)) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied.'
            ], self::UNAUTHORIZED_STATUS); 
        }

        $validated = $request->validated(); // validation
        
        if ($request->filled('role_id')) {
            $input['role_id'] = $request->request->get('role_id');
        }

        foreach ($input['role_id'] as $key) {
            $role_id = (integer)$key;

            $role = Role::find($role_id);

            //check if there are relation that depends on this record
            $isExist = $role->user()->exists();

            if ($isExist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role deletion failed. There are records that depends on this.'
                ], self::BAD_REQUEST_STATUS);
            }
        }

        if (!Role::destroy($input['role_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Role deletion failed.'
            ], self::INTERNAL_SERVER_STATUS);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully.'
            ], self::SUCCESS_STATUS);
        }
    }
}
