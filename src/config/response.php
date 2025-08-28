<?php

use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

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
        HttpFoundationResponse::HTTP_OK,
        HttpFoundationResponse::HTTP_CREATED,
        HttpFoundationResponse::HTTP_ACCEPTED,
    ],

    // Exceptions status codes.
    'apiExceptionCodes' => [
        HttpFoundationResponse::HTTP_CONFLICT,
        HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY,
        HttpFoundationResponse::HTTP_BAD_REQUEST,
        HttpFoundationResponse::HTTP_UNAUTHORIZED,
        HttpFoundationResponse::HTTP_FORBIDDEN,
    ],

    // Status codes.
    'statusCodes' => [
        HttpFoundationResponse::HTTP_CONTINUE,
        HttpFoundationResponse::HTTP_SWITCHING_PROTOCOLS,
        HttpFoundationResponse::HTTP_PROCESSING,
        HttpFoundationResponse::HTTP_EARLY_HINTS,
        HttpFoundationResponse::HTTP_OK,
        HttpFoundationResponse::HTTP_CREATED,
        HttpFoundationResponse::HTTP_ACCEPTED,
        HttpFoundationResponse::HTTP_NON_AUTHORITATIVE_INFORMATION,
        HttpFoundationResponse::HTTP_NO_CONTENT,
        HttpFoundationResponse::HTTP_RESET_CONTENT,
        HttpFoundationResponse::HTTP_PARTIAL_CONTENT,
        HttpFoundationResponse::HTTP_MULTI_STATUS,
        HttpFoundationResponse::HTTP_ALREADY_REPORTED,
        HttpFoundationResponse::HTTP_IM_USED,
        HttpFoundationResponse::HTTP_MULTIPLE_CHOICES,
        HttpFoundationResponse::HTTP_MOVED_PERMANENTLY,
        HttpFoundationResponse::HTTP_FOUND,
        HttpFoundationResponse::HTTP_SEE_OTHER,
        HttpFoundationResponse::HTTP_NOT_MODIFIED,
        HttpFoundationResponse::HTTP_USE_PROXY,
        HttpFoundationResponse::HTTP_RESERVED,
        HttpFoundationResponse::HTTP_TEMPORARY_REDIRECT,
        HttpFoundationResponse::HTTP_PERMANENTLY_REDIRECT,
        HttpFoundationResponse::HTTP_BAD_REQUEST,
        HttpFoundationResponse::HTTP_UNAUTHORIZED,
        HttpFoundationResponse::HTTP_PAYMENT_REQUIRED,
        HttpFoundationResponse::HTTP_FORBIDDEN,
        HttpFoundationResponse::HTTP_NOT_FOUND,
        HttpFoundationResponse::HTTP_METHOD_NOT_ALLOWED,
        HttpFoundationResponse::HTTP_NOT_ACCEPTABLE,
        HttpFoundationResponse::HTTP_PROXY_AUTHENTICATION_REQUIRED,
        HttpFoundationResponse::HTTP_REQUEST_TIMEOUT,
        HttpFoundationResponse::HTTP_CONFLICT,
        HttpFoundationResponse::HTTP_GONE,
        HttpFoundationResponse::HTTP_LENGTH_REQUIRED,
        HttpFoundationResponse::HTTP_PRECONDITION_FAILED,
        HttpFoundationResponse::HTTP_REQUEST_ENTITY_TOO_LARGE,
        HttpFoundationResponse::HTTP_REQUEST_URI_TOO_LONG,
        HttpFoundationResponse::HTTP_UNSUPPORTED_MEDIA_TYPE,
        HttpFoundationResponse::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE,
        HttpFoundationResponse::HTTP_EXPECTATION_FAILED,
        HttpFoundationResponse::HTTP_I_AM_A_TEAPOT,
        HttpFoundationResponse::HTTP_MISDIRECTED_REQUEST,
        HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY,
        HttpFoundationResponse::HTTP_LOCKED,
        HttpFoundationResponse::HTTP_FAILED_DEPENDENCY,
        HttpFoundationResponse::HTTP_TOO_EARLY,
        HttpFoundationResponse::HTTP_UPGRADE_REQUIRED,
        HttpFoundationResponse::HTTP_PRECONDITION_REQUIRED,
        HttpFoundationResponse::HTTP_TOO_MANY_REQUESTS,
        HttpFoundationResponse::HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE,
        HttpFoundationResponse::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS,
        HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR,
        HttpFoundationResponse::HTTP_NOT_IMPLEMENTED,
        HttpFoundationResponse::HTTP_BAD_GATEWAY,
        HttpFoundationResponse::HTTP_SERVICE_UNAVAILABLE,
        HttpFoundationResponse::HTTP_GATEWAY_TIMEOUT,
        HttpFoundationResponse::HTTP_VERSION_NOT_SUPPORTED,
        HttpFoundationResponse::HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL,
        HttpFoundationResponse::HTTP_INSUFFICIENT_STORAGE,
        HttpFoundationResponse::HTTP_LOOP_DETECTED,
        HttpFoundationResponse::HTTP_NOT_EXTENDED,
        HttpFoundationResponse::HTTP_NETWORK_AUTHENTICATION_REQUIRED,
    ],

    // Enable error codes
    'enableErrorCodes' => true,

    // Set error codes enum class
    'errorCodes' => \MA\LaravelApiResponse\Enums\ErrorCodesEnum::class,

    // Set error codes output either string or integer
    'errorCodesType' => 'string',

    // Return default error codes if error code is set to null
    'returnDefaultErrorCodes' => true,

    // Hide next, previous links in meta
    'hideMetaPaginationLinks' => true,

    /**
     * Render client errors status code in statusCode property.
     * if true it will be written, if false it will be 500
     */
    'renderClientErrorsStatusCode' => true,

    // Set error codes output default value for built-in functions
    'errorCodesDefaults' => [
        'apiNotFound' => 'RESOURCE_NOT_FOUND',
        'apiBadRequest' => 'BAD_REQUEST',
        'apiException' => 'SERVER_ERROR',
        'apiUnauthenticated' => 'UNAUTHORIZED_ACCESS',
        'apiForbidden' => 'FORBIDDEN',
        'apiValidate' => 'VALIDATION_FAILED',
    ],

    /*
     |-----------------------------------------------------------------------------------------------------
     | Custom Error Handlers
     |-----------------------------------------------------------------------------------------------------
     | /**
     | * Human-readable error message.
     | * - If omitted, a default message may be used.
     | * - Can be a plain string, or a callable that builds a message from the exception.
     | * - Callable signature: fn(Throwable $e): string
     | *\/
     | message?: string|callable(\Throwable):string,
     |
     | /**
     | * Application-specific error code.
     | * - String or integer (enum-to-string/int also acceptable).
     | * - Nullable; if null and "returnDefaultErrorCodes" is true, a default may be used.
     | *\/
     | code?: string|int|null,
     |
     | /**
     | * HTTP status code to return.
     | * - Use Symfony Response constants (preferred) or any valid HTTP status code integer.
     | * - e.g. \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND (404)
     | *\/
     | statusCode?: int,
     |
     | /**
     | * Validation/diagnostic errors to expose to the client.
     | * Accepts either:
     | * - A flat list of error messages:
     | *     ["Invalid email", "Password too short"]
     | * - A key-value map of named errors:
     | *     ["email" => "Invalid email", "password" => ["Too short", "Must contain a number"]]
     | * - Mixed lists + keyed entries:
     | *     [0 => "Generic error", "field" => "Specific error"]
     | * - Can be null/omitted to exclude.
     | *\/
     | errors?: list<string>|array<string, string|list<string>>|null,
     |
     | /**
     | * Extra payload to include under the "extra" property.
     | * - Arbitrary associative data, e.g. ["invalid_argument" => true]
     | * - May include its own "errors" which can be the same shapes as above.
     | * - If null/empty after processing, "extra" should be removed from the response.
     | *\/
     | extra?: array<string, mixed>|null,
     |
     | Examples:
     |
     | - Simple Not Found:
     |   [
     |     \Illuminate\Support\ItemNotFoundException::class => [
     |       'message' => 'Resource not found!',
     |       'code' => 'RESOURCE_NOT_FOUND',
     |       'statusCode' => \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
     |     ],
     |   ]
     |
     | - Invalid Argument with mixed errors and extra:
     |   [
     |     \InvalidArgumentException::class => [
     |       'message' => 'Invalid argument!',
     |       'code' => 'INVALID_ARGUMENT',
     |       'statusCode' => \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST,
     |       'errors' => [
     |         0 => 'Invalid Argument!',
     |         'test_error' => 'test error message',
     |       ],
     |       'extra' => [
     |         'invalid_argument' => true,
     |       ],
     |     ],
     |   ]
     |
     | - Dynamic message and report decision:
     |   [
     |     \RuntimeException::class => [
     |       'message' => static fn(\Throwable $e) => 'Runtime error: ' . $e->getMessage(),
     |       'statusCode' => \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR,
     |     ],
     |   ]
     | - PLEASE NOTE THAT THESE ARE EXAMPLES ONLY.
     | - THEY ARE NOT INTENDED TO BE USED DIRECTLY IN YOUR CODE THE WAY IT IS.
     | - THEY ARE JUST HERE TO SHOW WHAT THE CONFIGURATION MAY LOOK LIKE.
     | - ALSO Illuminate\Http\Exceptions\HttpResponseException IS NOT SUPPORTED AS UT SHOULD BE ESCAPED.
     |
     */
    'customErrorHandlers' => [
        Illuminate\Support\ItemNotFoundException::class => [
            'message' => 'Resource not found!',
            'code' => 'RESOURCE_NOT_FOUND',
            'statusCode' => HttpFoundationResponse::HTTP_NOT_FOUND,
        ],
    ]
];
