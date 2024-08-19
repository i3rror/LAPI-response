<?php

use Illuminate\Http\Response as Res;

return [
    // Remove null values from array.
    'removeNullDataValues' => false,

    // Set null data value to null.
    'setNullEmptyData' => true,

    /**
     * Return validation error keys
     * e.g.:
     * true: [ "email" => "Must me unique." ]
     * false: [ "Email must me unique." ]
     */
    'returnValidationErrorsKeys' => true,

    // Success status codes.
    'apiSuccessCodes' => [
        Res::HTTP_OK,
        Res::HTTP_CREATED,
        Res::HTTP_ACCEPTED,
    ],

    // Exceptions status codes.
    'apiExceptionCodes' => [
        Res::HTTP_CONFLICT,
        Res::HTTP_UNPROCESSABLE_ENTITY,
        Res::HTTP_BAD_REQUEST,
        Res::HTTP_UNAUTHORIZED,
        Res::HTTP_FORBIDDEN,
    ],

    // Status codes.
    'statusCodes' => [
        Res::HTTP_CONTINUE,
        Res::HTTP_SWITCHING_PROTOCOLS,
        Res::HTTP_PROCESSING,
        Res::HTTP_EARLY_HINTS,
        Res::HTTP_OK,
        Res::HTTP_CREATED,
        Res::HTTP_ACCEPTED,
        Res::HTTP_NON_AUTHORITATIVE_INFORMATION,
        Res::HTTP_NO_CONTENT,
        Res::HTTP_RESET_CONTENT,
        Res::HTTP_PARTIAL_CONTENT,
        Res::HTTP_MULTI_STATUS,
        Res::HTTP_ALREADY_REPORTED,
        Res::HTTP_IM_USED,
        Res::HTTP_MULTIPLE_CHOICES,
        Res::HTTP_MOVED_PERMANENTLY,
        Res::HTTP_FOUND,
        Res::HTTP_SEE_OTHER,
        Res::HTTP_NOT_MODIFIED,
        Res::HTTP_USE_PROXY,
        Res::HTTP_RESERVED,
        Res::HTTP_TEMPORARY_REDIRECT,
        Res::HTTP_PERMANENTLY_REDIRECT,
        Res::HTTP_BAD_REQUEST,
        Res::HTTP_UNAUTHORIZED,
        Res::HTTP_PAYMENT_REQUIRED,
        Res::HTTP_FORBIDDEN,
        Res::HTTP_NOT_FOUND,
        Res::HTTP_METHOD_NOT_ALLOWED,
        Res::HTTP_NOT_ACCEPTABLE,
        Res::HTTP_PROXY_AUTHENTICATION_REQUIRED,
        Res::HTTP_REQUEST_TIMEOUT,
        Res::HTTP_CONFLICT,
        Res::HTTP_GONE,
        Res::HTTP_LENGTH_REQUIRED,
        Res::HTTP_PRECONDITION_FAILED,
        Res::HTTP_REQUEST_ENTITY_TOO_LARGE,
        Res::HTTP_REQUEST_URI_TOO_LONG,
        Res::HTTP_UNSUPPORTED_MEDIA_TYPE,
        Res::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE,
        Res::HTTP_EXPECTATION_FAILED,
        Res::HTTP_I_AM_A_TEAPOT,
        Res::HTTP_MISDIRECTED_REQUEST,
        Res::HTTP_UNPROCESSABLE_ENTITY,
        Res::HTTP_LOCKED,
        Res::HTTP_FAILED_DEPENDENCY,
        Res::HTTP_TOO_EARLY,
        Res::HTTP_UPGRADE_REQUIRED,
        Res::HTTP_PRECONDITION_REQUIRED,
        Res::HTTP_TOO_MANY_REQUESTS,
        Res::HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE,
        Res::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS,
        Res::HTTP_INTERNAL_SERVER_ERROR,
        Res::HTTP_NOT_IMPLEMENTED,
        Res::HTTP_BAD_GATEWAY,
        Res::HTTP_SERVICE_UNAVAILABLE,
        Res::HTTP_GATEWAY_TIMEOUT,
        Res::HTTP_VERSION_NOT_SUPPORTED,
        Res::HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL,
        Res::HTTP_INSUFFICIENT_STORAGE,
        Res::HTTP_LOOP_DETECTED,
        Res::HTTP_NOT_EXTENDED,
        Res::HTTP_NETWORK_AUTHENTICATION_REQUIRED,
    ],

    // Enable error codes
    'enableErrorCodes' => true,

    // Set error codes enum class
    'errorCodes' => \MA\LaravelApiResponse\Enums\ErrorCodesEnum::class,

    // Set error codes output either string or integer
    'errorCodesType' => 'string',

    // Return default error codes if error code is set to null
    'returnDefaultErrorCodes' => true,

    // Set error codes output default value for built-in functions
    'errorCodesDefaults' => [
        'apiNotFound' => 'RESOURCE_NOT_FOUND',
        'apiBadRequest' => 'BAD_REQUEST',
        'apiException' => 'SERVER_ERROR',
        'apiUnauthenticated' => 'UNAUTHORIZED_ACCESS',
        'apiForbidden' => 'FORBIDDEN',
        'apiValidate' => 'VALIDATION_FAILED',
    ],
];
