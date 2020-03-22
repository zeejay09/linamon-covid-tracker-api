<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; 
use App\Exceptions\MyValidationException;
use App\Barangay;

class BarangayRequest extends FormRequest
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
            'brgy_name' => 'required|unique:barangay',
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
        if ($this->route('barangay_id')) {
            $barangay = Barangay::find($this->route('barangay_id'));
            // barangay validator
            $validator->after(function ($validator) use ($barangay) {
                if (!$barangay) {
                    $validator->errors()->add('barangay_id','Barangay not found.');
                }
            });
        }
    }
}
