<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator;

class MyValidationException extends Exception
{
    protected $validator;

    protected $code = 422;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function render()
    {
        $customErrors = [];
        foreach ($this->validator->errors()->toArray() as $field => $message) {
            $customErrors[] = [
                'error' => $field,
                'message' => $message,
            ];
        }

        // return a json with desired format
        return response()->json([
            "success" => false,
            "errors" => $customErrors
        ], $this->code);
    }
}
