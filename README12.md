## 53 flatpickr 表示確認

### create ファイル作成<br>

`controllers/EventController@create`<br>

```php:EventController.php
public function create()
{
  return view('manager.events.create');
}
```

`resources/views/manager/events/create.blade.php`<br>

```php:create.blade.php
// 略
<input type="text" id="event_date">

<script src="{{ mix('js/flatpickr.js') }}"></script>
</x-app-layout>
```

### ハンズオン

- `app/Http/Controllers/EventController.php`を編集<br>

```php:EventController.php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
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
  // 編集
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
    //
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

- `$ touch resources/views/manager/events/create.blade.php`を実行<br>

* `resources/views/manager/events/create.blade.php`を編集<br>

```php:create.blade.php
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            イベント新規登録
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <input type="text" id="event_date">
            </div>
        </div>
    </div>
    <script src="{{ mix('js/flatpickr.js') }}"></script>
</x-app-layout>
```

- http://localhost/manager/events/create にアクセスして確認<br>
