<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; 
use App\Exceptions\MyValidationException;
use App\LaCovidCase;

class CovidCaseDeleteRequest extends FormRequest
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
            'covid_case_id' => 'required',
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
        // covid case validator
        $covid_cases_ids = $this->input(['covid_case_id']);
        $validator->after(function ($validator) use ($covid_cases_ids) {
            $covid_case_ids = array();
            foreach ($covid_cases_ids as $key) { //traversing the ids and return ids that doesn't exist
                $covid_case_id = (integer)$key;

                $covid_case = LaCovidCase::find($covid_case_id);

                if(!$covid_case) {
                    $covid_case_ids[] = $covid_case_id;
                }
                
                if (!next($covid_cases_ids)) {
                    if(!empty(array_filter($covid_case_ids))) {
                        if (count($covid_case_ids) > 1) {
                            $validator->errors()->add('covid_case_id', 'Persons with id '.implode(',', $covid_case_ids).' not found.');
                        } else {
                            $validator->errors()->add('covid_case_id', 'Person not found.');
                        }
                    }
                }
            }
        });
    }
}
