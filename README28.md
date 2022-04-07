## 97 予約可能な人数の表示

### 予約可能な人数

予約可能な人数 = 最大定員 - 予約済みの人数(キャンセルを除く)<br>

```php:ReservationController.php
public function detail($id)
{
  $event = Event::findOrFail($id);
  $reservedPeople = DB::table('reservations')
    ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
    ->whereNull('canceled_date')
    ->groupBy('event_id')
    ->having('event_id', $event->id) // havingはgroupByの後に検索
    ->first();

  if(!is_null($reservedPeople)) {
    $reservablePeople = $event->max_people - $reservedPeople->number_of_people;
  } else {
    $reservablePeople = $event->max_people;
  }

  return view('events-detail', compact('event', 'reservablePeople'));
}
```

### ビュー側

```php:event-detail.blade.php
<x-jet-label for="reserved_people" value="予約人数" />
<select name="reserved_people">
  @for ($i = 1; $i <= $reservablePeople; $i++)
    <option value="{{ $i }}">{{ $i }}</option>
  @endfor
</select>
```

- `app/Http/Controllers/ReservationController.php`を編集<br>

```php:ReservationController.php
<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
  public function dashboard()
  {
    return view('dashboard');
  }

  public function detail($id)
  {
    $event = Event::findOrFail($id);

    // 追加
    $reservedPeople = DB::table('reservations')
      ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
      ->whereNull('canceled_date')
      ->groupBy('event_id')
      ->having('event_id', $event->id)
      ->first();

    if (!is_null($reservedPeople)) {
      $reservablePeople =
        $event->max_people - $reservedPeople->number_of_people;
    } else {
      $reservablePeople = $event->max_people;
    }
    // ここまで

    // 編集
    return view('event-detail', compact('event', 'reservablePeople'));
  }
}
```

- `resources/views/event-detial.blade.php`を編集<br>

```php:event-detail.blade.php
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            イベント詳細
        </h2>
    </x-slot>

    <div class="pt-4 pb-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="max-w-2xl py-4 mx-auto">
                    <x-jet-validation-errors class="mb-4" />

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="get" action="{{ route('events.edit', $event->id) }}">
                        <div>
                            <x-jet-label for="event_name" value="イベント名" />
                            {{ $event->name }}
                        </div>
                        <div class="mt-4">
                            <x-jet-label for="information" value="イベント詳細" />
                            {!! nl2br(e($event->information)) !!}
                        </div>

                        <div class="md:flex justify-between">
                            <div class="mt-4">
                                <x-jet-label for="event_date" value="イベント日付" />
                                {{ $event->eventDate }}
                            </div>

                            <div class="mt-4">
                                <x-jet-label for="start_time" value="開始時間" />
                                {{ $event->startTime }}
                            </div>

                            <div class="mt-4">
                                <x-jet-label for="end_time" value="終了時間" />
                                {{ $event->endTime }}
                            </div>
                        </div>
                        <div class="md:flex justify-between items-end">
                            <div class="mt-4">
                                <x-jet-label for="max_people" value="定員数" />
                                {{ $event->max_people }}
                            </div>
                            // 追加
                            <div class="mt-4">
                                <x-jet-label for="reserved_people" value="予約人数" />
                                <select name="reserved_people">
                                    @for ($i = 1; $i <= $reservablePeople; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            // ここまで
                            <x-jet-button class="ml-4">
                                予約する
                            </x-jet-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

## 98 イベント予約処理 その 1

### 予約の準備

ビュー `events-detail.blade.php`<br>

```php:events-detial.blade.php
<form method="post" action="{{ route('events.reserve', ['event' => $event->id]) }}">
<input type="hidden" name="id" value="{{ $event->id }}">
```

ルート `routes/web.php`<br>

```php:web.php
Route::post('/{id}', [ReservationController::class, 'reserve'])->name('events.reserve);
```

### ハンズオン

- `routes/web.php`を編集<br>

```php:web.php
<?php

use App\Http\Controllers\AlpineTestController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LivewireTestController;
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
        Route::get('events/past', [EventController::class, 'past'])->name('events.past');
        Route::resource('events', EventController::class);
    });

Route::middleware('can:user-higher')->group(function () {
    Route::get('/dashboard', [ReservationController::class, 'dashboard'])->name('dashboard');
    Route::get('/{id}', [ReservationController::class, 'detail'])->name('events.detail');
    // 追加
    Route::post('/{id}', [ReservationController::class, 'reserve'])->name('events.reserve');
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

+ `resources/views/event-detail.blade.php`を編集<br>

```php:event-detail.blade.php
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            イベント詳細
        </h2>
    </x-slot>

    <div class="pt-4 pb-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="max-w-2xl py-4 mx-auto">
                    <x-jet-validation-errors class="mb-4" />

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    // 編集
                    <form method="post" action="{{ route('events.reserve', $event->id) }}">
                        @csrf
                        <div>
                            <x-jet-label for="event_name" value="イベント名" />
                            {{ $event->name }}
                        </div>
                        <div class="mt-4">
                            <x-jet-label for="information" value="イベント詳細" />
                            {!! nl2br(e($event->information)) !!}
                        </div>

                        <div class="md:flex justify-between">
                            <div class="mt-4">
                                <x-jet-label for="event_date" value="イベント日付" />
                                {{ $event->eventDate }}
                            </div>

                            <div class="mt-4">
                                <x-jet-label for="start_time" value="開始時間" />
                                {{ $event->startTime }}
                            </div>

                            <div class="mt-4">
                                <x-jet-label for="end_time" value="終了時間" />
                                {{ $event->endTime }}
                            </div>
                        </div>
                        <div class="md:flex justify-between items-end">
                            <div class="mt-4">
                                <x-jet-label for="max_people" value="定員数" />
                                {{ $event->max_people }}
                            </div>
                            <div class="mt-4">
                                <x-jet-label for="reserved_people" value="予約人数" />
                                <select name="reserved_people">
                                    @for ($i = 1; $i <= $reservablePeople; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            // 追加
                            <input type="hidden" name="id" value="{{ $event->id }}">
                            <x-jet-button class="ml-4">
                                予約する
                            </x-jet-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

### 予約前に再度人数チェック

イベント詳細画面を表示している間に他ユーザーが予約する可能性があるので保存前に再度予約人数チェック(楽観的ロックに近い)<br>

保存にはイベント id, ユーザー id, 予約人数の３つの情報が必要<br>

必要コードを読み込む<br>

```php:ReservationController.php
use Illuminate\Support\Facades\Auth;
```

### ReservationController その 1

```php:ReservationController.php
public function reserve(Request $request)
{
  $event = Event::findOrFail($request->id);
  $resevedPeople = DB::table('reservatons')
    ->slect('event_id', DB::raw('sum(number_of_people) as number_of_people'))
    ->whereNull('canceled_date')
    ->groupBy('event_id')
    ->having('event_id', $request->id)
    ->first();
}
```

### ReservationController その 2

```php:ReservationController.php
$event = Event::findOrFail($request->id);

$reservedPeople = DB::table('reservations')
  ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
  ->whereNull('canceled_date')
  ->groupBy('event_id')
  ->having('event_id', $event->id)
  ->first();

// $reservedPeopleが空か、最大 >= 予約人数 + 入力された人数 なら予約可能
if (
  is_null($reservedPeople) ||
  $event->max_people >=
    $reservedPeople->numbar_of_people + $request->reserved_people
) {
  Reservation::create([
    'user_id' => Auth::id(),
    'event_id' => $request->id,
    'number_of_people' => $request->reserved_people,
  ]);
  session()->flash('status', '登録OKです。');

  return to_route('dashboard');
} else {
  session()->flash('status', 'この人数は予約できません。');

  return view('dashboard');
}
```

### Dashboard にセッション表示追加

ビュー `dashboard.blade.php`<br>

```php:dashboard.blade.php
@if (session('status'))
  <div class="mb-4 font-medium text-sm text-green-600">
    {{ session('status') }}
  </div>
@endif
@livewire('calendar')
```

Google Chrome のシークレットモードを使って確認してみる<br>

### ハンズオン

- `app/Http/Controllers/ReservationController.php`を編集<br>

```php:ReservationController.php
<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
  public function dashboard()
  {
    return view('dashboard');
  }

  public function detail($id)
  {
    $event = Event::findOrFail($id);

    $reservedPeople = DB::table('reservations')
      ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
      ->whereNull('canceled_date')
      ->groupBy('event_id')
      ->having('event_id', $event->id)
      ->first();

    if (!is_null($reservedPeople)) {
      $reservablePeople =
        $event->max_people - $reservedPeople->number_of_people;
    } else {
      $reservablePeople = $event->max_people;
    }

    return view('event-detail', compact('event', 'reservablePeople'));
  }

  // 追加
  public function reserve(Request $request)
  {
    $event = Event::findOrFail($request->id);

    $reservedPeople = DB::table('reservations')
      ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
      ->whereNull('canceled_date')
      ->groupBy('event_id')
      ->having('event_id', $event->id)
      ->first();

    if (
      is_null($reservedPeople) ||
      $event->max_people >=
        $reservedPeople->number_of_people + $request->reserved_people
    ) {
      Reservation::create([
        'user_id' => Auth::id(),
        'event_id' => $request->id,
        'number_of_people' => $request->reserved_people,
      ]);
      session()->flash('status', '登録OKです。');

      return to_route('dashboard');
    } else {
      session()->flash('status', 'この人数は予約できません。');

      return view('dashboard');
    }
  }
}
```

## 99 イベント予約処理 その 2

- `resources/views/dashboard.blade.php`を編集<br>

```php:dashboard.blade.php
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            イベントカレンダー
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="event-calendar mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                // 追加
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif
                // ここまで
                @livewire('calendar')
            </div>
        </div>
    </div>
    <script src="{{ mix('js/flatpickr.js') }}"></script>
</x-app-layout>
```
