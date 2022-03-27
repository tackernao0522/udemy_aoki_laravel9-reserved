## 70 過去のイベント

### 削除処理について

後程、予約情報とリレーションを組むため、やや複雑になるということと、<br>

Laravel 第 2 弾(マルチログインで EC サイト)でリレーション込みの削除方法、<br>
リレーション込みの削除方法、ソフトデリート などを詳しく説明している<br>

### 過去のイベント

### ルーティング

現在、全てのイベントが表示されている<br>
今日以降のイベントと昨日以前のイベントで画面を切り替える<br>

ルーティングは上から処理される<br>
リソースの下に書くと /past 部分がパラメータと勘違いされるのでリソースの上に書く<br>

`routes/web.php`<br>

```php:web.php
Route::ptefix('manager')
  ->middleware('can:manager-higher)->group(function () {
    Route::get('events/past', [EventController::class, 'past'])->name('events.past');
    Route::resource('events', EventController::class);
  });
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
  return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])
  ->get('/dashboard', function () {
    return view('dashboard');
  })
  ->name('dashboard');

Route::prefix('manager')
  ->middleware('can:manager-higher')
  ->group(function () {
    // 追加
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

- `app/Http/Controllers/EventController.php`を編集<br>

```php:EventController.php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Services\EventService;
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
    $check = EventService::checkEventDuplication(
      $request['event_date'],
      $request['start_time'],
      $request['end_time']
    );

    if ($check) {
      // 存在したら
      session()->flash('status', 'この時間帯は既に他の予約が存在します。');

      return redirect()->back();
    }

    $startDate = EventService::joinDateAndTime(
      $request['event_date'],
      $request['start_time']
    );
    $endDate = EventService::joinDateAndTime(
      $request['event_date'],
      $request['end_time']
    );

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
    // dd($event);
    $event = Event::findOrFail($event->id);
    $eventDate = $event->editEventDate;
    $startTime = $event->startTime;
    $endTime = $event->endTime;
    // dd($eventDate, $startTime, $endTime);

    return view(
      'manager.events.show',
      compact('event', 'eventDate', 'startTime', 'endTime')
    );
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function edit(Event $event)
  {
    $event = Event::findOrFail($event->id);
    $eventDate = $event->editEventDate;
    $startTime = $event->startTime;
    $endTime = $event->endTime;

    return view(
      'manager.events.edit',
      compact('event', 'eventDate', 'startTime', 'endTime')
    );
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
    $check = EventService::countEventDuplication(
      $request['event_date'],
      $request['start_time'],
      $request['end_time']
    );

    if ($check > 1) {
      $event = Event::findOrFail($event->id);
      $eventDate = $event->editEventDate;
      $startTime = $event->startTime;
      $endTime = $event->endTime;
      session()->flash('status', 'この時間帯は既に他の予約が存在します。');
      return view(
        'manager.events.edit',
        compact('event', 'eventDate', 'startTime', 'endTime')
      );
    }

    $startDate = EventService::joinDateAndTime(
      $request['event_date'],
      $request['start_time']
    );
    $endDate = EventService::joinDateAndTime(
      $request['event_date'],
      $request['end_time']
    );

    $event = Event::findOrFail($event->id);
    $event->name = $request->event_name;
    $event->information = $request->information;
    $event->start_date = $startDate;
    $event->end_date = $endDate;
    $event->max_people = $request->max_people;
    $event->is_visible = $request->is_visible;
    $event->save();

    session()->flash('status', '更新しました。');

    return to_route('events.index'); // 名前付きルート
  }

  // 追加
  public function past()
  {
    $today = Carbon::today();
    $events = DB::table('events')
      ->whereDate('start_date', '<', $today)
      ->orderBy('start_date', 'desc')
      ->paginate(10);

    return view('manager.events.past', compact('events'));
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

- `$ touch resources/views/manager/events/past.blade.php`を実行<br>

* `resources/views/manager/events/past.blade.php`を編集<br>

```php:past.blade.php
<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          過去のイベント一覧
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
                                      <th
                                          class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                          定員</th>
                                      <th
                                          class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                          表示・非表示</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @foreach ($events as $event)
                                      <tr>
                                          <td class="text-blue-500 px-4 py-3">
                                              <a href="{{ route('events.show', $event->id) }}">
                                                  {{ $event->name }}
                                              </a>
                                          </td>
                                          <td class="px-4 py-3">{{ $event->start_date }}</td>
                                          <td class="px-4 py-3">{{ $event->end_date }}</td>
                                          <td class="px-4 py-3">後程対応</td>
                                          <td class="px-4 py-3">{{ $event->max_people }}</td>
                                          <td class="px-4 py-3">{{ $event->is_visible }}</td>
                                      </tr>
                                  @endforeach
                              </tbody>
                          </table>
                          {{ $events->links() }}
                      </div>
                      <div class="flex pl-4 mt-4 lg:w-2/3 w-full mx-auto">

                      </div>
                  </div>
              </section>
          </div>
      </div>
  </div>
</x-app-layout>
```
