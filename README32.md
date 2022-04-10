## 110 メニュー追加

### ログインしていないときの対応

### メニューにログインなど追加

`resources/views/welcome.blade.php`の

`@if(Route::has('login')) 〜 @endif`を `layouts/calendar.blade.php`にコピー<br>

ログイン時は`dashboard`と表示される<br>

### ハンズオン

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
        // 追加
        @if (Route::has('login'))
            <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="text-sm text-gray-700 dark:text-gray-500 underline">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
                    @endif
                @endauth
            </div>
        @endif
        // ここまで

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

## 111 ログインしていない状態からのリダイレクト

### 未ログイン時はリダイレクト

`middleware('auth')`を使うと未ログイン時は login ページに移動<br>

`routes/web.php`<br>

```php:web.php
Route::middleware('can:user-higher')
  ->group(function() {
    略
    // Route::get('/{id}', [ReservationController::class, 'detail'])->name('events.detail);
    Route::post('/{id}', [ReservationController::class, 'reserve'])->name('events.reserve');
  });

  Route::middleware('auth')
    ->get('/{id}', [ReservationController::class, 'detail'])->name('events.detail');
```

### ハンズオン

- `routes/web.php`を編集<br>

```php:web.php
<?php

use App\Http\Controllers\AlpineTestController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LivewireTestController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return view('calendar');
});

// Route::middleware(['auth:sanctum', 'verified'])
//     ->get('/dashboard', function () {
//         return view('dashboard');
//     })
//     ->name('dashboard');

Route::prefix('manager')
  ->middleware('can:manager-higher')
  ->group(function () {
    Route::get('events/past', [EventController::class, 'past'])->name(
      'events.past'
    );
    Route::resource('events', EventController::class);
  });

Route::middleware('can:user-higher')->group(function () {
  Route::get('/dashboard', [ReservationController::class, 'dashboard'])->name(
    'dashboard'
  );
  Route::get('mypage', [MyPageController::class, 'index'])->name(
    'mypage.index'
  );
  Route::post('mypage/{id}', [MyPageController::class, 'cancel'])->name(
    'mypage.cancel'
  );
  Route::get('mypage/{id}', [MyPageController::class, 'show'])->name(
    'mypage.show'
  );
  // 削除
  // Route::get('/{id}', [ReservationController::class, 'detail'])->name('events.detail');
  Route::post('/{id}', [ReservationController::class, 'reserve'])->name(
    'events.reserve'
  );
});

// 追加
Route::middleware('auth')
  ->get('/{id}', [ReservationController::class, 'detail'])
  ->name('events.detail');

// localhost/livewire-test/index
Route::controller(LivewireTestController::class)
  ->prefix('livewire-test')
  ->name('livewire-test.')
  ->group(function () {
    Route::get('index', 'index')->name('index');
    Route::get('register', 'register')->name('register');
  });

Route::get('alpine-test/index', [AlpineTestController::class, 'index']);
```

### 補足: 認証・リダイレクト関連

`app/Http/Middleware`<br>

`Authenticate.php` リダイレクト先<br>

`RedirectIfAuthenticated.php` 認証されていた時のリダイレクト先<br>

`app/Providers/RouteServiceProvider.php`<br>
ホーム(dashboard)などを設定<br>

### ハンズオン

- `resources/views/calendar.blade.php`を編集<br>

```php:calendar.blade.php
<x-calendar-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            イベントカレンダー
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="event-calendar mx-auto sm:px-6 lg:px-8">
            // borderを削除
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
    <div class="text-center text-sm">
        日付を選択してください。本日から最大30日先まで選択可能です。
    </div>
    <input id="calendar" class="block mt-1 mb-2 mx-auto" type="text" name="calendar" value="{{ $currentDate }}"
        wire:change="getDate($event.target.value)" />

    // border greenを削除
    <div class="flex mx-auto">
        <x-calendar-time />
        @for ($i = 0; $i < 7; $i++)
            <div class="w-32">
                <div class="py-1 px-2 border border-gray-200 text-center">{{ $currentWeek[$i]['day'] }}</div>
                <div class="py-1 px-2 border border-gray-200 text-center">{{ $currentWeek[$i]['dayOfWeek'] }}</div>
                @for ($j = 0; $j < 21; $j++)
                    @if ($events->isNotEmpty())
                        @if (!is_null($events->firstWhere('start_date', $currentWeek[$i]['checkDay'] . ' ' . \Constant::EVENT_TIME[$j])))
                            @php
                                $eventId = $events->firstWhere('start_date', $currentWeek[$i]['checkDay'] . ' ' . \Constant::EVENT_TIME[$j])->id;
                                $eventName = $events->firstWhere('start_date', $currentWeek[$i]['checkDay'] . ' ' . \Constant::EVENT_TIME[$j])->name;
                                $eventInfo = $events->firstWhere('start_date', $currentWeek[$i]['checkDay'] . ' ' . \Constant::EVENT_TIME[$j]);
                                $eventPeriod = \Carbon\Carbon::parse($eventInfo->start_date)->diffInMinutes($eventInfo->end_date) / 30 - 1;
                            @endphp
                            <a href="{{ route('events.detail', ['id' => $eventId]) }}">
                                <div class="py-1 px-2 h-8 border border-gray-200 text-xs bg-blue-100">
                                    {{ $eventName }}
                                </div>
                            </a>
                            @if ($eventPeriod > 0)
                                @for ($k = 0; $k < $eventPeriod; $k++)
                                    <div class="py-1 px-2 h-8 border border-gray-200 bg-blue-100"></div>
                                @endfor
                                @php $j += $eventPeriod @endphp
                            @endif
                        @else
                            <div class="py-1 px-2 h-8 border border-gray-200 text-center"></div>
                        @endif
                    @else
                        <div class="py-1 px-2 h-8 border border-gray-200 text-center"></div>
                    @endif
                @endfor
            </div>
        @endfor
    </div>
</div>
```
