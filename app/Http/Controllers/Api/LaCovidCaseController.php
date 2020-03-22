<?php

namespace App\Http\Controllers\Api;

use App\LaCovidCase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CovidCaseCreateRequest;
use App\Http\Requests\CovidCaseUpdateRequest;
use App\Http\Requests\CovidCaseShowRequest;
use App\Http\Requests\CovidCaseDeleteRequest;
use App\Http\Requests\BarangayShowRequest;

class LaCovidCaseController extends Controller
{
    const SUCCESS_STATUS = 200;
    const BAD_REQUEST_STATUS = 400;
    const UNAUTHORIZED_STATUS = 401;
    const INTERNAL_SERVER_STATUS = 500;

    // Show all covid cases
    public function showAllCovidCases(Request $request) {
        $covid_cases = LaCovidCase::all();

        foreach ($covid_cases as $covid_case) {
            $covid_case['barangay'] = $covid_case->barangay;
        }

        return response()->json([
            'success' => true,
            'data' => $covid_cases
        ], self::SUCCESS_STATUS);
    }

    // Show all covid cases by barangay
    public function showAllCovidCasesByBrgy(BarangayShowRequest $request, $barangay_id) {
        $validated = $request->validated(); // validation

        $covid_cases = LaCovidCase::where('barangay_id', $barangay_id)->get();

        foreach ($covid_cases as $covid_case) {
            $covid_case['barangay'] = $covid_case->barangay;
        }

        return response()->json([
            'success' => true,
            'data' => $covid_cases
        ], self::SUCCESS_STATUS);
    }

    // Show covid case via id
    public function showCovidCase(CovidCaseShowRequest $request, $covid_case_id) {
        $validated = $request->validated(); // validation

        $covid_case = LaCovidCase::find($covid_case_id);
        $covid_case['barangay'] = $covid_case->barangay;
 
        return response()->json([
            'success' => true,
            'data' => $covid_case
        ], self::SUCCESS_STATUS);
    }

    // Create covid case
    public function createCovidCase(CovidCaseCreateRequest $request) {
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
            $covid_case = LaCovidCase::create([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'alias' => ($request->filled('alias')) ? $input['alias'] : null,
                'barangay_id' => $input['barangay_id']
            ]);

            DB::commit();
    
            return response()->json([
                'success'=> true,
                'message'=> 'COVID case added successfully.'
            ], self::SUCCESS_STATUS); 
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success'=> false,
                'message'=> 'COVID case creation failed.'
            ], self::INTERNAL_SERVER_STATUS); 
        }
    }

    // Update covid case via id
    public function updateCovidCase(CovidCaseUpdateRequest $request, $covid_case_id) {
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
            $covid_case = LaCovidCase::find($covid_case_id);

            $covid_case->fill(array_filter($input))->save();

            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'COVID case updated successfully.'
            ], self::SUCCESS_STATUS);  
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'COVID case update failed.'
            ], self::INTERNAL_SERVER_STATUS);
        }
    }

    // Delete single/multiple covid case
    public function deleteCovidCase(CovidCaseDeleteRequest $request) {
        $validated = $request->validated(); // validation
        
        if ($request->filled('covid_case_id')) {
            $input['covid_case_id'] = $request->request->get('covid_case_id'); 
        }

        DB::beginTransaction();

        try {
            foreach ($input['covid_case_id'] as $key) {
                $covid_case_id = (integer)$key;
    
                LaCovidCase::destroy($covid_case_id);
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'COVID case deleted successfully.'
            ], self::SUCCESS_STATUS);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'COVID case deletion failed.'
            ], self::INTERNAL_SERVER_STATUS);
        }
    }
}
