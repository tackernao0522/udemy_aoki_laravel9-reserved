## 16 ルーティング Jetstream

`vendor/laravel/jetstream/routes/livewire.php`<br>

Jetstream 側は publish 可能<br>
php artisan vendor:publish --tag=jetstream-routes<br>

`routes/jetstream.php` が生成される<br>

### ハンズオン

- `$ php artisan vendor:publish --tag=jetstream-routes`を実行<br>

## 17 ビューファイル Jetstream

### vendor/Fortify@loginView

`vendor/laravel/fortify/Fortify.php`<br>

```php:Fortify.php
public static function loginVIew($view)
{
  app()->singleton(LoginViewResponse::class, function () use ($view) {
    return new SimpleViewResponse($view);
  });
}
// app でサービスコンテナにLoginViewResponseを登録している
// SimpleViewResponseをインスタンス化している
```

### SimpleViewResponse

`vendor/laravel/fortify/src/Http/Responses/SimpleViewResponse.php`<br>

```php:SimpleViewResponse.php
if (!is_callable($this->view) || is_string($this->view)) {
  return view($this->view, ['request' => $request]); // auth.loginが表示される
}
$response = call_user_func($this->view, $request);
if ($response instanceof Responsable) {
  return $response->toResponse($request);
}

return $response;
```

### Jetstream ビューファイル

4 つの技術で構成されている<br>

Blade コンポーネント<br>

TailwindCSS<br>

Livewire<br>

Alpine.js<br>

隠れているファイルをコピーして表示しておく<br>

php artisan vendor:publish --tag=jetstream-views<br>

`resources/views/vendor/jetstream/components`<br>

`resources/views/vendor/jetstream/mail`<br>
が生成される<br>

### ハンズオン

- `$ php artisan vendor:publish --tag=jetstream-views`を実行<br>

# セクション 3: Livewire

## 18 Livewire の紹介

### Livewire とは

PHP のみで Vue や React のようなリアクティブな動的コンポーネントを作成できるライブラリ<br>

Blade 構文を使えるので Laravel と相性が良い<br>

### Livewire のデメリット

裏側で Ajax を使いサーバー通信をしているため、JavaScript ライブラリより多少表示スピードが遅い<br>

### Livewire マニュアル

Readouble<br>
https://readouble.com/livewire/2.x/ja/quickstart.html <br>

Livewire<br>
https://laravel-livewire.com/ <br>

### コントローラ生成

php artisan make:controller LivewireTestCotroller<br>

```php:LivewireTestController.php
public function index()
{
  return view('livewire-test.index');
}
```

## 19 Livewire の準備

### ハンズオン

- `$ php artisan make:controller LivewireTestController`を実行<br>

### ルートに追記

`routes/web.php`<br>

```php:web.php
// Laravel9から controllerでまとめることができる

use App\Http\Controllers\LivewireTestController;

Route::controller(LivewireTestController::class)
  ->prefix('livewire-test')
  ->group(function () {
    Route::get('index', 'index');
  });
```

### ハンズオン

- `routes/web.php`を編集<br>

```php:web.php
<?php

use App\Http\Controllers\LivewireTestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])
  ->get('/dashboard', function () {
    return view('dashboard');
  })
  ->name('dashboard');

// localhost/livewire-test/index
Route::controller(LivewireTestController::class)
  ->prefix('livewire-test')
  ->group(function () {
    Route::get('index', 'index');
  });
```

- `resources/views/livewire-test`ディレクトリを作成<br>

* `redources/views/livewire-test/index.blade.php`ファイルを作成<br>

```html:index.blade.php
livewireテスト
```

- localhost/livewire-test/index にアクセスしてみる<br>
