# Return API Response

[![Latest Version](https://img.shields.io/github/v/release/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/releases)
[![GitHub file size in bytes](https://img.shields.io/github/size/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/releases)
[![GitHub](https://img.shields.io/github/license/i3rror/LAPI-response)](https://github.com/i3rror/LAPI-response/blob/main/LICENSE.md)

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
php artisan vendor:publish --provider="MA\LaravelApiResponse\Providers\APIResponseProvider" --tag="config"
```

Then it's done!

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

There is validation function as example:
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
apiPaginate($pagination, bool $reverse_data = false)
 ```

First parameter is paginated model, And the second parameter is to whether reverse the data or keep it at its order.

 ```php
apiException($errors = null, bool $throw_exception = true)
apiNotFound($errors = null, bool $throw_exception = true)
apiBadRequest($errors = null, bool $throw_exception = true)
 ```

First parameter is for errors, and it can be set as string or array, And the second parameter is whether to throw
exception or not.

 ```php
apiValidate($data, $roles, array $messages = [], array $customAttributes = [])
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

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
