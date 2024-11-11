# Laravel API Response Package

[![Latest Version](https://img.shields.io/github/v/release/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/releases)
[![GitHub repo size](https://img.shields.io/github/repo-size/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/releases)
[![GitHub](https://img.shields.io/github/license/i3rror/LAPI-response)](https://img.shields.io/github/license/i3rror/LAPI-response)
[![Packagist Downloads](https://img.shields.io/packagist/dt/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/releases)

A comprehensive package for handling various API response types in Laravel applications.

## Installation

1. Install via Composer:
```bash
composer require i3rror/LAPI-response
```

2. Add the service provider to `config/app.php`:
```php
MA\LaravelApiResponse\Providers\APIResponseProvider::class
```

3. Publish the configuration:
```bash
php artisan vendor:publish --provider="MA\LaravelApiResponse\Providers\APIResponseProvider" --tag="lapi-response"
```

## Usage

To use this package, import the trait in your controllers:

```php
use APIResponseTrait;
```

### Implementation Options

1. **Global Implementation**: Add the trait to `App\Http\Controllers\Controller.php`
2. **Local Implementation**: Add the trait only to specific controllers where needed

## Basic Examples

### Simple Response

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
                ]
            ]
        ]);
    }
}
```

Expected Response:
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
    ]
}
```

### Simplified Parameter Usage

#### Message Only
```php
return $this->apiOk("test message");
// or
return $this->apiResponse("test message");
```

#### Data Only
```php
return $this->apiOk([
    ['id' => 1, 'name' => 'Test Name'],
    ['id' => 2, 'name' => 'Test Name 2'],
    ['id' => 3, 'name' => 'Test Name 3']
]);
```

### Stream Response

Requires a Generator class as the first parameter:

```php
public function index()
{
    return $this->apiStreamResponse($this->yieldUsers(), "Test Message", 200);
}

protected function yieldUsers(): Generator
{
    foreach (User::query()->cursor() as $user) {
        yield $user;
    }
}
```

### Error Responses

Available error types:
- created
- accepted
- notfound
- conflict
- badrequest
- exception
- unauthenticated
- unauthorized
- ok

Example:
```php
return $this->apiResponse([
    'type' => 'notfound', // or 'type' => 404
    'filter_data' => true,
    'throw_exception' => true,
    'message' => 'TestMessage',
    'errorCode' => 'INVALID_CREDENTIALS'
]);
```

### Validation

```php
$data = $this->apiValidate($request, [
    'countryId' => ['required'],
    'email' => ['required', 'email']
]);
```

### Pagination Response

```php
$tests = Tests::query()
    ->where('is_active', true)
    ->paginate(2);
    
return $this->apiPaginateResponse($tests);
```

## Available Methods

- `apiPaginate($pagination, bool $reverse_data = false)`
- `apiException($errors = null, bool $throw_exception = true, $errorCode = null)`
- `apiNotFound($errors = null, bool $throw_exception = true, $errorCode = null)`
- `apiBadRequest($errors = null, bool $throw_exception = true, $errorCode = null)`
- `apiForbidden($message = null, $errors = null, $errorCode = null)`
- `apiUnauthenticated($message = null, $errors = null, $errorCode = null)`
- `apiValidate($data, $roles, array $messages = [], array $customAttributes = [])`
- `apiDD($data)`

## Error Codes Configuration

The package provides flexible error code handling:
1. Enable/disable error codes
2. Set custom error code enum class
3. Configure output type (string/integer)
4. Enable/disable default error codes
5. Set default error codes for functions

### Publishing Error Codes Enum

```bash
php artisan lapi-response:publish-error-codes
# or with custom name
php artisan lapi-response:publish-error-codes CustomErrorCodesEnum
```

## Contributors

<table>
<tr>
    <td align="center">
        <a href="https://github.com/Ahmed-Elrayes">
            <img src="https://avatars.githubusercontent.com/u/30704271?v=4" width="48" style="border-radius:50%" alt="Ahmed Elrayes"/>
            <br />
            <sub><b>Ahmed Elrayes</b></sub>
        </a>
    </td>
    <td align="center">
        <a href="https://github.com/i3rror">
            <img src="https://avatars.githubusercontent.com/u/26237098?v=4" width="48" style="border-radius:50%" alt="Mohamed Aboushady"/>
            <br />
            <sub><b>Mohamed Aboushady</b></sub>
        </a>
    </td>
</tr>
</table>

## License

This package is licensed under the MIT License. See the [License File](LICENSE.md) for more information.
