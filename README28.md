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
<x-jet-label for="reservedPeople" value="予約人数" />
<select name="reservedPeople">
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
                                <select name="reservedPeople">
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
