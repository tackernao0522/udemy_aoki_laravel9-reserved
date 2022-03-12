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

## 13 プロフィール画像のアップロード

### プロフィール画像

`config/jetstream.php`の features で<br>
`Features:profilePhotos();`のコメントアウトを解除<br>

php artisan storage:link リンク作成<br>

php artisan migrate:fresh 実行<br>

`.env`の APP_URL を下記に変更<br>

APP_URL=http://127.0.0.1:8000<br>

### ハンズオン

- `config/jetstream.php`を編集<br>

```php:jetstream.php
<?php

use Laravel\Jetstream\Features;

return [
  /*
    |--------------------------------------------------------------------------
    | Jetstream Stack
    |--------------------------------------------------------------------------
    |
    | This configuration value informs Jetstream which "stack" you will be
    | using for your application. In general, this value is set for you
    | during installation and will not need to be changed after that.
    |
    */

  'stack' => 'livewire',

  /*
     |--------------------------------------------------------------------------
     | Jetstream Route Middleware
     |--------------------------------------------------------------------------
     |
     | Here you may specify which middleware Jetstream will assign to the routes
     | that it registers with the application. When necessary, you may modify
     | these middleware; however, this default value is usually sufficient.
     |
     */

  'middleware' => ['web'],

  /*
    |--------------------------------------------------------------------------
    | Jetstream Guard
    |--------------------------------------------------------------------------
    |
    | Here you may specify the authentication guard Jetstream will use while
    | authenticating users. This value should correspond with one of your
    | guards that is already present in your "auth" configuration file.
    |
    */

  'guard' => 'sanctum',

  /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Some of Jetstream's features are optional. You may disable the features
    | by removing them from this array. You're free to only remove some of
    | these features or you can even remove all of these if you need to.
    |
    */

  'features' => [
    // Features::termsAndPrivacyPolicy(),
    Features::profilePhotos(), // コメントアウトを解除
    // Features::api(),
    // Features::teams(['invitations' => true]),
    Features::accountDeletion(),
  ],

  /*
    |--------------------------------------------------------------------------
    | Profile Photo Disk
    |--------------------------------------------------------------------------
    |
    | This configuration value determines the default disk that will be used
    | when storing profile photos for your application's users. Typically
    | this will be the "public" disk but you may adjust this if needed.
    |
    */

  'profile_photo_disk' => 'public',
];
```

- `$ php artisan storage:link`を実行<br>

* `$ php artisan migrate:fresh`を実行(これでプロフィール画像をアップロードする)<br>

## 14 ログイン時のソースコードを見てみる

### ログイン機能をカスタマイズするなら

`App\Actions` に置かれている<br>

ルーティング先のファイルは参考程度に見ておく<br>

### Fortify（要塞・固める）

Laravel\Fortify は`vendor/laravel/fortify/src`の中<br>

login のアクション<br>
vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php<br>

`vendor/laravel/fortify/src/Http/controllers/AuthenticatedSessionController.php`<br>

app でサービスコンテナに登録(ページ読み込みの度に実行)<br>

```php:AutenticatedSessionController.php
public function create()
{
  return app(LoginViewResponse::class);
}
```

### 2 種類のサービスプロバイダ

```
// app/providersフォルダ配下
APP\Providers\FortifyServiceProvider
App\Providers\JetstreamServiceProvider
```

```
// vendor/laravelフォルダ配下
vendor/laravel/fortify/src/FortifyServiceProvider
vendor/laravel/jetstream/src/JetstreamServiceProvider
```

### vendor/jetstream@boot

`vendor/laravel/jetstream/src/jetstreamServiceProvider.php`<br>

```php:jetstreamServiceProvider.php
// bootメソッドは起動時に実行
public function boot()
{
  $this->loadViewsFrom(__DIR__.'/../resources/views','jetstream);
  // マニュアルのパッケージ開発/ビュー

  Fortify::viewPrefix('auth.'); // 次ページ
}
```

### vendor/Fortify@viewPrefix

`vendor/laravel/fortify/Fortify.php`<br>

```php:Fortify.php
public static function viewPrefix(string $prefix)
{
  static::loginView($prefix.'login');
}
// 引数で$prefixがつくので、auth.loginになる
```

### vendor/Fortify@loginView

`vendor/laravel/fortify/Fortify.php`<br>

```php:Fortify.php
public static function loginView($view)
{
  app()->singleton(LoginViewResponse::class, function() use ($view) {
    return new SimpleViewResponse($view);
  });
}
// appでサービスコンテナにLoginViewResponseを登録している
// SimpleViewResponseをインスタンス化している
```

### SimpleViewResponse

`vendor/laravel/fortify/src/Http/Response/SimpleViewResponse.php`<br>

```php:SimpleViewResponse.php
public function toResponse($request)
{
  if (!is_callable($this->view) || is_string($this->view)) {
    return view($this->view, ['request' => $request]); // auth.loginが表示される
  }

  $response = call_user_func($this->view, $request);

  if ($response instanceof Responsable) {
    return $response->toResponse($request);
  }
}
```

## 15 ルーティング Fortify

### ルーティング Fortify その 1

`vendor/laravel/fortify/routes/routes.php`<br>

Features で機能有無を確認している<br>
`vendor/laravel/fortify/src/Features.php`<br>
機能有無は `config/fortify.php`の`features`がコメントアウトされているか<br>

コントローラーは<br>

`vendor/laravel/fortify/src/Http/Controllers/配下<br>

### ルーティング Fortify その 2

もしカスタマイズする場合は<br>

`JetstreamServiceProvider`内でルーティング情報を無効化する<br>

```
use Laravel\Fortify\Fortify;

public function register()
{
  Fortify::ignoreRoutes();
}
```

### ハンズオン

`app/Providers/JetstreamServiceProvider.php`を編集<br>

```php:JetstreamServiceProvider.php
<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify; // 追記
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    // 編集
    Fortify::ignoreRoutes();
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    $this->configurePermissions();

    Jetstream::deleteUsersUsing(DeleteUser::class);
  }

  /**
   * Configure the permissions that are available within the application.
   *
   * @return void
   */
  protected function configurePermissions()
  {
    Jetstream::defaultApiTokenPermissions(['read']);

    Jetstream::permissions(['create', 'read', 'update', 'delete']);
  }
}
```

※ 上記のように一度無効化してから`web.php`で編集する<br>

`app/Providers/JetstreamServiceProvider.php`を編集<br>

```php:JetstreamServiceProvider.php
<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    // Fortify::ignoreRoutes(); // 今回はコメントアウトしておく Fortifyのルーティングを無効化する方法
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    $this->configurePermissions();

    Jetstream::deleteUsersUsing(DeleteUser::class);
  }

  /**
   * Configure the permissions that are available within the application.
   *
   * @return void
   */
  protected function configurePermissions()
  {
    Jetstream::defaultApiTokenPermissions(['read']);

    Jetstream::permissions(['create', 'read', 'update', 'delete']);
  }
}
```
