<?php

namespace App\Http\Controllers\Api;

use App\Pum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PumCreateRequest;
use App\Http\Requests\PumUpdateRequest;
use App\Http\Requests\PumShowRequest;
use App\Http\Requests\PumDeleteRequest;
use App\Http\Requests\BarangayShowRequest;

class PumController extends Controller
{
    const SUCCESS_STATUS = 200;
    const BAD_REQUEST_STATUS = 400;
    const UNAUTHORIZED_STATUS = 401;
    const INTERNAL_SERVER_STATUS = 500;

    // Show all pums
    public function showAllPum(Request $request) {
        $pums = Pum::all();

        foreach ($pums as $pum) {
            $pum['barangay'] = $pum->barangay;
        }

        return response()->json([
            'success' => true,
            'data' => $pums
        ], self::SUCCESS_STATUS);
    }

    // Show all pums by barangay
    public function showAllPumByBrgy(BarangayShowRequest $request, $barangay_id) {
        $validated = $request->validated(); // validation

        $pums = Pum::where('barangay_id', $barangay_id)->get();

        foreach ($pums as $pum) {
            $pum['barangay'] = $pum->barangay;
        }

        return response()->json([
            'success' => true,
            'data' => $pums
        ], self::SUCCESS_STATUS);
    }

    // Show pum via id
    public function showPum(PumShowRequest $request, $pum_id) {
        $validated = $request->validated(); // validation

        $pum = Pum::find($pum_id);
        $pum['barangay'] = $pum->barangay;
 
        return response()->json([
            'success' => true,
            'data' => $pum
        ], self::SUCCESS_STATUS);
    }

    // Create pum
    public function createPum(PumCreateRequest $request) {
        $validated = $request->validated(); // validation

        $input = $request->input();

        $input['first_name'] = $request->request->get('first_name');
        $input['last_name'] = $request->request->get('last_name');
        if ($request->filled('alias')) {
            $input['alias'] = $request->request->get('alias');
        }
        $input['barangay_id'] = $request->request->get('barangay_id');

        DB::beginTransaction();

        try {
            $pum = Pum::create([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'alias' => ($request->filled('alias')) ? $input['alias'] : null,
                'barangay_id' => $input['barangay_id']
            ]);

            DB::commit();
    
            return response()->json([
                'success'=> true,
                'message'=> 'PUM added successfully.'
            ], self::SUCCESS_STATUS); 
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success'=> false,
                'message'=> 'PUM creation failed.'
            ], self::INTERNAL_SERVER_STATUS); 
        }
    }

    // Update pum via id
    public function updatePum(PumUpdateRequest $request, $pum_id) {
        $validated = $request->validated(); // validation
    
        $input = $request->input();

        if ($request->filled('first_name')) {
            $input['first_name'] = $request->request->get('first_name');
        }
        if ($request->filled('last_name')) {
            $input['last_name'] = $request->request->get('last_name');
        }
        if ($request->filled('alias')) {
            $input['alias'] = $request->request->get('alias');
        }
        if ($request->filled('barangay_id')) {
            $input['barangay_id'] = $request->request->get('barangay_id');
        }

        DB::beginTransaction();

        try {
            $pum = Pum::find($pum_id);

            $pum->fill(array_filter($input))->save();

            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'PUM updated successfully.'
            ], self::SUCCESS_STATUS);  
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'PUM update failed.'
            ], self::INTERNAL_SERVER_STATUS);
        }
    }

    // Delete single/multiple pum
    public function deletePum(PumDeleteRequest $request) {
        $validated = $request->validated(); // validation
        
        if ($request->filled('pum_id')) {
            $input['pum_id'] = $request->request->get('pum_id'); 
        }

        DB::beginTransaction();

        try {
            foreach ($input['pum_id'] as $key) {
                $pum_id = (integer)$key;
    
                Pum::destroy($pum_id);
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'PUM deleted successfully.'
            ], self::SUCCESS_STATUS);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'PUM deletion failed.'
            ], self::INTERNAL_SERVER_STATUS);
        }
    }
}
