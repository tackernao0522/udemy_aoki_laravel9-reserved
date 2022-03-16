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
