# Laravel API Response

[![Latest Version](https://img.shields.io/github/v/release/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/releases)
[![GitHub repo size](https://img.shields.io/github/repo-size/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/releases)
[![GitHub](https://img.shields.io/github/license/i3rror/LAPI-response)](https://img.shields.io/github/license/i3rror/LAPI-response)
[![Packagist Downloads](https://img.shields.io/packagist/dt/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/releases)

This package provides comprehensive functionality for handling and returning all types of API responses in Laravel applications. It offers consistent response formatting, error handling, and pagination support.

## Installation and Setup

### Step 1: Install via Composer
```bash
$ composer require i3rror/LAPI-response
```

### Step 2: Register Service Provider
Include the service provider in your `config/app.php` or in `bootstrap/providers.php` if you're using laravel 11:
```php
MA\LaravelApiResponse\Providers\APIResponseProvider::class
```

### Step 3: Publish Configuration
Run the following command to publish the package configuration:
```bash
$ php artisan vendor:publish --provider="MA\LaravelApiResponse\Providers\APIResponseProvider" --tag="lapi-response-config"
```

## Basic Implementation

To utilize this package, you'll need to use the APIResponseTrait in your controllers:

```php
use APIResponseTrait;
```

You have two implementation options:

1. **Global Implementation**: Add the trait to `App\Http\Controllers\Controller.php` to make it available across all controllers
2. **Local Implementation**: Add the trait only to specific controllers where API responses are needed

## Usage Examples

### Basic Response Example

```php
use MA\LaravelApiResponse\Traits\APIResponseTrait;

class TestController extends Controller
{
    use APIResponseTrait;
    
    public function index()
    {
        return $this->apiResponse([
            'message' => 'Test Message',
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Test Name',
                ],
                [
                    'id' => 2,
                    'name' => 'Test Name 2',
                ],
                [
                    'id' => 3,
                    'name' => 'Test Name 3',
                ],
            ],
            'extra' => [
                'field1' => 'Field 1',
                'field2' => 'Field 2'
            ],
        ]);
    }
}
```

Expected response:
```json
{
  "status": true,
  "statusCode": 200,
  "timestamp": 1662070087,
  "message": "Test Message",
  "data": [
    {
      "id": 1,
      "name": "Test Name"
    },
    {
      "id": 2,
      "name": "Test Name 2"
    },
    {
      "id": 3,
      "name": "Test Name 3"
    }
  ],
  "field1": "Field 1",
  "field2": "Field 2"
}
```

### Simplified Parameter Usage

You can use short parameter values in two ways:
- String parameters are set as messages
- Array parameters are set as data

#### Message Example:
```php
return $this->apiOk("test message");

// Or alternatively
return $this->apiResponse("test message");
```

Response:
```json
{
  "status": true,
  "statusCode": 200,
  "timestamp": 1662104853,
  "message": "OK",
  "data": "test message"
}
```

#### Data Example:
```php
return $this->apiOk([
    [
        'id' => 1,
        'name' => 'Test Name',
    ],
    [
        'id' => 2,
        'name' => 'Test Name 2',
    ],
    [
        'id' => 3,
        'name' => 'Test Name 3',
    ],
]);

// Or alternatively
return $this->apiResponse([
    [
        'id' => 1,
        'name' => 'Test Name',
    ],
    [
        'id' => 2,
        'name' => 'Test Name 2',
    ],
    [
        'id' => 3,
        'name' => 'Test Name 3',
    ],
]);
```

Response:
```json
{
  "status": true,
  "statusCode": 200,
  "timestamp": 1662105038,
  "message": "OK",
  "data": [
    {
      "id": 1,
      "name": "Test Name"
    },
    {
      "id": 2,
      "name": "Test Name 2"
    },
    {
      "id": 3,
      "name": "Test Name 3"
    }
  ]
}
```

### API Stream Response
The stream response feature requires a Generator class as the first parameter:
- Generator is required
- Message is optional
- Status code is optional

```php
// Return api stream response
public function index()
{
    // Message and status code are optional
    return $this->apiStreamResponse($this->yieldUsers(), "Test Message", 200);
}

protected function yieldUsers(): Generator
{
    foreach (User::query()->cursor() as $user) {
        yield $user;
    }
}
```

Response:
```json
{
  "status": true,
  "statusCode": 200,
  "timestamp": 1662105038,
  "message": "Test Message",
  "data": [
    {
      "id": 1,
      "name": "Test User",
      "email": "test-user-email@email.com"
    },
    {
      "id": 2,
      "name": "Test User 2",
      "email":  "test-user-email@email2.com"
    },
    {
      "id": 3,
      "name": "Test User 3",
      "email":  "test-user-email@email3.com"
    }
  ]
}
```

### Error Handling

#### Not Found Exception
```php
return $this->apiResponse([
    'type' => 'notfound', // 'type' => 404,
]);
```

Important Notes:
* You can use dash-separated words (e.g., 'not-found')
* Status codes can be used directly (e.g., 404)

Response:
```json
{
  "status": false,
  "statusCode": 404,
  "timestamp": 1662121027,
  "message": "Not found!",
  "data": null
}
```

### Available Status Types
* created
* accepted
* notfound
* conflict
* badrequest
* exception
* unauthenticated
* unauthorized
* ok

### API Response Parameters
The apiResponse function accepts the following arguments:

1. `type` => Response type (from the types listed above)
2. `filter_data` => boolean
3. `throw_exception` => boolean
4. `message` => string
5. `errorCode` => Check MA\LaravelApiResponse\Enums\ErrorCodesEnum (can be integer, string, or UnitEnum)
6. `status_code` => integer (applies only if type is not sent)

Example with Error Code:
```php
/*
* Error code examples:
* THROTTLING_ERROR
* 1021
* ErrorCodesEnum::THROTTLING_ERROR
* ErrorCodesEnum::THROTTLING_ERROR->name
* ErrorCodesEnum::THROTTLING_ERROR->value
*/
return $this->apiResponse([
    'type' => 'notfound',
    'filter_data' => true,
    'throw_exception' => true,
    'message' => 'TestMessage',
    'errorCode' => 'INVALID_CREDENTIALS', // can be string, integer or UnitEnum
]);
```

Response:
```json
{
  "status": false,
  "statusCode": 404,
  "timestamp": 1724082523,
  "message": "TestMessage",
  "data": null,
  "errorCode": "INVALID_CREDENTIALS"
}
```

### Validation Support

```php
$data = $this->apiValidate($request, [
    'countryId' => ['required'],
    'email' => ['required', 'email']
]);
```

Note: The first parameter can be either `Illuminate\Http\Request` or an array

Response for validation failure:
```json
{
  "status": false,
  "statusCode": 400,
  "timestamp": 1662122013,
  "message": "Bad Request!",
  "data": null,
  "errors": [
    {
      "countryId": [
        "The country id field is required."
      ],
      "email": [
        "The email must be a valid email address."
      ]
    }
  ]
}
```

### Pagination Support

```php
$tests = Tests::query()
        ->where('is_active', true)
        ->paginate(2);
        
return $this->apiPaginateResponse($tests);
```

Response:
```json
{
  "status": true,
  "statusCode": 200,
  "timestamp": 1662070940,
  "message": "OK",
  "data": [
    {
      "id": 1,
      "name": "Test 1",
      "is_active": true,
      "created_at": null,
      "updated_at": null
    },
    {
      "id": 2,
      "name": "Test 2",
      "is_active": true,
      "created_at": null,
      "updated_at": null
    }
  ],
  "pagination": {
    "meta": {
      "page": {
        "current": 1,
        "first": 1,
        "last": 10,
        "next": 2,
        "previous": null,
        "per": 2,
        "from": 1,
        "to": 2,
        "count": 2,
        "total": 20,
        "isFirst": true,
        "isLast": false,
        "isNext": true,
        "isPrevious": false
      }
    },
    "links": {
      "path": "https://laravel-v8.test/api/data",
      "first": "https://laravel-v8.test/api/data?page=1",
      "next": "https://laravel-v8.test/api/data?page=2",
      "previous": null,
      "last": "https://laravel-v8.test/api/data?page=10"
    }
  }
}
```

### Available Methods

```php
$this->apiPaginate($pagination, bool $reverse_data = false)
```
Parameters:
- First parameter: paginated model
- Second parameter: whether to reverse the data or maintain original order

Error Handling Methods:
```php
$this->apiException($errors = null, bool $throw_exception = true, $errorCode = null)
$this->apiNotFound($errors = null, bool $throw_exception = true, $errorCode = null)
$this->apiBadRequest($errors = null, bool $throw_exception = true, $errorCode = null)
```

Parameters:
1. First parameter: errors (string or array)
2. Second parameter: whether to throw exception (default: true)
3. Third parameter: error code (integer, string, null, or UnitEnum instance)

**IMPORTANT**: If error code is null, it will return the default error code if config `returnDefaultErrorCodes` is true

### Forbidden Error Response

```php
return $this->apiForbidden('TEST MESSAGE', [
    'error_1' => 'asdasasdasd',
    'error_2' => 'asdasdasdasd'
], 'FORBIDDEN');
```

Parameters:
1. First parameter: message (string or null)
2. Second parameter: errors (string, array, or null)
3. Third parameter: error code (integer, string, null, or UnitEnum instance)

Default message is "Forbidden"

Note: If errors is null, the errors property won't appear in the response

Response:
```json
{
  "status": false,
  "statusCode": 403,
  "timestamp": 1723864903,
  "message": "TEST MESSAGE",
  "data": null,
  "errorCode": "FORBIDDEN",
  "errors": {
    "error_1": "asdasasdasd",
    "error_2": "asdasdasdasd"
  }
}
```

### Unauthenticated Error Response

```php
return $this->apiUnauthenticated('TEST MESSAGE', [
    'error_1' => 'asdasasdasd',
    'error_2' => 'asdasdasdasd'
], 'UNAUTHORIZED_ACCESS');
```

Parameters:
- First parameter: message (string or null)
- Second parameter: errors (array, string, or null)
- Default message: "Unauthenticated"

Note: If errors is null, the errors property won't appear in the response

Response:
```json
{
  "status": false,
  "statusCode": 403,
  "timestamp": 1723864903,
  "message": "TEST MESSAGE",
  "data": null,
  "errorCode": "UNAUTHORIZED_ACCESS",
  "errors": {
    "error_1": "asdasasdasd",
    "error_2": "asdasdasdasd"
  }
}
```

### API Validation

```php
$this->apiValidate($data, $roles, array $messages = [], array $customAttributes = [])
```

Follows Laravel's validate() method pattern:
- First parameter: data
- Second parameter: rules
- Third parameter: messages
- Fourth parameter: custom attributes

Returns validated data on success or throws an exception using this trait on failure.

### API Validation (Request)

```php
use APIRequestValidator;
```

Add this trait to your requests to handle validation errors.

### Debug Helper

```php
return $this->apiDD([
    [
        'id' => 1,
        'name' => 'Test Name',
    ],
    [
        'id' => 2,
        'name' => 'Test Name 2',
    ],
    [
        'id' => 3,
        'name' => 'Test Name 3',
    ],
]);
```

Response:
```json
{
  "status": false,
  "statusCode": 422,
  "timestamp": 1662105345,
  "message": "Die and dump",
  "data": [
    {
      "id": 1,
      "name": "Test Name"
    },
    {
      "id": 2,
      "name": "Test Name 2"
    },
    {
      "id": 3,
      "name": "Test Name 3"
    }
  ]
}
```

### Error Codes Configuration

The package provides extensive error code configuration options:

1. Enable/disable error codes
2. Set error code enum class or custom enum class
3. Set error codes output type (string or integer)
4. Enable/disable returning default error codes if set as null
5. Set error codes defaults for error functions

### Publishing Error Codes Enum

Basic usage:
```bash
$ php artisan lapi-response:publish-error-codes
```

With custom class name:
```bash
$ php artisan lapi-response:publish-error-codes CustomErrorCodesEnum
```

If no custom name is specified, it will generate with the default class name "ErrorCodesEnum"

## Contributors

[![Contributors](https://contrib.rocks/image?repo=i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
