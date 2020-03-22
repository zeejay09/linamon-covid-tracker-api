<?php

namespace App\Http\Controllers\Api;

use App\Barangay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BarangayRequest;
use App\Http\Requests\BarangayShowRequest;
use App\Http\Requests\BarangayDeleteRequest;

class BarangayController extends Controller
{
    const SUCCESS_STATUS = 200;
    const BAD_REQUEST_STATUS = 400;
    const UNAUTHORIZED_STATUS = 401;
    const INTERNAL_SERVER_STATUS = 500;

    // Show all barangays
    public function showAllBrgy(Request $request) {
        $barangays = Barangay::all();

        return response()->json([
            'success' => true,
            'data' => $barangays
        ], self::SUCCESS_STATUS);
    }

    // Show barangay via id
    public function showBrgy(BarangayShowRequest $request, $barangay_id) {
        $validated = $request->validated(); // validation

        $barangay = Barangay::find($barangay_id);
 
        return response()->json([
            'success' => true,
            'data' => $barangay
        ], self::SUCCESS_STATUS);
    }

    // Create barangay
    public function createBrgy(BarangayRequest $request) {
        if (!Auth::user()->authorizeRoles(1)) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied.'
            ], self::UNAUTHORIZED_STATUS); 
        }

        if ($request->filled('brgy_name')) {
            $validated = $request->validated(); // validation

            $input = $request->input();

            $input['brgy_name'] = $request->request->get('brgy_name');
    
            $barangay = new Barangay();
            $barangay->brgy_name = $input['brgy_name'];
    
            if ($barangay->save()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Barangay created successfully.',
                    'data' => $role->toArray()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Barangay creation failed.'
                ], self::INTERNAL_SERVER_STATUS);
            }
        }
    }

    // Update barangay via id
    public function updateBrgy(BarangayRequest $request, $barangay_id) {
        if (!Auth::user()->authorizeRoles(1)) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied.'
            ], self::UNAUTHORIZED_STATUS); 
        }

        $validated = $request->validated(); // validation
    
        $input = $request->input();

        $input['brgy_name'] = $request->request->get('brgy_name');

        $barangay = Barangay::find($barangay_id);

        $updated = $barangay->fill(array_filter($input))->save();

        if ($updated) {
            return response()->json([
                'success' => true,
                'message'=> 'Barangay updated successfully.'
            ], self::SUCCESS_STATUS);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Barangay update failed.'
            ], self::INTERNAL_SERVER_STATUS);
        }
    }

    // Delete single/multiple barangays
    public function deleteBrgy(BarangayDeleteRequest $request) {
        if (!Auth::user()->authorizeRoles(1)) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied.'
            ], self::UNAUTHORIZED_STATUS); 
        }

        $validated = $request->validated(); // validation
        
        if ($request->filled('barangay_id')) {
            $input['barangay_id'] = $request->request->get('barangay_id');
        }

        foreach ($input['barangay_id'] as $key) {
            $barangay_id = (integer)$key;

            $barangay = Barangay::find($barangay_id);

            //check if there are relation that depends on this record
            $isUserExist = $barangay->user()->exists();
            $isPuiExist = $barangay->pui()->exists();
            $isPumExist = $barangay->pum()->exists();
            $isCovidExist = $barangay->covidcase()->exists();

            if ($isUserExist || $isPuiExist || $isPumExist || $isCovidExist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barangay deletion failed. There are records that depends on this.'
                ], self::BAD_REQUEST_STATUS);
            }
        }

        if (!Barangay::destroy($input['barangay_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Barangay deletion failed.'
            ], self::INTERNAL_SERVER_STATUS);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Barangay deleted successfully.'
            ], self::SUCCESS_STATUS);
        }
    }
}
