<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; 
use App\Exceptions\MyValidationException;
use App\Pum;

class PumShowRequest extends FormRequest
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
            //
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
        $pum = Pum::find($this->route('pum_id'));
        // pum validator
        $validator->after(function ($validator) use ($pum) {
            if (!$pum) {
                $validator->errors()->add('pum_id', 'PUM not found.');
            }
        });
    }
}
