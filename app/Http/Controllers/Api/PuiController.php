<?php

namespace App\Http\Controllers\Api;

use App\Pui;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PuiCreateRequest;
use App\Http\Requests\PuiUpdateRequest;
use App\Http\Requests\PuiShowRequest;
use App\Http\Requests\PuiDeleteRequest;
use App\Http\Requests\BarangayShowRequest;

class PuiController extends Controller
{
    const SUCCESS_STATUS = 200;
    const BAD_REQUEST_STATUS = 400;
    const UNAUTHORIZED_STATUS = 401;
    const INTERNAL_SERVER_STATUS = 500;

    // Show all puis
    public function showAllPui(Request $request) {
        $puis = Pui::all();

        foreach ($puis as $pui) {
            $pui['barangay'] = $pui->barangay;
        }

        return response()->json([
            'success' => true,
            'data' => $puis
        ], self::SUCCESS_STATUS);
    }

    // Show all puis by barangay
    public function showAllPuiByBrgy(BarangayShowRequest $request, $barangay_id) {
        $validated = $request->validated(); // validation

        $puis = Pui::where('barangay_id', $barangay_id)->get();

        foreach ($puis as $pui) {
            $pui['barangay'] = $pui->barangay;
        }

        return response()->json([
            'success' => true,
            'data' => $puis
        ], self::SUCCESS_STATUS);
    }

    // Show pui via id
    public function showPui(PuiShowRequest $request, $pui_id) {
        $validated = $request->validated(); // validation

        $pui = Pui::find($pui_id);
        $pui['barangay'] = $pui->barangay;
 
        return response()->json([
            'success' => true,
            'data' => $pui
        ], self::SUCCESS_STATUS);
    }

    // Create pui
    public function createPui(PuiCreateRequest $request) {
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
            $pui = Pui::create([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'alias' => ($request->filled('alias')) ? $input['alias'] : null,
                'barangay_id' => $input['barangay_id']
            ]);

            DB::commit();
    
            return response()->json([
                'success'=> true,
                'message'=> 'PUI added successfully.'
            ], self::SUCCESS_STATUS); 
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success'=> false,
                'message'=> 'PUI creation failed.'
            ], self::INTERNAL_SERVER_STATUS); 
        }
    }

    // Update pui via id
    public function updatePui(PuiUpdateRequest $request, $pui_id) {
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
            $pui = Pui::find($pui_id);

            $pui->fill(array_filter($input))->save();

            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'PUI updated successfully.'
            ], self::SUCCESS_STATUS);  
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'PUI update failed.'
            ], self::INTERNAL_SERVER_STATUS);
        }
    }

    // Delete single/multiple pui
    public function deletePui(PuiDeleteRequest $request) {
        $validated = $request->validated(); // validation
        
        if ($request->filled('pui_id')) {
            $input['pui_id'] = $request->request->get('pui_id'); 
        }

        DB::beginTransaction();

        try {
            foreach ($input['pui_id'] as $key) {
                $pui_id = (integer)$key;
    
                Pui::destroy($pui_id);
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'PUI deleted successfully.'
            ], self::SUCCESS_STATUS);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'PUI deletion failed.'
            ], self::INTERNAL_SERVER_STATUS);
        }
    }
}
