# Return API Response

[![Latest Version](https://img.shields.io/github/v/release/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/releases)
[![GitHub repo size](https://img.shields.io/github/repo-size/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/releases)
[![GitHub](https://img.shields.io/github/license/i3rror/LAPI-response)](https://img.shields.io/github/license/i3rror/LAPI-response)
[![Packagist Downloads](https://img.shields.io/packagist/dt/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/releases)


This package can return all sorts of responses for API

How to use this package:

```cmd
composer require i3rror/LAPI-response
```

Then include its service provider to your `config/app.php`

```cmd
MA\LaravelApiResponse\Providers\APIResponseProvider::class
```

After that you can publish the config.

```cmd
php artisan vendor:publish --provider="MA\LaravelApiResponse\Providers\APIResponseProvider" --tag="lapi-response"
```

Then it's done!

In order to use this package you need to use this code inside your controllers
```php
use APIResponseTrait;
```

There are two ways to use this package

1. Use it globally by adding using this code inside ``App\Http\Controllers\Controller.php``
2. Use it internally by adding the use code inside the controller you want to use it in.



Here are a few short examples of what you can do:

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
            ]
        ]);
    }
}
```

Expected response

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

You can use short params values (If it's string then it will be set as message, And if it's array then it will be set as
data)
Message:

```php
return $this->apiOk("test message");

// Or

return $this->apiResponse("test message");
```

Responses:

```json
{
  "status": true,
  "statusCode": 200,
  "timestamp": 1662104853,
  "message": "OK",
  "data": "test message"
}
```

Data:

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

// Or

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

There are also more types such as not found exception

```php
return $this->apiResponse([
    'type' => 'notfound', // 'type' => 404,
]);
```
* You can also add dash between the words such as `not-found`.
* Or even write **status code as 404**

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
There is a list of all status strings you can use **(otherwise you can use status code)**
* created
* accepted
* notfound
* conflict
* badrequest
* exception
* unauthenticated
* unauthorized
* ok

These are all the arguments you can send in apiResponse function
1. type => the types we wrote earlier.
2. filter_data => boolean.
3. throw_exception => boolean.
4. message => string.
5. errorCode => Check `MA\LaravelApiResponse\Enums\ErrorCodesEnum`, you can either send it as integer, string or UnitEnum.
6. status_code => integer (it will be applied only of type is not sent).

**as example**
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
    'errorCode' => 'INVALID_CREDENTIALS', // you can make it string, integer or UnitEnum
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

**There is validation function as example:**
```php
$data = $this->apiValidate($request, [
    'countryId' => ['required'],
    'email' => ['required', 'email']
]);
```
**Note that you can either pass `Illuminate\Http\Request` Or `Array` as first parameter**

Response:
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
You can use a pagination response as example:

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

#### **List of all methods that can be used:**

 ```php
$this->apiPaginate($pagination, bool $reverse_data = false)
 ```

First parameter is paginated model, And the second parameter is to whether reverse the data or keep it at its order.

 ```php
$this->apiException($errors = null, bool $throw_exception = true, $errorCode = null)
$this->apiNotFound($errors = null, bool $throw_exception = true, $errorCode = null)
$this->apiBadRequest($errors = null, bool $throw_exception = true, $errorCode = null)
 ```
1. The first parameter is for errors as it can be set as string or array.
2. The second parameter determines whether to throw exception or not, default is true.
3. The third parameter is for error code to be returned with response, it can either be an integer, string, null or UnitEnum instance

**IMPORTANT**
<br>
If error code is set to `null` it will return default error code if config `returnDefaultErrorCodes` is set to `true`

**Return api forbidden error:**

The first param is for message and can be set as null, The second one is for errors can be either array, string or null.
1. The first parameter is for message as it can be set as string or null.
2. The second parameter is for errors as it can be set as string or array.
3. The third parameter is for error code to be returned with response, it can either be an integer, string, null or UnitEnum instance

Default message is **Forbidden**

**PS: if errors is null it won't show errors property in response**
```php
return $this->apiForbidden('TEST MESSAGE', [
            'error_1' => 'asdasasdasd',
            'error_2' => 'asdasdasdasd'
        ], 'FORBIDDEN');
```

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

**Return api unauthenticated error:**

The first param is for message and can be set as null, The second one is for errors can be either array, string or null.

Default message is **Unauthenticated**

**PS: if errors is null it won't show errors property in response**
```php
return $this->apiUnauthenticated('TEST MESSAGE', [
            'error_1' => 'asdasasdasd',
            'error_2' => 'asdasdasdasd'
        ], 'UNAUTHORIZED_ACCESS');
```

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
**There is api Validate**

 ```php
$this->apiValidate($data, $roles, array $messages = [], array $customAttributes = [])
 ```

Same as Laravel `This->validate()` method first parameter is for data and the second one is for roles and the 3rd one
for messages and the last one is for custom attributes.
it should return the values of the validated data if passed or will throw exception using this trait if it failed.

There is die and dump data method

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
### For error codes in config file
1. [x] Enable or disable it.
2. [x] Set error code enum class or maybe your custom enum class.
3. [x] Set error codes output type (string or integer).
4. [x] Enable or disable returning default error codes if set as null.
5. [x] Set error codes defaults for error functions.

### In order to publish the ErrorCodesEnum class
```cmd
php artisan lapi-response:publish-error-codes
```
You can also specify the class name if you want

```cmd
php artisan lapi-response:publish-error-codes CustomErrorCodesEnum
```
Otherwise it will generate it with the default class name as **ErrorCodesEnum**
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
