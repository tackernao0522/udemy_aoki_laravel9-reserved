## 100 満員時の表示変更

### 満員時の表記

```php:event-detail.blade.php
@if($reservablePeople <= 0)
  <span class="text-xs">このイベントは満員です。</span>
@else
  <x-jet-label for="reserved_people" value="予約人数" />
  <select name="reserved_people">
    @for($i = 1; $i <= $reservablePeople; $i++)
      <option value="{{ $i }}">{{ $i }}</option>
    @endfor
  </select>
@endif
```

### ハンズオン

- `resources/views/event-detail.php`を編集<br>

```php:event-detail.php
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
                                // 編集
                                @if ($reservablePeople <= 0)
                                    <span class="text-red-500 text-xs">このイベントは満員です。</span>
                                @else
                                    <x-jet-label for="reserved_people" value="予約人数" />
                                    <select name="reserved_people">
                                        @for ($i = 1; $i <= $reservablePeople; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                @endif
                                // ここまで
                            </div>
                            <input type="hidden" name="id" value="{{ $event->id }}">
                            // 編集
                            @if ($reservablePeople > 0)
                                <x-jet-button class="ml-4">
                                    予約する
                                </x-jet-button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

### 改善案

- イベント満員時はカレンダー表示色を変える<br>

* イベント予約時にメール送信<br>

- キャンセル待ち機能(キャンセルがでたらメール通知)<br>

## 101 マイページの準備、コントローラ

### マイページ

ログインしているユーザーが予約しているイベントの一覧を表示(キャンセル分は表示しない)<br>

- 今日を含む未来のイベント一覧<br>

* 過去のイベント一覧<br>

### マイページ準備

ルーティング<br>

```php:web.php
use App\Http\Controllers\MyPageController;

Route::middleware('can:user-higher')
  ->group(function() {
      略
      Route::get('mypage', [MyPageController::class, 'index'])->name('mypage.index);
  })
```

### コントローラ作成

`php artisan make:controller MyPageController`<br>

### ハンズオン

- `$ php artisan make:controller MyPageController.php`を実行<br>

* `routes/web.php`を編集<br>

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
  // 追加
  Route::get('mypage', [MyPageController::class, 'index'])->name(
    'mypage.index'
  );
  Route::get('/{id}', [ReservationController::class, 'detail'])->name(
    'events.detail'
  );
  Route::post('/{id}', [ReservationController::class, 'reserve'])->name(
    'events.reserve'
  );
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

### MyPageController(例)

```php:MyPageController.php
public function index()
{
    $user = User::findOrFail(Auth::id());
    $events = $user->events; // イベント一覧を取得
    $fromTodayEvents = MyPageService::reservedEvent($events, 'fromToday');
    $pastEvents = MypPageService::reservedEvent($events, 'past');
    // dd($events, $fromTodayEvents, $pastEvents);

    return view('mypage/index', compact('fromTodayEvents', 'pastEvents'));
}
```

### ハンズオン

- `$ touch app/Services/MyPageService.php`を実行<br>

* `app/Services/MyPageService.php`を編集<br>

```php:MyPageService.php
<?php

namespace App\Services;

class MyPageService
{
  public static function reservedEvent($events, $string)
  {
    $reservedEvents = [];
    if ($string === 'fromToday') {
    } elseif ($string === 'past') {
    }

    return $reservedEvents;
  }
}
```

- `app/Http/Controllers/MyPageController.php`を編集<br>

```php:MyPageController.php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MyPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyPageController extends Controller
{
  public function index()
  {
    $user = User::findOrFail(Auth::id());
    $events = $user->events;
    $fromTodayEvents = MyPageService::reservedEvent($events, 'fromToday');
    $pastEvents = MyPageService::reservedEvent($events, 'past');
    dd($events, $fromTodayEvents, $pastEvents);
  }
}
```

## 102 予約したイベントの取得(今日を含む未来と過去それぞれ)

### Services/MyPageService

```php:MyService.php
namespace App\Services;

use Carbon\Carbon;

public static function reservedEvent($events, $string)
{
    $reservedEvents = [];
    if($string === 'fromToday') {
        foreach($events->sortBy('start_date') as $event)  { // 昇順
            if(is_null($event->pivot->canceled_date) && $event->start_date >= Carbon::now()->format('Y-m-d 00:00:00')) {
                $eventInfo = [
                    'id' => $event->id,
                    'name' => $event->name,
                    'start_date' => $event->start_date,
                    'end_date' => $event->end_date,
                    'number_of_people' => $event->pivot->number_of_people,
                ];
                array_push($reservedEvents, $eventInfo;);
            }
        }
    }
    if($string === 'past') {
        foreach($events->sortByDesc('start_date') as $evnet) { // 降順
            if(is_null($event->pivot->canceled_date) && $event->start_date < Carbon::now()->format('Y-m-d 00:00:00')) {
                $eventInfo = [
                    'id' => $event->id,
                    'name' => $event->name,
                    'start_date' => $event->start_date,
                    'end_date' => $event->end_date,
                    'number_of_people' => $event->pivot->number_of_people,
                ];
                array_push($reservedEvents, $eventInfo);
            }
        }
    }
}
```

### ハンズオン

- `app/Services/MyPageService.php`を編集<br>

```php:MyPageService.php
<?php

namespace App\Services;

use Carbon\Carbon;

class MyPageService
{
<?php

namespace App\Services;

use Carbon\Carbon;

class MyPageService
{
  public static function reservedEvent($events, $string)
  {
    $reservedEvents = [];
    if ($string === 'fromToday') {
      foreach ($events->sortBy('start_date') as $event) {
        if (
          is_null($event->pivot->canceled_date) && $event->start_date >= Carbon::now()
          ->format('Y-m-d 00:00:00')
        ) {
          $eventInfo = [
            'id' => $event->id,
            'name' => $event->name,
            'start_date' => $event_date,
            'number_of_people' => $event->pivot->numbar_of_people,
          ];
          array_push($reservedEvents, $eventInfo);
        }
      }
    } elseif ($string === 'past') {
      foreach ($events->sortByDesc('start_date') as $event) {
        if (
          is_null($event->pivot->canceled_date) && $event->start_date < Carbon::now()
          ->format('Y-m-d 00:00:00')
        ) {
          $eventInfo = [
            'id' => $event->id,
            'name' => $event->name,
            'start_date' => $event->start_date,
            'end_date' => $event->end_date,
            'number_of_people' => $event->pivot->number_of_people,
          ];
          array_push($reservedEvents, $eventInfo);
        }
      }
    }

    return $reservedEvents;
  }
}
```

- `app/Http/Controllers/MyPageController.php`を編集<br>

```php:MyPageController.php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MyPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyPageController extends Controller
{
  public function index()
  {
    $user = User::findOrFail(Auth::id());
    $events = $user->events;
    $fromTodayEvents = MyPageService::reservedEvent($events, 'fromToday');
    $pastEvents = MyPageService::reservedEvent($events, 'past');
    // dd($user, $events, $fromTodayEvents, $pastEvents);

    return view('mypage/index', compact('fromTodayEvents', 'pastEvents'));
  }
}
```

## 103 マイページ index

### maypage/index.blade.php

manager/events/index.blade.php をコピー<br>

以前はコレクション型だったので \$event->id をいう書き方<br>

今回は連想配列型なので \$event['id']という書き方になる<br>

### ハンズオン

- `$ mkdir resources/views/mypage && cp resources/views/manager/events/index.blade.php resources/views/mypage/index.blade.php`を実行<br>

* `resources/views/mypage/index.blade.php`を編集<br>

```php:index.blade.php
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            予約済みのイベント一覧
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <section class="text-gray-600 body-font">
                    <div class="container px-5 py-4 mx-auto">
                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div class="w-full mx-auto overflow-auto">
                            <table class="table-auto w-full text-left whitespace-no-wrap">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            イベント名</th>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            開始日時</th>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            終了日時</th>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            予約人数</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($fromTodayEvents as $event)
                                        <tr>
                                            <td class="text-blue-500 px-4 py-3">
                                                <a href="{{ route('mypage.show', $event['id']) }}">
                                                    {{ $event['name'] }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3">{{ $event['start_date'] }}</td>
                                            <td class="px-4 py-3">{{ $event['end_date'] }}</td>
                                            <td class="px-4 py-3">
                                                {{ $event['number_of_people'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <h2 class="text-center py-2">過去のイベント一覧</h2>
                <section class="text-gray-600 body-font">
                    <div class="container px-5 py-4 mx-auto">
                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div class="w-full mx-auto overflow-auto">
                            <table class="table-auto w-full text-left whitespace-no-wrap">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            イベント名</th>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            開始日時</th>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            終了日時</th>
                                        <th
                                            class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                            予約人数</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pastEvents as $event)
                                        <tr>
                                            <td class="text-blue-500 px-4 py-3">
                                                <a href="{{ route('mypage.show', $event['id']) }}">
                                                    {{ $event['name'] }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3">{{ $event['start_date'] }}</td>
                                            <td class="px-4 py-3">{{ $event['end_date'] }}</td>
                                            <td class="px-4 py-3">
                                                {{ $event['number_of_people'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
```

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
  // 追加
  Route::get('mypage/{id}', [MyPageController::class, 'show'])->name(
    'mypage.show'
  );
  Route::get('/{id}', [ReservationController::class, 'detail'])->name(
    'events.detail'
  );
  Route::post('/{id}', [ReservationController::class, 'reserve'])->name(
    'events.reserve'
  );
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
