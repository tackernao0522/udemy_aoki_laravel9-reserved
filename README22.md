# セクション 7: 利用者側その 1 予約カレンダー

## 80 予約カレンダーの準備

ログインなしで表示可能<br>
予約時はログイン（会員登録）必要<br>
週間カレンダー<br>
選択日を含む 7 日間を表示<br>
10 時〜20 時 30 分単位(Flatpickr 設定)<br>
livewire で作成<br>

### calendar.blade.php

ルートの welecome を calendar に変更<br>

`layouts/app.blade.php`から livewire, mix()などをコピー<br>

`flatpickr`は`events/create.blade.php`からコピー<br>

### resources/js/flatpickr.js

```js:flatpickr.js
flatpickr('#calendar', {
  locale: japanese,
  minDate: 'today',
  maxDate: new Date().fp_incr(30),
})

const setting = {
  locale: Japanse,
  enableTime: true,
  noCalendar: true,
  dateFormat: 'H:i',
  time_24hr: true,
  minuteIncrement: 30,
}
```

### ハンズオン

- `routes/web.php`を編集<br>

```php:web.php
<?php

use App\Http\Controllers\AlpineTestController;
use App\Http\Controllers\EventController;
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
  return view('calendar'); // calendarに変更
});

Route::middleware(['auth:sanctum', 'verified'])
  ->get('/dashboard', function () {
    return view('dashboard');
  })
  ->name('dashboard');

Route::prefix('manager')
  ->middleware('can:manager-higher')
  ->group(function () {
    Route::get('events/past', [EventController::class, 'past'])->name(
      'events.past'
    );
    Route::resource('events', EventController::class);
  });

Route::middleware('can:user-higher')->group(function () {
  Route::get('index', function () {
    dd('user');
  });
});

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

- `$ touch resources/views/calendar.blade.php`を実行<br>

* `resources/views/calendar.blade.php`を編集<br>

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
    カレンダー
    <x-jet-input id="calendar" class="block mt-1 w-full" type="text" name="calendar" />
    <script src="{{ mix('js/flatpickr.js') }}"></script>
    @livewireScripts
</body>

</html>
```

- `resources/js/flatpickr.js`を編集<br>

```js:flatpickr.js
import flatpickr from 'flatpickr'
import { Japanese } from 'flatpickr/dist/l10n/ja.js'

// 日本語設定、今日以降選択、30日間
flatpickr('#event_date', {
  locale: Japanese,
  minDate: 'today',
  maxDate: new Date().fp_incr(30),
})

// 追加
flatpickr('#calendar', {
  locale: Japanese,
  minDate: 'today',
  maxDate: new Date().fp_incr(30),
})

const setting = {
  locale: Japanese,
  enableTime: true,
  noCalendar: true,
  dateFormat: 'H:i',
  time_24hr: true,
  minTime: '10:00',
  maxTime: '20:00',
  // 追加
  minuteIncrement: 30,
}

flatpickr('#start_time', setting)

flatpickr('#end_time', setting)
```

## 81 livewire calendar の作成

### Livewire でカレンダー

php artisan make:livewire Calendar<br>

`app/Http/Livewire/Calendar.php`<br>

`resources/views/livewire/calendar.blade.php`が生成<br>

### app/Http/Livewire/Calendar.php

```php:Calendar.php
use Carbon\Carbon;

class Calendar extends Component
{
  public $currentDate;
  public $day;
  public $currentWeek;

  public function mount()
  {
    $this->currentDate = Carbon::tody();
    $this->currentWeek = [];

    for ($i = 0; $i < 7; $i++) {
      $this->day = Carbon::tody()
        ->addDays($i)
        ->format('m月d日');
      array_push($this->currentWeek, $this->day);
    }
    // dd($this->currentWeek);
  }
}
```

### livewire/calendar.blade.php

```php:calendar.blade.php
<div>
  <x-jet-input id="calendar" class="block mt-1 w-full" type="text" name="calendar" />
    {{ $currentDate }}
    <div class="flex">
      @for ($day = 0; $day < 7; $day++)
        {{ $currentWeek[$day] }}
      @endfor
    </div>
</div>
```

`views/calendar.php`<br>

```php:calendar.blade.php
@livewire('calendar'); // コンポーネント読み込み
```

### ハンズオン

- `$ php artisan make:livewire Calendar`を実行<br>

* `app/Http/Livewire/Calendar.php`を編集<br>

```php:Calendar.php
<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Livewire\Component;

class Calendar extends Component
{
  public $currentDate; // 現在の日付
  public $day;
  public $currentWeek; // 一週間分

  public function mount()
  {
    $this->currentDate = Carbon::today();
    $this->currentWeek = [];

    for ($i = 0; $i < 7; $i++) {
      $this->day = Carbon::today()
        ->addDays($i)
        ->format('m月d日');
      array_push($this->currentWeek, $this->day);
    }
    dd($this->currentWeek);
  }

  public function render()
  {
    return view('livewire.calendar');
  }
}
```

- `resources/views/calendar.blade.php`を編集<br>

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
    // 編集
    @livewire('calendar')
    <script src="{{ mix('js/flatpickr.js') }}"></script>
    @livewireScripts
</body>

</html>
```

- `resources/views/livewire/calendar.blade.php`を編集<br>

```php:calendar.blade.php
<div>
    カレンダー
    <x-jet-input id="calendar" class="block mt-1 w-full" type="text" name="calendar" />
</div>
```

- http://localhost/ にアクセスすると<br>

```:browser
^ array:7 [▼
  0 => "03月31日"
  1 => "04月01日"
  2 => "04月02日"
  3 => "04月03日"
  4 => "04月04日"
  5 => "04月05日"
  6 => "04月06日"
]
```

- `resources/views/livewire/calendar.blade.php`を編集<br>

```php:calendar.blade.php
<div>
    カレンダー
    <x-jet-input id="calendar" class="block mt-1 w-full" type="text" name="calendar" />
    {{ $currentDate }}
    <div class="flex">
        @for ($day = 0; $day < 7; $day++)
            {{ $currentWeek[$day] }}
        @endfor
    </div>
</div>
```
