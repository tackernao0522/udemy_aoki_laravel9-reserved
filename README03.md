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

## 20 Livewire と Alpine.js の比較表など

### TALL スタック

T ・・ Tailwindcss<br>

A ・・ Alpine.js<br>

L ・・ Laravel<br>

L ・・ Livewire<br>

### 比較表

|     Vue.js      |          Alpine.js           |        Livewire        |                特徴                 |
| :-------------: | :--------------------------: | :--------------------: | :---------------------------------: |
| data プロパティ |            x-data            |       Blade 構文       | データの状態 オブジェクトでも書ける |
| mounted()フック |            x-init            | クラス内に mount()など |          DOM 更新時に実行           |
|     v-show      |            x-show            |       Blade 構文       |            True なら表示            |
|     v-bind      | x-bind:属性="式", :属性="式" |  Blade コンポーネント  |           属性の値を設定            |
|      v-on       |   x-on:click="", @click=""   |       wire:click       |   イベント時のメソッドなどを設定    |
|     v-model     |           x-model            |       wire:model       |     双方向データバインディング      |
| v-text, v-html  |        x-text, x-html        |       Blade 構文       |       テキスト表示、HTML 表示       |
|                 |            x-ref             |                        |     コンポーネントから DOM 取得     |
|   v-if, v-for   |         x-if, x-for          |       Blade 構文       |            if 文、for 文            |
|  v-transition   |         x-transition         |                        |           トランジション            |
|                 |           x-spread           |                        |   再利用できるオブジェクトに抽出    |
|     v-cloak     |           x-cloak            |                        |            チラつき防止             |

### Jetstream で使用されている構文

| フォルダ |                                                                                                                                                                   Alpine.js                                                                                                                                                                   |                                                          Livewire                                                           |
| :------: | :-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------: | :-------------------------------------------------------------------------------------------------------------------------: |
|   auth   |                                                                                                                                                              x-show, x-on:click                                                                                                                                                               |                                                                                                                             |
| layouts  |                                                                                                                                                                                                                                                                                                                                               |                                     @livewireStyles<br>@livewire()<br>@livewireScripts                                      |
| profile  |                                                                                                      x-data,<br>x-on:confirming-logout-other-browser-sittions.window,<br>x-ref, x-on:change, x-show, x-bind:style,<br>x-on:click.prevent                                                                                                      |    wire:click, wire:loading.attr,<br>wire:model,<br>wire:model.defer,<br>wire:keydown.enter,<br>wire:then, wire:target,     |
|  vendor  | x-data, x-init, x-show.transition.out.opacity.duration.1500ms,<br>x-transition:leave.opacity.duration.1500ms,<br>x-show, x-on:click, x-ref,<br>x-show, x-on:click, x-ref,<br>x-on:password-confirmed.window,<br>x-on:confirming-password.window,<br>@click, @click.away, @close.stop,<br>x-transition(略), x-on:close.stop, x-on:keydown(略), | wire:model,<br>wire:model.defer,<br>wire:keydown.enter,<br>wire:click, wire:loading.attr,<br>wire:submit.prevent,<br>\$wire |
|  other   |                                                                                                                                                                x-data, @click                                                                                                                                                                 |                                                                                                                             |

### jetstreum のコンポーネント

x-jet- ・・・ コンポーネント<br>

`vendor/jetstream`フォルダ内のコンポーネント<br>

### Livewire を使うために

```html:sample.html
<html>
  <head>
    @livewireStyles
  </head>
  <body>
    @livewireScripts
  </body>
</html>
```

### ハンズオン

- `resources/views/livewire-test/index.blade.php`を編集<br>

```html:index.blade.php
<html>
  <head>
    @livewireStyles
  </head>

  <body>
    livewireテスト @livewireScripts
  </body>
</html>
```

## 21 サンプル Counter

### Livewire サンプルについて

Jetstream は Alpine.js と組み合わせて構成されているが、シンプルに解説するために<br>
まずは Livewire のみで進めていく。<br>

https://readouble.com/livewire/2.x/ja/quickstart.html <br>

### Counter コンポーネント

php artisan make:livewire counter<br>

2 つのファイルが生成される<br>

`app/Http/Livewire/Counter.php`<br>

`resources/views/livewire/counter.blade.php`<br>

### ハンズオン

- `$ php artisan make:livewire counter`を実行<br>

```:terminal

CLASS: app/Http/Livewire/Counter.php
VIEW:  resources/views/livewire/counter.blade.php

  _._
/ /o\ \   || ()                ()  __
|_\ /_|   || || \\// /_\ \\ // || |~~ /_\
 |`|`|    || ||  \/  \\_  \^/  || ||  \\_


Congratulations, you've created your first Livewire component! 🎉🎉🎉

 Would you like to show some love by starring the repo? (yes/no) [no]:
 >
```

- `no`を入力して`Enter`<br>

### views/livewire/counter.blade.php

```html:counter.blade.php
<div sthle="text-align: center">
  <button wire:click="increment">+</button>
  // wire:click="メソッド名"で実行
  <h1>{{ $count }}</h1>
  // Counterクラス内プロパティを表示
</div>
```

### ハンズオン

- `app/Http/Livewire/Counter.php`を編集<br>

```php:Counter.php
<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Counter extends Component
{
  public $count = 0;

  public function increment()
  {
    $this->count++;
  }

  public function render()
  {
    return view('livewire.counter');
  }
}
```

- `resources/views/livewire/counter.blade.php`を編集<br>

```html:counter.blade.php
<div style="text-align: center">
  <button wire:click="increment">+</button>
  <h1>{{ $count }}</h1>
</div>
```

- `resources/views/livewire-test/index.blade.php`を編集<br>

```html:index.blade.php
<html>
  <head>
    @livewireStyles
  </head>

  <body>
    livewireテスト {{--
    <livewire:counter />
    --}} @livewire('counter') @livewireScripts
  </body>
</html>
```

- localhost/livewire-test/index にアクセスして試してみる<br>

## 22. データバインディング wire:model

### データバインディング

Class 側<br>

```
public \$name = '';
```

Blade 側<br>

```
<input wire:model="name" type="text">
こんにちは {{ $name }} さん
```

### ハンズオン

- `app/Http/Livewire/Counter.php`を編集<br>

```php:Counter.php
<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Counter extends Component
{
  public $count = 0;
  public $name = ''; // 追記

  public function increment()
  {
    $this->count++;
  }

  public function render()
  {
    return view('livewire.counter');
  }
}
```

- `resoureces/views/livewire/counter.blade.php`を編集<br>

```html:counter.blade.php
<div style="text-align: center">
  <button wire:click="increment">+</button>
  <h1>{{ $count }}</h1>
  <div class="mb-8"></div>
  こんにちは、{{ $name }} さん
  <br />
  <input type="text" wire:model="name" />
</div>
```

## 23 wire:model のオプション

### データバインディング オプション

`wire:model.debounce.20000ms=""`<br>
指定 ms 待って通信 1000ms = 1 秒<br>

`wire:model.lazy=""`<br>
フォーカルが外れたタイミングで通信<br>
(JS の change イベント)<br>

`wire:model.defer=""`<br>
submit ボタンなどを押したタイミングで通信<br>

### ハンズオン

`resources/views/livewire/counter.blade.php`を編集<br>

```html:counter.blade.php
<div style="text-align: center">
  <button wire:click="increment">+</button>
  <h1>{{ $count }}</h1>
  <div class="mb-8"></div>
  こんにちは、{{ $name }} さん
  <br />
  {{--
  <input type="text" wire:model.debounce.2000ms="name" />
  --}}
  <input type="text" wire:model.lazy="name" />
</div>
```
