## 37 x-data の影響範囲(x-show, x-on:click)

### x-data ・・ HTML のタグ内に追記<br>

`<div x-data={ open:false }></div>`など<br>

x-data 属性のあるタグの中で x-data を参照できる<br>

```
<div x-data={ open:false }>
  <div x-show="open">openがtrue</div> // ok
</div>

<div x-data={ open:false }>
</div>

<div x-show="!open">openがfalse</div> // ng うまく動かない
```

### ハンズオン

- `resources/views/alpine-test/index.blade.php`を編集<br>

```html:index.blade.php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap"
    />

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" />

    @livewireStyles

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
  </head>

  <body class="font-sans antialiased">
    <x-jet-banner />

    <div class="min-h-screen bg-gray-100">
      @livewire('navigation-menu')

      <!-- Page Heading -->
      @if (isset($header))
      <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          {{ $header }}
        </div>
      </header>
      @endif

      <!-- Page Content -->
      <main>
        Alpineテスト
        <div x-data="{ open:true}">
          <div x-show="open">openがtrue</div>
          <div x-show="!open">openがfalse</div>
          <button
            class="px-4 py-2 bg-blue-400 text-white"
            x-on:click="open = !open"
          >
            ボタン
          </button>
        </div>
        {{-- 効かなくなる --}} {{--
        <div x-show="open">openがtrue</div>
        <div x-show="!open">openがfalse</div>
        --}}
      </main>
    </div>

    @stack('modals') @livewireScripts
  </body>
</html>
```

## 38 イベント

### Alpine.js のイベント

https://alpinejs.dev/essentials/events <br>

https://alpinejs.dev/directives/on <br>

x-on:click="" 省略形 @click=""<br>

オプション(修飾子)<br>
.prevent HTML デフォルト動作を防ぐ<br>
.stop イベント伝搬を防ぐ<br>
.away 対象タグ以外をクリックしたら
.window Window オブジェクトにイベントリスナー登録<br>
(JavaScript は全てオブジェクト(ブラウザもオブジェクト)ロード、スクロール、ブラウザサイズなどを全てイベントとして検知可能)<br>

### コードを読んでみる

`layouts/app.blade.php`の<br>
`@livewire('navigation-menu')`<br>

(vendor/laravel/jetstream/src/JetstreamServiceProvider.php でコンポーネント登録されている)<br>

実態は `views/navigation-menu.blade.php`<br>
