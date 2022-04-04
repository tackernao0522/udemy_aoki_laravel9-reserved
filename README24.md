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

## 87 Blade コンポーネント(day, calendar-time)

### カレンダーの幅を固定

`resources/css/app.css`<br>

```css:app.css
.event-calendar {
  width: 1000px;
}
```

`views/calendar.blade.php`<br>

```php:calendar.blade.php
<div class="py-4">
  <div class="event-calendar border border-red-400 mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
      @livewire('calendar')
    </div>
  </div>
</div>
```

### livewire/calendar.blade.php

```php:calendar.blade.php
<div class="text-center text-sm">
  日付を選択してください。本日から最大30日先まで選択可能です。
</div>
<input id="calendar" class="block mt-1 mx-auto" type="text" 略 />
<div class="flex border border-green-400 mx-auto">
  <x-calendar-time /> // コンポーエンと作成 仮で直書き
  <x-day />
  <x-day />
  <x-day />
  <x-day />
  <x-day />
  <x-day />
  <x-day />
</div>
```

### components ファイル

`resoursec/views/components/calendar-time.blade.php`<br>

```php:calendar.blade.php
<div>
  <div class="py-1 px-2 border border-gray-200 text-center">日</div>
  <div class="py-1 px-2 border border-gray-200 text-center">曜日</div>
  <div class="py-1 px-2 h-8 border border-gray-200 text-center">10:00</div>
  <div class="py-1 px-2 h-8 border border-gray-200 text-center">10:30</div>
  ・・・〜20時まで
</div>
```

`components/day.blade.php`<br>
`calendar-time.blade.php`をコピーし幅調整<br>

```
<div class="w-32">
  ・・
</div>
```

### ハンズオン

- `resources/css/app.css`を編集<br>

```css:app.css
@import 'tailwindcss/base';
@import 'tailwindcss/components';
@import 'tailwindcss/utilities';

@import 'flatpickr/dist/flatpickr.css';

/* 追加 */
.event-calendar {
  width: 1000px;
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

    <div class="py-4">
        <div class="event-calendar border border-red-400 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @livewire('calendar')
            </div>
        </div>
    </div>
</x-calendar-layout>
```

- `resources/views/livewire/calendar.blade.php`を編集<br>

```php:calendar.blade.php
<div>
    // 追加
    <div class="text-center text-sm">
        日付を選択してください。本日から最大30日先まで選択可能です。
    </div>
    // ここまで
    <input
        id="calendar"
        class="block mt-1 mx-auto" // 編集
        type="text"
        name="calendar"
        value="{{ $currentDate }}"
        wire:change="getDate($event.target.value)"
    />

    // 追加
    <div class="flex border border-green-400 mx-auto">
        <x-calendar-time />
        <x-day />
        <x-day />
        <x-day />
        <x-day />
        <x-day />
        <x-day />
        <x-day />
    </div>
    // ここまで
    <div class="flex">
        @for ($day = 0; $day < 7; $day++)
            {{ $currentWeek[$day] }}
        @endfor
    </div>
    @foreach ($events as $event)
        {{ $event->start_date }}<br>
    @endforeach
</div>
```

- `$ touch resources/views/components/calendar-time.blade.php`を実行<br>

* `$ touch resources/views/components/day.blade.php`を実行<br>

- `resources/views/components/calendar-time.blade.php`を編集<br>

```php:calendar.blade.php
<div>
    <div class="py-1 px-2 border border-gray-200 text-center">日</div>
    <div class="py-1 px-2 border border-gray-200 text-center">曜日</div>
    <div class="py-1 px-2 h-8 border border-gray-200">10:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">10:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">11:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">11:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">12:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">12:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">13:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">13:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">14:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">14:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">15:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">15:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">16:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">16:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">17:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">17:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">18:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">18:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">19:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">19:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">20:00</div>
</div>
```

- `resources/views/components/day.blade.php`を編集<br>

```php:day.blade.php
<div class="w-32">
    <div class="py-1 px-2 border border-gray-200 text-center">日</div>
    <div class="py-1 px-2 border border-gray-200 text-center">曜日</div>
    <div class="py-1 px-2 h-8 border border-gray-200">10:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">10:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">11:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">11:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">12:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">12:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">13:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">13:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">14:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">14:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">15:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">15:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">16:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">16:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">17:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">17:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">18:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">18:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">19:00</div>
    <div class="py-1 px-2 h-8 border border-gray-200">19:30</div>
    <div class="py-1 px-2 h-8 border border-gray-200">20:00</div>
</div>
```

- `resources/views/livewire/calendar.blade.php`を編集<br>

```php:calendar.blade.php
<div>
    <div class="text-center text-sm">
        日付を選択してください。本日から最大30日先まで選択可能です。
    </div>
    <input
        id="calendar"
        // 編集 mb-2を追加
        class="block mt-1 mb-2 mx-auto"
        type="text"
        name="calendar"
        value="{{ $currentDate }}"
        wire:change="getDate($event.target.value)"
    />

    <div class="flex border border-green-400 mx-auto">
        <x-calendar-time />
        <x-day />
        <x-day />
        <x-day />
        <x-day />
        <x-day />
        <x-day />
        <x-day />
    </div>
    <div class="flex">
        @for ($day = 0; $day < 7; $day++)
            {{ $currentWeek[$day] }}
        @endfor
    </div>
    @foreach ($events as $event)
        {{ $event->start_date }}<br>
    @endforeach
</div>
```
