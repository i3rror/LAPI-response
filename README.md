# Laravel API Response

[![Latest Version](https://img.shields.io/github/v/release/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/releases)
[![GitHub repo size](https://img.shields.io/github/repo-size/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/releases)
[![GitHub](https://img.shields.io/github/license/i3rror/LAPI-response)](https://img.shields.io/github/license/i3rror/LAPI-response)
[![Packagist Downloads](https://img.shields.io/packagist/dt/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/releases)

## Overview

LAPI-response is a comprehensive Laravel package that standardizes API responses across your application. It provides consistent response formatting, error handling, validation support, and pagination, making it easier to build robust APIs.

## Features

- **Consistent JSON Response Format**: Standardized structure for all API responses
- **Multiple Response Types**: Support for success, error, validation, and pagination responses
- **Error Handling**: Built-in error handling with customizable error codes
- **Validation Support**: Simplified request validation with standardized error responses
- **Pagination Support**: Easy pagination with meta information
- **Stream Response**: Support for streaming large datasets
- **Configurable**: Extensive configuration options to customize behavior

## Installation

### Step 1: Install via Composer

```bash
composer require i3rror/lapi-response
```

### Step 2: Register Service Provider

Include the service provider in your `config/app.php` or in `bootstrap/providers.php` if you're using Laravel 11:

```php
MA\LaravelApiResponse\Providers\APIResponseProvider::class
```

### Step 3: Publish Configuration

Run the following command to publish the package configuration:

```bash
php artisan vendor:publish --provider="MA\LaravelApiResponse\Providers\APIResponseProvider" --tag="lapi-response-config"
```

## Basic Implementation

To use this package, add the `APIResponseTrait` to your controllers:

```php
use MA\LaravelApiResponse\Traits\APIResponseTrait;

class YourController extends Controller
{
    use APIResponseTrait;

    // Your controller methods...
}
```

You have two implementation options:

1. **Global Implementation**: Add the trait to `App\Http\Controllers\Controller.php` to make it available across all controllers
2. **Local Implementation**: Add the trait only to specific controllers where API responses are needed

### Helper Functions

Alternatively, you can use the package's global helper functions without adding the trait to your controllers. These functions can be used anywhere in your application:

```php
// In any file in your application
return apiOk($data, $message);
return apiNotFound(["Resource not found"], "Resource not found");
return apiResponse(['message' => 'Success', 'data' => $data]);
```

These helper functions can be used as public functions or as internal helper functions within your application's codebase.

## Usage Examples

### Basic Response

```php
return $this->apiResponse([
    'message' => 'Success Message',
    'data' => $yourData,
    'extra' => [
        'field1' => 'Value 1',
        'field2' => 'Value 2'
    ],
]);
```

Response:
```json
{
  "status": true,
  "statusCode": 200,
  "timestamp": 1662070087,
  "message": "Success Message",
  "data": {
    "key": "value"
  },
  "field1": "Value 1",
  "field2": "Value 2"
}
```

#### Message Example:

```php
return $this->apiOk("Operation completed successfully", "Message");
```

Response:
```json
{
  "status": true,
  "statusCode": 200,
  "timestamp": 1662104853,
  "message": "Message",
  "data": "Operation completed successfully"
}
```

#### Data Example:

```php
return $this->apiOk($yourDataArray, "Message");
```

Response:
```json
{
  "status": true,
  "statusCode": 200,
  "timestamp": 1662105038,
  "message": "Message",
  "data": [
    {
      "id": 1,
      "name": "Example"
    }
  ]
}
```

### Stream Response

The stream response feature allows you to handle large datasets efficiently:

```php
return $this->apiStreamResponse($this->yieldData(), "Streaming data", 200);

protected function yieldData(): Generator
{
    foreach (User::cursor() as $user) {
        yield $user;
    }
}
```

### Error Handling

#### Not Found Example

```php
return $this->apiNotFound("Resource not found", "Error Not Found Message");
```

Response:
```json
{
  "status": false,
  "statusCode": 404,
  "timestamp": 1662121027,
  "message": "Not found!",
  "data": null,
  "errors": ["Resource not found"]
}
```

#### Bad Request Example

```php
return $this->apiBadRequest("Invalid parameters");
```

#### With Error Code

```php
return $this->apiResponse([
    'type' => 'notfound',
    'message' => 'Resource not found',
    'errorCode' => 'RESOURCE_NOT_FOUND',
]);
```

### Available Status Types

* `created` - 201 Created
* `accepted` - 202 Accepted
* `notfound` - 404 Not Found
* `conflict` - 409 Conflict
* `badrequest` - 400 Bad Request
* `exception` - 422 Unprocessable Entity
* `unauthenticated` - 401 Unauthorized
* `unauthorized` - 401 Unauthorized
* `forbidden` - 403 Forbidden
* `ok` - 200 OK

### Validation Support

```php
$data = $this->apiValidate($request, [
    'email' => ['required', 'email'],
    'password' => ['required', 'min:8']
]);
```

If validation fails, it returns a standardized error response with validation errors.

### Pagination Support

```php
$users = User::paginate(10);
return $this->apiPaginate($users);
```

The response includes pagination metadata with page information and navigation links.

## Available Methods

All methods listed below are available both as trait methods and as global helper functions. You can use them either way depending on your implementation preference.

### Create Response
#### Simplified Usage for apiResponse function

You can use short parameter values in two ways:
- String parameters are set as messages
- Array parameters are set as data
```php
// As trait methods
$this->apiResponse($data = null)

// As helper functions
apiResponse($data = null)
````
#### Example
```php
return $this->apiResponse([
        'status_code' => Response::HTTP_OK,
        'message' => 'This is custom message',
        'data' => [
            'data1' => 'custom data 1',
            'data2' => 'custom data 2'
        ],
        'extra' => [
            'extra1' => 'extra data 1',
            'extra2' => 'extra data 2'
        ]
    ]);
```
### Response
```json
{
  "status": true,
  "statusCode": 200,
  "timestamp": 1749643578,
  "message": "This is custom message",
  "data": {
    "data1": "custom data 1",
    "data2": "custom data 2"
  },
  "extra1": "extra data 1",
  "extra2": "extra data 2"
}
```

### Success Responses

```php
// As trait methods
$this->apiOk($data = null)
$this->apiResponse($data = null)

// As helper functions
apiOk($data = null)
apiResponse($data = null)
```

### Error Responses

```php
// As trait methods
$this->apiNotFound($errors = null, $throw_exception = true, $errorCode = null)
$this->apiBadRequest($errors = null, $throw_exception = true, $errorCode = null)
$this->apiException($errors = null, $throw_exception = true, $errorCode = null)
$this->apiUnauthenticated($message = null, $errors = null, $errorCode = null)
$this->apiForbidden($message = null, $errors = null, $errorCode = null)

// As helper functions
apiNotFound($errors = null, $throw_exception = true, $errorCode = null)
apiBadRequest($errors = null, $throw_exception = true, $errorCode = null)
apiException($errors = null, $throw_exception = true, $errorCode = null)
apiUnauthenticated($message = null, $errors = null, $errorCode = null)
apiForbidden($message = null, $errors = null, $errorCode = null)
```

### Pagination and Validation

```php
// As trait methods
$this->apiPaginate($pagination, $appends = [], $reverse_data = false)
$this->apiValidate($data, $rules, $messages = [], $attributes = [])

// As helper functions
apiPaginate($pagination, $appends = [], $reverse_data = false)
apiValidate($data, $rules, $messages = [], $attributes = [])
```

### Other Utilities

```php
// As trait methods
$this->apiDD($data) // Debug helper
$this->apiStreamResponse($generator, $message = null, $statusCode = 200)

// As helper functions
apiDD($data) // Debug helper
apiStreamResponse($generator, $message = null, $statusCode = 200)
```

## Configuration

The package provides extensive configuration options in `config/response.php`:

### Data Handling

```php
// Remove null values from arrays
'removeNullDataValues' => false,

// Set empty data to null
'setNullEmptyData' => true,

// Format of validation errors
'returnValidationErrorsKeys' => true,
```

### Error Codes

```php
// Enable error codes
'enableErrorCodes' => true,

// Error codes enum class
'errorCodes' => \MA\LaravelApiResponse\Enums\ErrorCodesEnum::class,

// Error codes output format (string or integer)
'errorCodesType' => 'string',

// Return default error codes if not specified
'returnDefaultErrorCodes' => true,
```

### Publishing Error Codes Enum

```bash
php artisan lapi-response:publish-error-codes
```

With custom class name:
```bash
php artisan lapi-response:publish-error-codes CustomErrorCodesEnum
```

## Contributors

[![Contributors](https://contrib.rocks/image?repo=i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
