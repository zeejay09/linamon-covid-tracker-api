<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; 
use App\Exceptions\MyValidationException;
use App\Role; 

class RoleDeleteRequest extends FormRequest
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
            'role_id' => 'required',
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
        // role validator
        $roles_ids = $this->input(['role_id']);
        $validator->after(function ($validator) use ($roles_ids) {
            $role_ids = array();
            foreach ($roles_ids as $key) { //traversing the ids and return ids that doesn't exist
                $role_id = (integer)$key;

                $role = Role::find($role_id);

                if(!$role) {
                    $role_ids[] = $role_id;
                }
                
                if (!next($roles_ids)) {
                    if(!empty(array_filter($role_ids))) {
                        if (count($role_ids) > 1) {
                            $validator->errors()->add('role_id', 'Role with id '.implode(',', $role_ids).' not found.');
                        } else {
                            $validator->errors()->add('role_id', 'Role not found.');
                        }
                    }
                }
            }
        });
    }
}
