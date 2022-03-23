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

## 54 flatpickr 日本語化・時間表示

https://flatpickr.js.org/localization/ <br>

https://flatpickr.js.org/examples/ <br>

### flatpickr 設定 View 側

```php:sample.blade.php
日付 <input type="text" id="event_date" name="event_date">
開始 <input type="text" id="start_time" name="start_time">
終了時間 <input type="text" id="end_time" name="end_time">
```

### flatpickr.js

```js:jatpickr.js
import flatpickr from 'flatpickr'
import { japanese } from 'flatpickr/dist/l10n/ja.js'

// 日本語設定、今日以降選択、30日間
flatpickr('#event_date', {
  locale: japanese,
  minDate: 'today',
  maxDate: new Date().fp_incr(30),
})

// 時間表示、絡んだー非表示、24時間表記
const setting = {
  locale: Japanese,
  enableTime: true,
  noCalendar: true,
  dateFormat: 'H:i',
  time_24hr: true,
}

flatpickr('#start_time', setting)

flatpickr('end_time', setting)
```

### ハンズオン

- `resources/views/manager/events/create.blade.php`を編集<br>

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
                日付
                <input type="text" id="event_date" name="event_date">
                開始時間
                <input type="text" id="start_time" name="start_time">
                終了時間
                <input type="text" id="end_time" name="end_time">
            </div>
        </div>
    </div>
    <script src="{{ mix('js/flatpickr.js') }}"></script>
</x-app-layout>
```

- `resources/js/flatpickr.js`を編集<br>

```js:flatpickr.js
import flatpickr from 'flatpickr'
import { Japanese } from 'flatpickr/dist/l10n/ja.js'

// 日本語設定、今日以降選択、30日間
flatpickr('#event_date', {
  locale: Japanese,
  minDate: 'today',
  maxDate: new Date().fp_incr(30),
})

const setting = {
  locale: Japanese,
  enableTime: true,
  noCalendar: true,
  dateFormat: 'H:i',
  time_24hr: true,
  minTime: '10:00',
  maxTime: '20:00',
}

flatpickr('#start_time', setting)

flatpickr('#end_time', setting)
```

## 55 イベント新規登録 View

`resources/views/manager/events/create.blade.php`<br>
`auth/login.blade.php`を参考<br>

コンポーネントは components フォルダを作成し`components/textarea.blade.php`を作成<br>
`<x-textarea>`で使用できる<br>

`index.blade.php`にボタン追加<br>

```php:index.blade.php
<button onlick="location.href='{{ route('events.create') }}'"></button>
```

### Event 新規登録

フォームの抜粋 name の値<br>

`event_name`・・バリデーションで表示させるため<br>
`information`<br>
`event_date`<br>
`start_time`<br>
`end_time`<br>
`max_people`<br>
`is_visible`<br>

### バリデーション 日本語化

`lang/ja/validation.php`<br>

```php:validation.php
'attributes' => [
  'email' => 'メールアドレス',
  'password' => 'パスワード',
  'name' => '名前',
  'event_name' => 'イベント名',
  'information' => 'イベントの日付',
  'end_time' => '終了時間',
  'start_time' => '開始時間',
  'max_people' => '定員',
],
```

### ハンズオン

- `resources/views/manager/events/index.blade.php`を編集<br>

```php:index.blade.php
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            イベント管理
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <section class="text-gray-600 body-font">
                    <div class="container px-5 py-4 mx-auto">
                        // 追加
                        <button onclick="location.href='{{ route('events.create') }}'"
                            class="flex mb-4 ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">新規登録</button>
                        // ここまで
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
                                            <td class="px-4 py-3">{{ $event->name }}</td>
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
                        // 編集
                        <div class="flex pl-4 mt-4 lg:w-2/3 w-full mx-auto">

                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
```
