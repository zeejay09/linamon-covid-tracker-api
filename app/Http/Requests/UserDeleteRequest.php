<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; 
use App\Exceptions\MyValidationException;
use App\User;

class UserDeleteRequest extends FormRequest
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
            'user_id' => 'required',
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
        // user validator
        $users_ids = $this->input(['user_id']);
        $validator->after(function ($validator) use ($users_ids) {
           $user_ids = array();
           foreach ($users_ids as $key) { //traversing the ids and return ids that doesn't exist
               $user_id = (integer)$key;

               $user = User::find($user_id);

               if(!$user) {
                   $user_ids[] = $user_id;
               }
               
               if (!next($users_ids)) {
                   if(!empty(array_filter($user_ids))) {
                       if (count($user_ids) > 1) {
                            $validator->errors()->add('user_id', 'User with id '.implode(',', $user_ids).' not found');
                       } else {
                            $validator->errors()->add('user_id', 'User not found.');
                       }
                   }
               }
           }
        });
    }
}
