## 104 マイページ show

### マイページ詳細 コントローラ

Event と Reservation を use で読み込み<br>

※ 同ユーザーが同イベントを予約する可能性があるので追って対策をする<br>

```php:MypageController.php
public function show($id)
{
  $event = Event::findOrFail($id);
  $reservation = Reservation::where('user_id', '=', Auth::id())
    ->whwere('event_id', '=', $id)
    ->first();
    // dd($reservation)

    return view('mypage/show', compact('event', 'reservation'));
}
```

ビュー<br>

`manager/events/show.blade.php`をコピー mypage/show.blade.php

```php:show.blade.php
<form class="py-4" method="post" action="{{ route('mypage.cancel', $event->id) }}">
  <x-jet-label value="予約人数" />
  {{ $reservation->number_of_people }}
</form>
```

### ハンズオン

- `app/Http/Controllers/MyPageController.php`を編集<br>

```php:MyPageController.php
<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Reservation;
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

  // 追加
  public function show($id)
  {
    $event = Event::findOrFail($id);
    $reservation = Reservation::where('user_id', '=', Auth::id())
      ->where('event_id', '=', $id)
      ->first();
    // dd($reservation);

    return view('mypage/show', compact('event', 'reservation'));
  }
}
```

- `$ cp resources/views/manager/events/show.blade.php resources/views/mypage/show.blade.php`を実行<br>

* `resources/views/mypage/show.blade.php`を編集<br>

```php::show.blade.php
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

                    <form method="get" action="{{-- route('events.edit', $event->id) --}}">
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
                                <x-jet-label value="予約人数" />
                                {{ $reservation->number_of_people }}
                            </div>
                            @if ($event->eventDate < \Carbon\Carbon::today()->format('Y年m月d日'))
                                <x-jet-button class="ml-4">
                                    キャンセルする
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

## 105 マイページキャンセル処理 JS

### マイページ詳細・キャンセル

ルート<br>

```php:web.php
Route::post('mypage/{id}', [MyPageController::class, 'cancel'])->name(
  'mypage.cancel'
);
```

ビュー<br>

```
<form id="cancel_{{ $event->id }}" method="post" action="{{ route('mypage.cancel', $event->id) }}">
  @csrf
  <a href="#" data-id="{{ $event->id }}" onclick="cancelPost(this)">キャンセルする</a>
</form>

<script>
  function cancelPost(e) {
    'use strict';
    if (confirm('本当にキャンセルしてもよろしいですか？')) {
      document.getElementById('cancel_' + e.dataset.id).submit();
    }
  }
</script>
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
  // 追加
  Route::post('mypage/{id}', [MyPageController::class, 'cancel'])->name(
    'mypage.cancel'
  );
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

- `resources/views/mypage/show.blade.php`を編集<br>

```php:show.blade.php
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
                    // 編集 formタグをここへ移動
                    <form id="cancel_{{ $event->id }}" method="post"
                        action="{{ route('mypage.cancel', $event->id) }}">
                        @csrf
                        <div class="md:flex justify-between items-end">
                            <div class="mt-4">
                                <x-jet-label value="予約人数" />
                                {{ $reservation->number_of_people }}
                            </div>
                            @if ($event->eventDate < \Carbon\Carbon::today()->format('Y年m月d日'))
                                <a href="#" data-id="{{ $event->id }}" onclick="cancelPost(this)"
                                    class="ml-4 bg-black text-white py-2 px-4">
                                    キャンセルする
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    // 追加
    <script>
        function cancelPost(e) {
            'user strinct';
            if (confirm('本当にキャンセルしてもよろしいですか？')) {
                document.getElementById('cancel_' + e.dataset.id).submit();
            }
        }
    </script>
</x-app-layout>
```

- `app/Http/Controllers/MyPageController.php`を編集<br>

```php:MyPageController.php
<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Reservation;
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

  public function show($id)
  {
    $event = Event::findOrFail($id);
    $reservation = Reservation::where('user_id', '=', Auth::id())
      ->where('event_id', '=', $id)
      ->first();
    // dd($reserveation);

    return view('mypage/show', compact('event', 'reservation'));
  }

  // 追加
  public function cancel($id)
  {
    dd($id);
  }
}
```

## 106 マイページ キャンセル処理 コントローラ

### マイページ キャンセル

コントローラ<br>

```php:MyPageController.php
use Carbon\Carbon;

public function cancel($id)
{
  $reservation = Reservation::where('user_id', '=', Auth::id())
    ->where('event_id', '=', $id)
    ->first();

  $reservation->canceled_date = Carbon::now()->format('Y-m-d H:i:s');
  $reservation->save();

  session()->flash('status', 'キャンセルしました。');

  return to_route('dashboard');
}
```

- `app/Http/Controllers/MyPageController.php`を編集<br>

```php:MyPageController.php
<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Reservation;
use App\Models\User;
use App\Services\MyPageService;
use Carbon\Carbon;
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

  public function show($id)
  {
    $event = Event::findOrFail($id);
    $reservation = Reservation::where('user_id', '=', Auth::id())
      ->where('event_id', '=', $id)
      ->first();
    // dd($reserveation);

    return view('mypage/show', compact('event', 'reservation'));
  }

  // 追加
  public function cancel($id)
  {
    $reservation = Reservation::where('user_id', '=', Auth::id())
      ->where('event_id', '=', $id)
      ->first();

    $reservation->canceled_date = Carbon::now()->format('Y-m-d H:i:s');
    $reservation->save();

    session()->flash('status', 'キャンセルしました。');

    return to_route('dashboard');
  }
}
```

- `resources/views/mypage/show.blade.php`を編集<br>

```php:show.blade.php
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
                    <form id="cancel_{{ $event->id }}" method="post"
                        action="{{ route('mypage.cancel', $event->id) }}">
                        @csrf
                        <div class="md:flex justify-between items-end">
                            <div class="mt-4">
                                <x-jet-label value="予約人数" />
                                {{ $reservation->number_of_people }}
                            </div>
                            // 編集 >= に戻す
                            @if ($event->eventDate >= \Carbon\Carbon::today()->format('Y年m月d日'))
                                <a href="#" data-id="{{ $event->id }}" onclick="cancelPost(this)"
                                    class="ml-4 bg-black text-white py-2 px-4">
                                    キャンセルする
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cancelPost(e) {
            'user strinct';
            if (confirm('本当にキャンセルしてもよろしいですか？')) {
                document.getElementById('cancel_' + e.dataset.id).submit();
            }
        }
    </script>
</x-app-layout>
```
