# セクション 02: Jetstream, Fortify

## 10 Jetstream の紹介

### 認証ライブラリ比較

|              |                                 Laravel / ui                                 |                                Laravel Breeze                                |                   Fortify                    |       Jetstream       |
| :----------: | :--------------------------------------------------------------------------: | :--------------------------------------------------------------------------: | :------------------------------------------: | :-------------------: |
|   Version    |                                    6.x〜                                     |                                    8.x〜                                     |                    8.x〜                     |         8.x〜         |
| View（PHP）  |                                    Blade                                     |                                    Blade                                     |                      -                       |   Livewire + Blade    |
|      JS      |                              Vue.js / React.js                               |                                  Alpine.js                                   |                      -                       |  Inertia.js + Vue.js  |
|     CSS      |                                  Bootstrap                                   |                                 Tailwindcss                                  |                      -                       |      Tailwindcss      |
| 追加ファイル |                            View/Controller/Route                             |                            View/Controller/Route                             |                      -                       | View/Controller/Route |
|    機能 1    | ログイン、ユーザー登録、パスワードのリセット、<br>メール検証、パスワード確認 | ログイン、ユーザー登録、パスワードのリセット、<br>メール検証、パスワード確認 |                      -                       |
|    機能 2    |                                      -                                       |                                      -                                       | 2 要素認証、<br>プロフィール管理、チーム管理 | API サポート(Sanctum) |

### Jetstream について

Laravel Fortify ・・セッションベースの認証<br>

Laravel Sanctum ・・ ユーザプローフィール・チーム管理周りの UI のビュー<br>

Tailwind CSS ・・ UI のデザイン<br>

Jetstream 自体 ・・ ルートやビュー、コントローラのスカフォールド等を担当<br>

## 11 Jetstream+Livewire のインストール

### Jetstream インストール

`コマンド`<br>

composer require laravel/jetstream<br>

php artisan jetstream:install livewire<br>

npm install && npm run dev<br>

php artisan migrate<br>

php artisan serve<br>

- 参考: https://readouble.com/laravel/9.x/ja/starter-kits.html#laravel-jetstream <br>

### ハンズオン

- `$ composer require laravel/jetstream`を実行<br>

* `$ php artisan jetstream:install livewire`を実行<br>

- `$ npm install && npm run dev`を実行<br>

* `$ php artisan migrate`を実行<br>

### Jetstream 追加ファイル(抜粋)

app/Actions<br>

app/Providers<br>

app/View/Components<br>

## 12 ユーザー登録 ・ ログイン

### オプション機能の OnOff

ユーザー登録、パスワードリセット、メール認証、プロフィール情報更新、パスワード変更、2 要素認証を On/Off できる<br>

`config/fortify.php`<br>

```php:fortify.php
'features' => [
  Features::registration(),
  // 略
]
```

`config/jetstream.php`にも'features`がある<br>
チーム、プロフィール画像、アカウント削除、api<br>

### ハンズオン

`config/fortify.php`を編集<br>

```php:fortify.php
<?php

use App\Providers\RouteServiceProvider;
use Laravel\Fortify\Features;

return [
  /*
    |--------------------------------------------------------------------------
    | Fortify Guard
    |--------------------------------------------------------------------------
    |
    | Here you may specify which authentication guard Fortify will use while
    | authenticating users. This value should correspond with one of your
    | guards that is already present in your "auth" configuration file.
    |
    */

  'guard' => 'web',

  /*
    |--------------------------------------------------------------------------
    | Fortify Password Broker
    |--------------------------------------------------------------------------
    |
    | Here you may specify which password broker Fortify can use when a user
    | is resetting their password. This configured value should match one
    | of your password brokers setup in your "auth" configuration file.
    |
    */

  'passwords' => 'users',

  /*
    |--------------------------------------------------------------------------
    | Username / Email
    |--------------------------------------------------------------------------
    |
    | This value defines which model attribute should be considered as your
    | application's "username" field. Typically, this might be the email
    | address of the users but you are free to change this value here.
    |
    | Out of the box, Fortify expects forgot password and reset password
    | requests to have a field named 'email'. If the application uses
    | another name for the field you may define it below as needed.
    |
    */

  'username' => 'email',

  'email' => 'email',

  /*
    |--------------------------------------------------------------------------
    | Home Path
    |--------------------------------------------------------------------------
    |
    | Here you may configure the path where users will get redirected during
    | authentication or password reset when the operations are successful
    | and the user is authenticated. You are free to change this value.
    |
    */

  'home' => RouteServiceProvider::HOME,

  /*
    |--------------------------------------------------------------------------
    | Fortify Routes Prefix / Subdomain
    |--------------------------------------------------------------------------
    |
    | Here you may specify which prefix Fortify will assign to all the routes
    | that it registers with the application. If necessary, you may change
    | subdomain under which all of the Fortify routes will be available.
    |
    */

  'prefix' => '',

  'domain' => null,

  /*
    |--------------------------------------------------------------------------
    | Fortify Routes Middleware
    |--------------------------------------------------------------------------
    |
    | Here you may specify which middleware Fortify will assign to the routes
    | that it registers with the application. If necessary, you may change
    | these middleware but typically this provided default is preferred.
    |
    */

  'middleware' => ['web'],

  /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | By default, Fortify will throttle logins to five requests per minute for
    | every email and IP address combination. However, if you would like to
    | specify a custom rate limiter to call then you may specify it here.
    |
    */

  'limiters' => [
    'login' => 'login',
    'two-factor' => 'two-factor',
  ],

  /*
    |--------------------------------------------------------------------------
    | Register View Routes
    |--------------------------------------------------------------------------
    |
    | Here you may specify if the routes returning views should be disabled as
    | you may not need them when building your own application. This may be
    | especially true if you're writing a custom single-page application.
    |
    */

  'views' => true,

  /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Some of the Fortify features are optional. You may disable the features
    | by removing them from this array. You're free to only remove some of
    | these features or you can even remove all of these if you need to.
    |
    */

  'features' => [
    Features::registration(),
    Features::resetPasswords(),
    // Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    //編集(コメントアウトする。今回は使わない)
    // Features::twoFactorAuthentication([
    //     // 'confirm' => true,
    //     'confirmPassword' => true,
    // ]),
  ],
];
```
