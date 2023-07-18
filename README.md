<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel">
<h1>LaravelSessionLog : Manage and logout your active session on other devices</h1>

---

## Table of contents

- [Introduction](#introduction)
- [Installation & Configuration](#installation--configuration)
- [Todo](#todo)
- [Features](#features)
- [License](#license)

## Introduction

`LaravelSessionLog` is a [Laravel](https://laravel.com/) package, designed Manage and logout your active session on other devices.

## Todo

- [x] Create Session for login
- [x] List of Active Sessions
- [x] Logout Specific Device Session
- [x] Logout Other Devices Session

## Features

- Logged Session Log
- Show Logged in devices
- Logout Specific Device Session
- Logout Other Devices Session

## Installation & Configuration

You can install this package via composer using:

```
composer require signaturetech/laravel-session-log
```

Now instruct the our custom model via the `usePersonalAccessTokenModel` method provided by Sanctum. Typically, you should call this method in the boot method of one of your application's service providers

Please below code to `AppServiceProvider` in `boot` method

```
use SignatureTech\LaravelSessionLog\Models\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
}
```

Now add the `use SignatureTech\LaravelSessionLog\SessionLog` trait to your model.

```
use Laravel\Sanctum\HasApiTokens;
use SignatureTech\LaravelSessionLog\SessionLog;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SessionLog;
}
```

> Please do't forgot to add `Laravel\Sanctum\HasApiTokens` Sanctum default trait to your model

To issue a token, you may use the `createLoginToken` method. The `createLoginToken` method returns a `Laravel\Sanctum\NewAccessToken` instance. API tokens are hashed using SHA-256 hashing before being stored in your database, but you may access the plain-text value of the token using the `plainTextToken` property of the `NewAccessToken` instance. You should display this value to the user immediately after the token has been created:

```
use Illuminate\Http\Request;

Route::post('/tokens/create', function (Request $request) {
    $token = $user->createLoginToken(TokenName);

    return ['token' => $token->plainTextToken];
});
```

> Please use `createLoginToken` function to generate access token insted of `createToken` of Santum

### Methods you can use

- To Get All Active Sessions

```
$user = auth()->user();
$user->sessions();
```

- To Logout Other Devices Session

```
$user = auth()->user();
$user->logoutOtherDevices();
```

- To Logout Specific Device Session

```
$user = auth()->user();
$user->logoutDevice($id);
```

## License

- Written and copyrighted &copy;2022 by Prem Chand Saini ([prem@signaturetech.in](mailto:prem@signaturetech.in))
- ResponseBuilder is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
