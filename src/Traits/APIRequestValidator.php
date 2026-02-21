<?php

namespace MA\LaravelApiResponse\Traits;

use Illuminate\Http\Concerns\InteractsWithContentTypes;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\Validator;

trait APIRequestValidator
{
    use APIResponseTrait;
    use InteractsWithContentTypes;

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator|\Illuminate\Contracts\Validation\Validator $validator
     * @return JsonResponse
     *
     */
    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator)
    {
        $exception = $validator->getException();

        // if expects json
        if ($this->expectsJson()) {
            // Set errors
            $errors = config('response.returnValidationErrorsKeys', true) ?
                $validator->errors()->toArray() :
                $validator->errors()->all();
            if ((bool)config('response.returnDefaultErrorCodes', true)) {
                $errorCode = $this->getErrorCode(config('response.errorCodesDefaults.apiValidate', 'VALIDATION_FAILED'));
            } else {
                $errorCode = null;
            }

            return apiBadRequest($errors, $this->errorMessage ?? null, true, $errorCode);
        }

        throw new $exception($validator);
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\UnauthorizedException
     */
    protected function failedAuthorization()
    {
        // if expects json
        if ($this->expectsJson()) {
            return $this->apiForbidden();
        }

        throw new UnauthorizedException;
    }
}
