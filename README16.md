## 66 Show ビュー側の調整

- `resources/views/manager/events/show.blade.php`を編集<br>

```php:show.blade.php
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            イベント詳細
        </h2>
    </x-slot>

    <div class="py-12">
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
                            <div class="flex space-x-4 justify-around">
                                {{ $event->is_visible ? '表示中' : '非表示' }}
                            </div>
                            <x-jet-button class="ml-4">
                                編集する
                            </x-jet-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ mix('js/flatpickr.js') }}"></script>
</x-app-layout>
```

## 67 edit

### edit 編集

EventController@edit は、ほぼ show と同じ(return view で edit に渡す)<br>

`events/edit.blade.php`は create(input タグ)と show(アクセサで取得した値など)を混ぜて作る<br>

(form method="post"で update に渡す、<br>
@method('put')をつけるなど)<br>

### ハンズオン

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
    $eventDate = $event->eventDate;
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
  //   追加
  public function edit(Event $event)
  {
    $event = Event::findOrFail($event->id);
    $eventDate = $event->eventDate;
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

- `$ touch resources/views/manager/events/edit.blade.php`を実行<br>

* `resources/views/manager/events/edit.blade.php`を編集<br>

```php:edit.blade.php
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            イベント編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="max-w-2xl py-4 mx-auto">
                    <x-jet-validation-errors class="mb-4" />

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('events.update', $event->id) }}">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-jet-label for="event_name" value="イベント名" />
                            <x-jet-input id="event_name" class="block mt-1 w-full" type="text" name="event_name"
                                :value="old('event_name', $event->name)" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-jet-label for="information" value="イベント詳細" />
                            <x-textarea row="3" id="information" name="information" class="block mt-1 w-full">
                                {{ old('information', $event->information) }}
                            </x-textarea>
                        </div>

                        <div class="md:flex justify-between">
                            <div class="mt-4">
                                <x-jet-label for="event_date" value="イベント日付" />
                                <x-jet-input id="event_date" class="block mt-1 w-full" type="text" name="event_date"
                                    value="{{ old('event_date', $event->eventDate) }}" required />
                            </div>

                            <div class="mt-4">
                                <x-jet-label for="start_time" value="開始時間" />
                                <x-jet-input id="start_time" class="block mt-1 w-full" type="text" name="start_time"
                                    value="{{ old('start_time', $event->startTime) }}" required />
                            </div>

                            <div class="mt-4">
                                <x-jet-label for="end_time" value="終了時間" />
                                <x-jet-input id="end_time" class="block mt-1 w-full" type="text" name="end_time"
                                    value="{{ old('end_time', $event->endTime) }}" required />
                            </div>
                        </div>
                        <div class="md:flex justify-between items-end">
                            <div class="mt-4">
                                <x-jet-label for="max_people" value="定員数" />
                                <x-jet-input id="max_people" class="block mt-1 w-full" type="number" name="max_people"
                                    value="{{ old('max_people', $event->max_people) }}" required />
                            </div>
                            <div class="flex space-x-4 justify-around">
                                <input type="radio" name="is_visible" value="1"
                                    {{ old('is_visible', $event->is_visible) === 1 ? 'checked' : '' }} />表示
                                <input type="radio" name="is_visible" value="0"
                                    {{ old('is_visible', $event->is_visible) === 0 ? 'checked' : '' }} />非表示
                            </div>
                            <x-jet-button class="ml-4">
                                更新する
                            </x-jet-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ mix('js/flatpickr.js') }}"></script>
</x-app-layout>
```

## 68 update

### update 更新

EventController@update はほぼ store と同じ<br>

(Event::create ではなく、
$event = Event::findOrFail($id)で指定して
$event->name = $request['name']とする

UpdateEventRequest があるので<br>
StoreEventRequest をコピーする<br>

### ハンズオン

- `app/Http/Requests/UpdateEventRequest.php`を編集<br>

```php:UpdateEventRequest.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'event_name' => ['required', 'max:50'],
      'information' => ['required', 'max:200'],
      'event_date' => ['required', 'date'],
      'start_time' => ['required'],
      'end_time' => ['required', 'after:start_time'], // 開始時間よりも後でなければ引っかかる
      'max_people' => ['required', 'numeric', 'between:1, 20'],
      'is_visible' => ['required', 'boolean'],
    ];
  }
}
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
    $eventDate = $event->eventDate;
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
    $eventDate = $event->eventDate;
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
  //   編集
  public function update(UpdateEventRequest $request, Event $event)
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

※ このままだと書き換えなしで更新するとエラーになってしまう。<br>
