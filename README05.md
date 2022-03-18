## 31 tailwaidcss3 以降は npm run watch

### Tailwindcss の読み込み

関連ファイル<br>

`resources/css/app.css`<br>

`tailwind.config.js`<br>
content ・・ JustInTime 対象を選ぶ<br>

`webpack.mix.js`<br>
コンパイル元、必要機能、コンパイル先の設定<br>

`layouts/app.blade.php`を参照<br>

```html:app.blade.php
<!-- Styles -->
<link res="stylesheet" href="{{ mix('css/app.css') }}" />

@livewireStyles

<!-- Scripts -->
<script src="{{ mix('js/app.js') }}" defer></script>
```

### Tailwindcss を編集する時の注意

Tailwind 3.0 から<br>
Just In Time 機能が追加されたため、<br>

`npm run watch`で監視しながら<br>
`php artisan serve`でサーバーを立ち上げる<br>

合間で`npm run dev（コンパイル）`や<br>
`npm run prod（本番環境向けコンパイル）も実施する<br>

### ハンズオン

- `resources/views/livewire-test/register.blade.php`を編集<br>

```html:register.blade.php
<html>
  <head>
    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" />

    @livewireStyles

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
  </head>

  <body>
    livewireテスト
    <span class="text-blue-600">register</span>
    @livewire('register') @livewireScripts
  </body>
</html>
```

- `$ npm run watch`を実行<br>

* `resources/views/livewire/register.blade.php`を編集<br>

```html:register.blade.php
<div>
  <form wire:submit.prevent="register">
    <label for="name">名前</label>
    <input id="name" type="text" wire:model="name" />
    <br />
    @error('name')
    <div class="text-red-400">{{ $message }}</div>
    @enderror

    <label for="email">メールアドレス</label>
    <input type="text" id="email" wire:model="email" />
    <br />
    @error('email')
    <div class="text-red-400">{{ $message }}</div>
    @enderror

    <label for="password">パスワード</label>
    <input type="password" id="password" wire:model="password" />
    <br />
    @error('password')
    <div class="text-red-400">{{ $message }}</div>
    @enderror

    <button>登録する</button>
  </form>
</div>
```

### 32 リアルタイムバリデーション

- `app/Http/Livewire/Register.php`を編集<br>

```php:Register.php
<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
  public $name;
  public $email;
  public $password;

  protected $rules = [
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users',
    'password' => 'required|string|min:8',
  ];

  // 追記
  public function updated($property)
  {
    $this->validateOnly($property);
  }

  public function register()
  {
    $this->validate();
    User::create([
      'name' => $this->name,
      'email' => $this->email,
      'password' => Hash::make($this->password),
    ]);
  }

  public function render()
  {
    return view('livewire.register');
  }
}
```

- `resources/views/livewire/register.blade.php`を編集<br>

```html:register.blade.php
<div>
  <form wire:submit.prevent="register">
    <label for="name">名前</label>
    <input id="name" type="text" wire:model="name" />
    <br />
    @error('name')
    <div class="text-red-400">{{ $message }}</div>
    @enderror

    <label for="email">メールアドレス</label>
    <input type="text" id="email" wire:model.lazy="email" />
    <!-- 編集 -->
    <br />
    @error('email')
    <div class="text-red-400">{{ $message }}</div>
    @enderror

    <label for="password">パスワード</label>
    <input type="password" id="password" wire:model="password" />
    <br />
    @error('password')
    <div class="text-red-400">{{ $message }}</div>
    @enderror

    <button>登録する</button>
  </form>
</div>
```

## 33 フラッシュメッセージ

https://readouble.com/livewire/2.x/ja/flash-messages.html <br>

`Livewire/Register.php クラス`<br>

### フラッシュメッセージ クラス側

`Livewire/Register.php クラス`<br>

```php:Register.php
public function register()
{
  // 略
  session()->flash('message', '登録OKです');

  return to_route('livewire-test.index); // Laravel9新機能
}
```

### ハンズオン

- `app/Http/Livewire/Register.php`を編集<br>

```php:Register.php
<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
  public $name;
  public $email;
  public $password;

  protected $rules = [
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users',
    'password' => 'required|string|min:8',
  ];

  public function updated($property)
  {
    $this->validateOnly($property);
  }

  public function register()
  {
    $this->validate();
    User::create([
      'name' => $this->name,
      'email' => $this->email,
      'password' => Hash::make($this->password),
    ]);

    session()->flash('message', '登録OKです'); // 追記

    return to_route('livewire-test.index'); // 追記
  }

  public function render()
  {
    return view('livewire.register');
  }
}
```

- `resources/views/livewire-test/index.blade.php`を編集<br>

```html:index.blade.php
<html>
  <head>
    @livewireStyles
  </head>

  <body>
    livewireテスト
    <div>
      @if (session()->has('message'))
      <div class="">
        {{ session('message') }}
      </div>
      @endif
    </div>
    {{--
    <livewire:counter />
    --}} @livewire('counter') @livewireScripts
  </body>
</html>
```

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
  ->name('livewire-test.')
  ->group(function () {
    Route::get('index', 'index')->name('index'); // 編集
    Route::get('register', 'register')->name('register'); // 編集
  });
```

# セクション 04: Alpine.js

## 35 Alpne.js の紹介

### Alpine.js とは

HTML 内にスクリプトが書ける<br>
Tailwindcss の JavaScript 版<br>

Vue や React よりシンプル<br>
Vue の構文に似ている<br>

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

### Alpine.js マニュアル

Readouble<br>
https://readouble.com/livewire/2.x/ja/alpine-js.html <br>

Livewire 内の Alpine.js<br>
https://laravel-livewire.com/docs/2.x/alpine-js <br>

Alpine.js<br>
https://alpinejs.dev/ <br>
