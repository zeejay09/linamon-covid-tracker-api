<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; 
use App\Exceptions\MyValidationException;
use App\Barangay;

class BarangayDeleteRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new MyValidationException($validator);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'barangay_id' => 'required',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        // barangay validator
        $barangays_ids = $this->input(['barangay_id']);
        $validator->after(function ($validator) use ($barangays_ids) {
            $barangay_ids = array();
            foreach ($barangays_ids as $key) { //traversing the ids and return ids that doesn't exist
                $barangay_id = (integer)$key;

                $barangay = Barangay::find($barangay_id);

                if(!$barangay) {
                    $barangay_ids[] = $barangay_id;
                }
                
                if (!next($barangays_ids)) {
                    if(!empty(array_filter($barangay_ids))) {
                        if (count($barangay_ids) > 1) {
                            $validator->errors()->add('barangay_id', 'Barangay with id '.implode(',', $barangay_ids).' not found.');
                        } else {
                            $validator->errors()->add('barangay_id', 'Barangay not found.');
                        }
                    }
                }
            }
        });
    }
}
