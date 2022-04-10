## 107 予約済みイベントを考える

Reservation モデル<br>
予約済みイベントは再度予約できない。キャンセルしていたら再度予約可能。<br>
`created_at`が最新のレコードが最新の情報<br>

| user_id | event_id | number_of_people |    canceled_date    |     created_at      |
| :-----: | :------: | :--------------: | :-----------------: | :-----------------: |
|    1    |    2     |        2         | 2022-03-02 00:00:00 | 2022-03-01 00:00:00 |
|    1    |    2     |        1         | 2022-03-03 00:00:00 | 2022-03-02 10:00:00 |
|    1    |    2     |        3         |        null         | 2022-03-03 00:00:00 |

### マイページ詳細 コントローラ

#### MyPageController

`created_at`が最新の情報を取得<br>

```php:MyPageController.php
public function show($id)
{
  $event = Event::findOrFail($id);
  $reservation = Reservation::where('user_id', '=', Auth::id())
    ->where('event_id', '=', $id)
    ->latest() // 引数なしだとcreated_atが新しい順
    ->first();
    // dd($reservation);

    return view('mypage/show', compact('event', 'reservation'));
}
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
    // 編集 latest()を追加
    $reservation = Reservation::where('user_id', '=', Auth::id())
      ->where('event_id', '=', $id)
      ->latest()
      ->first();
    // dd($reserveation);

    return view('mypage/show', compact('event', 'reservation'));
  }

  public function cancel($id)
  {
    // 編集 latest()を追加
    $reservation = Reservation::where('user_id', '=', Auth::id())
      ->where('event_id', '=', $id)
      ->latest()
      ->first();

    $reservation->canceled_date = Carbon::now()->format('Y-m-d H:i:s');
    $reservation->save();

    session()->flash('status', 'キャンセルしました。');

    return to_route('dashboard');
  }
}
```

## 108 予約済みのイベントは予約できないように変更

### ReservationController@detail

```php:ReservationController.php
public function detail($id)
{
  略
  $isReserved = Reservation::where('user_id', '=', Auth::id())
    ->where('event_id', '=', $id)
    ->where('canceled_date', '=', null)
    ->latest()
    ->first();

    return view('', compact('isReserved'));
}
```

### events-detail.blade.php

```php:events-detail.blade.php
@if($isReserved === null)
  <input type="hidden" name="id" value="{{ $event->id }}">
    <div class="flex items-center justify-center mt-4">
      <x-jet-button class="ml-4">
        予約する
      </x-jet-button>
    </div>
@else
  <span class="text-xs">このイベントは既に予約済みです。</span>
@endif
```

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

    // 追加
    $isReserved = Reservation::where('user_id', '=', Auth::id())
      ->where('event_id', '=', $id)
      ->where('canceled_date', '=', null)
      ->latest()
      ->first();
    // ここまで

    // 編集 isReservedを追加
    return view(
      'event-detail',
      compact('event', 'reservablePeople', 'isReserved')
    );
  }

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
        'event_id' => $request['id'],
        'number_of_people' => $request['reserved_people'],
      ]);

      session()->flash('status', '登録okです');

      return to_route('dashboard');
    } else {
      session()->flash('status', 'この人数は予約できません。');
      return view('dashboard');
    }
  }
}
```

- `resources/views/event-detail.blade.php`を編集<br>

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
                            </div>
                            // 編集
                            @if ($isReserved === null)
                                <input type="hidden" name="id" value="{{ $event->id }}">
                                @if ($reservablePeople > 0)
                                    <x-jet-button class="ml-4">
                                        予約する
                                    </x-jet-button>
                                @endif
                            @else
                                <span class="text-xs">このイベントは既に予約済みです。</span>
                            @endif
                            // ここまで
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```
