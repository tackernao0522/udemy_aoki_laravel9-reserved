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
