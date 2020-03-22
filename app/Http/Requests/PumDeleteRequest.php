<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; 
use App\Exceptions\MyValidationException;
use App\Pum;

class PumDeleteRequest extends FormRequest
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
            'pum_id' => 'required',
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
        // pum validator
        $pums_ids = $this->input(['pum_id']);
        $validator->after(function ($validator) use ($pums_ids) {
            $pum_ids = array();
            foreach ($pums_ids as $key) { //traversing the ids and return ids that doesn't exist
                $pum_id = (integer)$key;

                $pum = Pum::find($pum_id);

                if(!$pum) {
                    $pum_ids[] = $pum_id;
                }
                
                if (!next($pums_ids)) {
                    if(!empty(array_filter($pum_ids))) {
                        if (count($pum_ids) > 1) {
                            $validator->errors()->add('pum_id', 'PUM with id '.implode(',', $pum_ids).' not found.');
                        } else {
                            $validator->errors()->add('pum_id', 'PUM not found.');
                        }
                    }
                }
            }
        });
    }
}
