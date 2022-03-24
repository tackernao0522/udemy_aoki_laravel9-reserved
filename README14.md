## 60 保存時の注意(重複チェック)

### 予約システムの仕様

施設内にスタジオが 1 つ<br>

使用例<br>
料理教室、オンラインセミナー、野外イベント　など<br>

できない事<br>
(同じ時間帯に複数のイベントは作成できない)<br>
会議室(1 施設内に複数予約できるスペースがある)<br>

### 重複チェック

新規の開始時間 < 登録済みの終了時間 AND<br>

新規の終了時間 > 登録済みの開始時間<br>

を見なす場合、重複している<br>

61 重複チェックのクエリ(whereDate, whereTime)

https://readouble.com/laravel/9.x/ja/queries.html (whereDate / whereMonth / whereDay / whereYear / whereTime) <br>

```php:EventController.php
$check = DB::table('events')
  ->whereDate('start_date', $request['event_date']) // 日にち
  ->whereTime('end_date', '>', $request['start_time'])
  ->whereTime('start_date', '<', $request['end_time'])
  ->exists(); // 存在確認

// dd($check);
if ($check) {
  // 存在したら
  session()->flash('status', 'この時間帯は既に他の予約が存在します。');

  return view('manager.events.create');
}
```

### ハンズオン

- `app/Http/Controllers/EventController.php`を編集<br>

```php:EventController.php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $events = DB::table('events')
      ->orderBy('start_date', 'ASC')
      ->paginate(10);

    return view('manager.events.index', compact('events'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('manager.events.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\StoreEventRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(StoreEventRequest $request)
  {
    // 追加
    $check = DB::table('events')
      ->whereDate('start_date', $request['event_date']) // 日にち
      ->whereTime('end_date', '>', $request['start_time'])
      ->whereTime('start_date', '<', $request['end_time'])
      ->exists(); // 存在確認

    // dd($check);
    if ($check) {
      // 存在したら
      session()->flash('status', 'この時間帯は既に他の予約が存在します。');

      return redirect()->back();
    }
    // ここまで

    // dd($request);
    // formatは event_date, start_time, end_time modelはstart_date, end_date
    $start = $request['event_date'] . ' ' . $request['start_time'];
    $startDate = Carbon::createFromFormat('Y-m-d H:i', $start);

    $end = $request['event_date'] . ' ' . $request['end_time'];
    $endDate = Carbon::createFromFormat('Y-m-d H:i', $end);

    Event::create([
      'name' => $request['event_name'],
      'information' => $request['information'],
      'start_date' => $startDate,
      'end_date' => $endDate,
      'max_people' => $request['max_people'],
      'is_visible' => $request['is_visible'],
    ]);

    session()->flash('status', '登録okです');

    return to_route('events.index'); // 名前付きルート
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function show(Event $event)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function edit(Event $event)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\UpdateEventRequest  $request
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateEventRequest $request, Event $event)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function destroy(Event $event)
  {
    //
  }
}
```
