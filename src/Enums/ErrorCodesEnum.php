<?php

namespace MA\LaravelApiResponse\Enums;

use Exception;

enum ErrorCodesEnum: int
{
    case INVALID_CREDENTIALS = 1000;
    case VALIDATION_FAILED = 1001;
    case UNAUTHORIZED_ACCESS = 1002;
    case RESOURCE_NOT_FOUND = 1003;
    case SERVER_ERROR = 1004;
    case BAD_REQUEST = 1005;
    case FORBIDDEN = 1006;
    case TIMEOUT_ERROR = 1007;
    case CONFLICT_ERROR = 1008;
    case RATE_LIMIT_EXCEEDED = 1009;
    case DATABASE_ERROR = 1010;
    case SERVICE_UNAVAILABLE = 1011;
    case INTERNAL_SERVER_ERROR = 1012;
    case CONFIGURATION_ERROR = 1013;
    case DEPLOYMENT_ERROR = 1014;
    case DEPENDENCY_FAILURE = 1015;
    case INSUFFICIENT_RESOURCES = 1016;
    case NETWORK_ERROR = 1017;
    case DATA_INTEGRITY_ERROR = 1018;
    case FILE_SYSTEM_ERROR = 1019;
    case UNSUPPORTED_OPERATION = 1020;
    case THROTTLING_ERROR = 1021;
    case SERIALIZATION_ERROR = 1022;
    case RESOURCE_LOCK_ERROR = 1023;
    case VERSION_MISMATCH_ERROR = 1024;
    case QUEUE_OVERFLOW_ERROR = 1025;


    /**
     * @param string|int $code
     * @return ErrorCodesEnum
     * @throws Exception
     */
    public static function getProperty(string|int $code): ErrorCodesEnum
    {
        if (is_numeric($code)) {
            $property = self::tryFrom($code)->name;
        } else {
            $property = ErrorCodesEnum::class . "::" . strtoupper($code);
        }

        if (defined($property))
            return constant($property);
        throw new Exception("Enum '$property' not found!");
    }
}