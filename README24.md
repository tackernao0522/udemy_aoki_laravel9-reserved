## 86 Calendar レイアウト

### レイアウトを作成

`resources/layouts`<br>

`app.blade.php`をコピーして`calendar.blade.php`を作成<br>

ナビゲーションを削除<br>

`{{ mix('js/flatpickr.js') }}`を追加<br>

### View/Components 作成

`app/View/Components/`<br>

AppLayout.php をコピー CalendarLayout.php を作成<br>

```
public function render()
{
  return view('layouts.calendar');
}
```

### ハンズオン

- `$ cp resources/views/layouts/app.blade.php resources/views/layouts/calendar.blade.php`を実行<br>

- `resources/views/layouts/calendar.blade.php`を編集<br>

```php:calendar.blade.php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    @livewireStyles

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>

<body class="font-sans antialiased">

    <div class="min-h-screen bg-gray-100">

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
            {{ $slot }}
        </main>
    </div>
    <script src="{{ mix('js/flatpickr.js') }}"></script>

    @livewireScripts
</body>

</html>
```

- `$ cp app/View/Components/AppLayout.php app/View/Components/CalendarLayout.php`を実行<br>

* `app/View/Components/CalendarLayout.php`を編集<br>

```php:CalendarLayout.php
<?php

namespace App\View\Components;

use Illuminate\View\Component;

// 編集
class CalendarLayout extends Component
{
  /**
   * Get the view / contents that represents the component.
   *
   * @return \Illuminate\View\View
   */
  public function render()
  {
    // 編集
    return view('layouts.calendar');
  }
}
```

- `resources/views/calendar.blade.php`を編集<br>

```php:calendar.blade.php
<x-calendar-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            イベントカレンダー
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="max-w-2xl py-4 mx-auto">
                    @livewire('calendar')
                </div>
            </div>
        </div>
    </div>
</x-calendar-layout>
```
