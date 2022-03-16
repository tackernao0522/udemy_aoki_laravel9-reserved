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

## 30 バリデーションメッセージ

- 参考: https://readouble.com/laravel/9.x/ja/validation.html <br>

### エラー表示

```
<form wire:submit.prevent="register">
  <label for="name">名前</label>
  <input id="name" type="text" wire:model="name"><br>
  @error('name')
    <div>{{ $message }}</div>
  @enderror
</form>
```

### ハンズオン

- `resources/views/livewire/register.blade.php`を編集<br>

```html:register.blade.php
<div>
  <form wire:submit.prevent="register">
    <label for="name">名前</label>
    <input id="name" type="text" wire:model="name" />
    <br />
    @error('name')
    <div>{{ $message }}</div>
    @enderror

    <label for="email">メールアドレス</label>
    <input type="text" id="email" wire:model="email" />
    <br />
    @error('email')
    <div>{{ $message }}</div>
    @enderror

    <label for="password">パスワード</label>
    <input type="password" id="password" wire:model="password" />
    <br />
    @error('password')
    <div>{{ $message }}</div>
    @enderror

    <button>登録する</button>
  </form>
</div>
```

### エラーの日本語対応

`lang/ja/validatation.php`<br>

```
'attributes' => [
  'email' => 'メールアドレス',
  'password' => 'パスワード,
];
```

### ハンズオン

- `lang/ja/validation.php`を編集<br>

```php:validation.php
<?php

return [
  /*
    |--------------------------------------------------------------------------
    | バリデーション言語行
    |--------------------------------------------------------------------------
    |
    | 以下の言語行はバリデタークラスにより使用されるデフォルトのエラー
    | メッセージです。サイズルールのようにいくつかのバリデーションを
    | 持っているものもあります。メッセージはご自由に調整してください。
    |
    */

  'accepted' => ':attributeを承認してください。',
  'accepted_if' => ':otherが:valueの場合、:attributeを承認してください。',
  'active_url' => ':attributeが有効なURLではありません。',
  'after' => ':attributeには、:dateより後の日付を指定してください。',
  'after_or_equal' => ':attributeには、:date以降の日付を指定してください。',
  'alpha' => ':attributeはアルファベットのみがご利用できます。',
  'alpha_dash' =>
    ':attributeはアルファベットとダッシュ(-)及び下線(_)がご利用できます。',
  'alpha_num' => ':attributeはアルファベット数字がご利用できます。',
  'array' => ':attributeは配列でなくてはなりません。',
  'before' => ':attributeには、:dateより前の日付をご利用ください。',
  'before_or_equal' => ':attributeには、:date以前の日付をご利用ください。',
  'between' => [
    'numeric' => ':attributeは、:minから:maxの間で指定してください。',
    'file' => ':attributeは、:min kBから、:max kBの間で指定してください。',
    'string' => ':attributeは、:min文字から、:max文字の間で指定してください。',
    'array' => ':attributeは、:min個から:max個の間で指定してください。',
  ],
  'boolean' => ':attributeは、trueかfalseを指定してください。',
  'confirmed' => ':attributeと、確認フィールドとが、一致していません。',
  'current_password' => 'パスワードが正しくありません。',
  'date' => ':attributeには有効な日付を指定してください。',
  'date_equals' => ':attributeには、:dateと同じ日付けを指定してください。',
  'date_format' => ':attributeは:format形式で指定してください。',
  'different' => ':attributeと:otherには、異なった内容を指定してください。',
  'digits' => ':attributeは:digits桁で指定してください。',
  'digits_between' => ':attributeは:min桁から:max桁の間で指定してください。',
  'dimensions' => ':attributeの図形サイズが正しくありません。',
  'distinct' => ':attributeには異なった値を指定してください。',
  'email' => ':attributeには、有効なメールアドレスを指定してください。',
  'ends_with' =>
    ':attributeには、:valuesのどれかで終わる値を指定してください。',
  'exists' => '選択された:attributeは正しくありません。',
  'file' => ':attributeにはファイルを指定してください。',
  'filled' => ':attributeに値を指定してください。',
  'gt' => [
    'numeric' => ':attributeには、:valueより大きな値を指定してください。',
    'file' => ':attributeには、:value kBより大きなファイルを指定してください。',
    'string' => ':attributeは、:value文字より長く指定してください。',
    'array' => ':attributeには、:value個より多くのアイテムを指定してください。',
  ],
  'gte' => [
    'numeric' => ':attributeには、:value以上の値を指定してください。',
    'file' => ':attributeには、:value kB以上のファイルを指定してください。',
    'string' => ':attributeは、:value文字以上で指定してください。',
    'array' => ':attributeには、:value個以上のアイテムを指定してください。',
  ],
  'image' => ':attributeには画像ファイルを指定してください。',
  'in' => '選択された:attributeは正しくありません。',
  'in_array' => ':attributeには:otherの値を指定してください。',
  'integer' => ':attributeは整数で指定してください。',
  'ip' => ':attributeには、有効なIPアドレスを指定してください。',
  'ipv4' => ':attributeには、有効なIPv4アドレスを指定してください。',
  'ipv6' => ':attributeには、有効なIPv6アドレスを指定してください。',
  'json' => ':attributeには、有効なJSON文字列を指定してください。',
  'lt' => [
    'numeric' => ':attributeには、:valueより小さな値を指定してください。',
    'file' => ':attributeには、:value kBより小さなファイルを指定してください。',
    'string' => ':attributeは、:value文字より短く指定してください。',
    'array' => ':attributeには、:value個より少ないアイテムを指定してください。',
  ],
  'lte' => [
    'numeric' => ':attributeには、:value以下の値を指定してください。',
    'file' => ':attributeには、:value kB以下のファイルを指定してください。',
    'string' => ':attributeは、:value文字以下で指定してください。',
    'array' => ':attributeには、:value個以下のアイテムを指定してください。',
  ],
  'max' => [
    'numeric' => ':attributeには、:max以下の数字を指定してください。',
    'file' => ':attributeには、:max kB以下のファイルを指定してください。',
    'string' => ':attributeは、:max文字以下で指定してください。',
    'array' => ':attributeは:max個以下指定してください。',
  ],
  'mimes' => ':attributeには:valuesタイプのファイルを指定してください。',
  'mimetypes' => ':attributeには:valuesタイプのファイルを指定してください。',
  'min' => [
    'numeric' => ':attributeには、:min以上の数字を指定してください。',
    'file' => ':attributeには、:min kB以上のファイルを指定してください。',
    'string' => ':attributeは、:min文字以上で指定してください。',
    'array' => ':attributeは:min個以上指定してください。',
  ],
  'multiple_of' => ':attributeには、:valueの倍数を指定してください。',
  'not_in' => '選択された:attributeは正しくありません。',
  'not_regex' => ':attributeの形式が正しくありません。',
  'numeric' => ':attributeには、数字を指定してください。',
  'password' => '正しいパスワードを指定してください。',
  'present' => ':attributeが存在していません。',
  'regex' => ':attributeに正しい形式を指定してください。',
  'required' => ':attributeは必ず指定してください。',
  'required_if' => ':otherが:valueの場合、:attributeも指定してください。',
  'required_unless' =>
    ':otherが:valuesでない場合、:attributeを指定してください。',
  'required_with' => ':valuesを指定する場合は、:attributeも指定してください。',
  'required_with_all' =>
    ':valuesを指定する場合は、:attributeも指定してください。',
  'required_without' =>
    ':valuesを指定しない場合は、:attributeを指定してください。',
  'required_without_all' =>
    ':valuesのどれも指定しない場合は、:attributeを指定してください。',
  'prohibited' => ':attributeは入力禁止です。',
  'prohibited_if' => ':otherが:valueの場合、:attributeは入力禁止です。',
  'prohibited_unless' => ':otherが:valueでない場合、:attributeは入力禁止です。',
  'prohibits' => 'attributeは:otherの入力を禁じています。',
  'same' => ':attributeと:otherには同じ値を指定してください。',
  'size' => [
    'numeric' => ':attributeは:sizeを指定してください。',
    'file' => ':attributeのファイルは、:sizeキロバイトでなくてはなりません。',
    'string' => ':attributeは:size文字で指定してください。',
    'array' => ':attributeは:size個指定してください。',
  ],
  'starts_with' =>
    ':attributeには、:valuesのどれかで始まる値を指定してください。',
  'string' => ':attributeは文字列を指定してください。',
  'timezone' => ':attributeには、有効なゾーンを指定してください。',
  'unique' => ':attributeの値は既に存在しています。',
  'uploaded' => ':attributeのアップロードに失敗しました。',
  'url' => ':attributeに正しい形式を指定してください。',
  'uuid' => ':attributeに有効なUUIDを指定してください。',

  /*
    |--------------------------------------------------------------------------
    | Custom バリデーション言語行
    |--------------------------------------------------------------------------
    |
    | "属性.ルール"の規約でキーを指定することでカスタムバリデーション
    | メッセージを定義できます。指定した属性ルールに対する特定の
    | カスタム言語行を手早く指定できます。
    |
    */

  'custom' => [
    '属性名' => [
      'ルール名' => 'カスタムメッセージ',
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション属性名
    |--------------------------------------------------------------------------
    |
    | 以下の言語行は、例えば"email"の代わりに「メールアドレス」のように、
    | 読み手にフレンドリーな表現でプレースホルダーを置き換えるために指定する
    | 言語行です。これはメッセージをよりきれいに表示するために役に立ちます。
    |
    */

  // 編集
  'attributes' => [
    'name' => '名前',
    'email' => 'メールアドレス',
    'password' => 'パスワード',
  ],
];
```
