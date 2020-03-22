<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; 
use App\Exceptions\MyValidationException;
use App\Barangay;
use App\Role;

class UserRegisterRequest extends FormRequest
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
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',  
            'c_password' => 'required|same:password',
            'department' => 'required',
            'position' => 'required',
            'barangay_id' => 'required',
            'role_id' => 'required'
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
        $barangay = Barangay::find($this->input('barangay_id'));
        $validator->after(function ($validator) use ($barangay) {
            if (!$barangay) {
                $validator->errors()->add('barangay_id', 'Barangay not found.');
            }
        });

        // role validator
        $role_id = $this->request->get('role_id');
        $validator->after(function ($validator) use ($role_id) {
            $role = Role::find($role_id);

            if (!$role) {
                $validator->errors()->add('role_id', 'Role not found.');
            }
        });
    }
}
