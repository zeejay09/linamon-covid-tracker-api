<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; 
use App\Exceptions\MyValidationException;
use App\Pui;

class PuiDeleteRequest extends FormRequest
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
            'pui_id' => 'required',
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
        // pui validator
        $puis_ids = $this->input(['pui_id']);
        $validator->after(function ($validator) use ($puis_ids) {
            $pui_ids = array();
            foreach ($puis_ids as $key) { //traversing the ids and return ids that doesn't exist
                $pui_id = (integer)$key;

                $pui = Pui::find($pui_id);

                if(!$pui) {
                    $pui_ids[] = $pui_id;
                }
                
                if (!next($puis_ids)) {
                    if(!empty(array_filter($pui_ids))) {
                        if (count($pui_ids) > 1) {
                            $validator->errors()->add('pui_id', 'PUI with id '.implode(',', $pui_ids).' not found.');
                        } else {
                            $validator->errors()->add('pui_id', 'PUI not found.');
                        }
                    }
                }
            }
        });
    }
}
