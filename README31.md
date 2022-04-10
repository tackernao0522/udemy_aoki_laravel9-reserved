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
