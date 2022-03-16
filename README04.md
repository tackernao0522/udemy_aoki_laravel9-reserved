## 25 アクション wire:click, wire:mouseover など

### アクション

`wire:click=""` または `@click=""`<br>

`wire:keydown=""` `wire:keydown.enter=""`<br>

`wire:mouseover=""`<br>

`wire:submit.prenent=""` ページ読み込みを防ぐ<br>

JavaScript のアクション(イベント)と同様<br>
https://developer.mozilla.org/ja/docs/Web/Events <br>

クラス<br>

```
public function mouseOver()
{
  $this->name = 'mouseover';
}
```

ビュー<br>

```
<button wire:mouseover="mouseover">マウスを合わせてね</button>
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
  public $name = '';

  public function mount()
  {
    $this->name = 'mount';
  }

  public function updated()
  {
    $this->name = 'updated';
  }

  public function mouseOver()
  {
    $this->name = 'mouseover';
  }

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
  <div class="mb-8"></div>
  こんにちは、{{ $name }} さん
  <br />
  <input type="text" wire:model="name" />
  {{--
  <input type="text" wire:model.debounce.2000ms="name" />
  --}} {{--
  <input type="text" wire:model.lazy="name" />
  --}} {{--
  <input type="text" wire:model.defer="name" />
  --}}
  <br />
  <button wire:mouseover="mouseOver">マウスを合わせてね</button>
</div>
```

- 参考: https://readouble.com/livewire/2.x/ja/actions.html <br>

## 26 フォームの準備

### フォーム送信の準備

php artisan make:livewire register<br>

ルーティング<br>

```
Route::get('register', 'register); // 追記
```

コントローラ<br>

```
public function register()
{
  return view('livewire-test.register);
}
```

ビュー<br>
`livewire-test/register.blade.php`<br>

```
@livewire('register')
```

`Livewire/Register.php`クラス<br>

```
public function register()
{
  dd('登録テスト');
}
```

`views/livewire/register.blade.php`コンポーネント<br>

```
<form wire:submit="register">
  <button>登録</button>
</form>
```

### ハンズオン

- `$ php artisan make:livewire register`を実行<br>

* `routes/web.php`を編集<br>

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
    Route::get('register', 'register'); // 追記
  });
```

- `app/Http/Controllers/LiveWireTestController.php`を編集<br>

```php:LivewireTestController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LivewireTestController extends Controller
{
  public function index()
  {
    return view('livewire-test.index');
  }

  public function register()
  {
    return view('livewire-test.register');
  }
}
```

- `resources/views/livewire-test/register.blade.php`ファイルを作成<br>

```html:register.blade.php
<html>
  <head>
    @livewireStyles
  </head>

  <body>
    livewireテスト register @livewire('register') @livewireScripts
  </body>
</html>
```

- `resources/views/livewire/register.blade.php`を編集<br>

```html:register.blade.php
<div>
  <form wire:submit="register">
    <button>登録する</button>
  </form>
</div>
```

- `app/Http/Livewire/Register.php`を編集<br>

```php:Register.php
<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Register extends Component
{
  public function register()
  {
    dd('登録テスト');
  }

  public function render()
  {
    return view('livewire.register');
  }
}
```

## 27 wire:submit についての補足

この段階では form wire:submit に.prevent をつけていないので、<br>

登録ボタンを押すたびにページ再読み込みがかかります。<br>

再読み込みが早いと dd 画面が表示されない事もあるのですが、<br>

気にせず次のレクチャーに進んでいただくと、.prevent をつけるので dd 画面が表示されると思われます。<br>

## 28 \$this と wire:submit.prevent

### Livewire/Register.php

```php:Livewire/Register.php
public $name;
public $email;
public $password;

public function register()
{
  dd($this); // $thisで値が取得できる
}
```

### views/livewire/register.blade.php

```html/register.blade.php
<form wire:submit.prevent="register">
  <label for="name">名前</label>
  <input id="name" type="text" wire:model="name" />
  <br />

  <label for="email">メールアドレス</label>
  <input id="email" type="email" wire:model="email" />
  <br />

  <label for="password">パスワード</label>
  <input id="password" type="password" wire:model="password" />
  <button>登録する</button>
</form>
```

### ハンズオン

`app/Http/Livewire/Register.php`を編集<br>

```php:Register.php
<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Register extends Component
{
  public $name;
  public $email;
  public $password;

  public function register()
  {
    dd($this);
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

    <label for="email">メールアドレス</label>
    <input type="text" id="email" wire:model="email" />
    <br />

    <label for="password">パスワード</label>
    <input type="password" id="password" wire:model="password" />
    <br />
    <button>登録する</button>
  </form>
</div>
```

## 29 登録とバリデーション

- https://readouble.com/livewire/2.x/ja/input-validation.html <br>

### ユーザー登録とバリデーション

`app/Fortify/CreateUser.php`を参考<br>

`Livewire/Register.php(クラス)`<br>

```php:Register.php
use App\Models\User;
user Illuminate\Support\Facades\Hash;

protected $rules = [
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users',
    'password' => 'required|string|min:8',
  ];

  public function register()
  {
    $this->validate();
    User::create([
      'name' => $this->name,
      'email' => $this->email,
      'password' => Hash::make($this->password)
    ]);
  }
```

### ハンズオン

`app/Http/Livewire/Register.php`を編集<br>

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

- https://localhost/livewire-test/register にアクセスして新規登録してみる<br>
