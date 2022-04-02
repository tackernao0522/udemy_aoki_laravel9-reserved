## 83 whereBetween で指定期間のイベントを取得

### 選んだ日から 7 日分のイベント取得

ダミーデータが過去の日付が多い関係で、一旦カレンダーを過去日も選択できるようにする。<br>

`resources/js/flatpickr.js`<br>

```js:flatpickr.js
fatpickr('#calendar', {
  locale: Japanese,
  // minDate: "today", // コメントアウト
  maxDate: new Date().fp_incr(30),
})
```

### イベント情報の取得

コードが長くなるので、Service に切り離すことにする。<br>

https://readouble.com/laravel/9.x/ja/queries.html (whereBetween)<br>

`app/Services/EventService.php`<br>

```php:EventService.php
public static function getWeekEvents($startDate, $endDate)
{
  $reservedPeople = DB::table('reservations')
    ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
    ->whereNotNull('canceled_date')
    ->groupBy('event_id');

  return DB::table('events')
    ->leftJoinSub($reservedPeople, 'reservedPeople', function($join) {
      $join->on('events.id', '=', 'reservedPeople.event_id');
    })
    ->whereBetween('events.start_date', [$startDate, $endDate])
    ->orderBy('events.start_date', 'asc')
    ->get();
}
```

### ハンズオン

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

flatpickr('#calendar', {
  locale: Japanese,
  // minDate: 'today', // コメントアウトする
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
  minuteIncrement: 30,
}

flatpickr('#start_time', setting)

flatpickr('#end_time', setting)
```

- `app/Services/EventService.php`を編集<br>

```php:EventService.php
<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EventService
{
  public static function checkEventDuplication($eventDate, $startTime, $endTime)
  {
    return DB::table('events')
      ->whereDate('start_date', $eventDate)
      ->whereTime('end_date', '>', $startTime)
      ->whereTime('start_date', '<', $endTime)
      ->exists();
  }

  public static function countEventDuplication($eventDate, $startTime, $endTime)
  {
    return DB::table('events')
      ->whereDate('start_date', $eventDate)
      ->whereTime('end_date', '>', $startTime)
      ->whereTime('start_date', '<', $endTime)
      ->count();
  }

  public static function joinDateAndTime($date, $time)
  {
    $join = $date . ' ' . $time;
    return Carbon::createFromFormat('Y-m-d H:i', $join);
  }

  // 追加
  public static function getWeekEvents($startDate, $endDate)
  {
    $reservedPeople = DB::table('reservations')
      ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
      ->whereNotNull('canceled_date')
      ->groupBy('event_id');

    return DB::table('events')
      ->leftJoinSub($reservedPeople, 'reservedPeople', function ($join) {
        $join->on('events.id', '=', 'reservedPeople.event_id');
      })
      ->whereBetween('start_date', [$startDate, $endDate])
      ->orderBy('start_date', 'asc')
      ->get();
  }
}
```

- `app/Http/Livewire/Calendar.php`を編集<br>

```php:Calendar.php
<?php

namespace App\Http\Livewire;

use App\Services\EventService;
use Carbon\Carbon;
use Livewire\Component;

class Calendar extends Component
{
  public $currentDate;
  public $day;
  public $currentWeek;
  public $sevenDaysLater; // 追加
  public $events; // 追加

  public function mount()
  {
    $this->currentDate = Carbon::today();
    // 追加
    $this->sevenDaysLater = $this->currentDate->addDays(7);
    $this->currentWeek = [];

    // 追加
    $this->events = EventService::getWeekEvents(
      $this->currentDate->format('Y-m-d'),
      $this->sevenDaysLater->format('Y-m-d')
    );
    // ここまで

    for ($i = 0; $i < 7; $i++) {
      $this->day = Carbon::today()
        ->addDays($i)
        ->format('m月d日');
      array_push($this->currentWeek, $this->day);
    }
    // dd($this->currentWeek);
  }

  public function getDate($date)
  {
    $this->currentDate = $date; // 文字列
    $this->currentWeek = [];
    // 追加
    $this->sevenDaysLater = Carbon::parse($this->currentDate)->addDays(7);

    // 追加
    $this->events = EventService::getWeekEvents(
      $this->currentDate,
      $this->sevenDaysLater->format('Y-m-d')
    );
    // ここまで

    for ($i = 0; $i < 7; $i++) {
      $this->day = Carbon::parse($this->currentDate)
        ->addDays($i)
        ->format('m月d日'); // parseでCarbonインスタンスに変換後 日付を計算
      array_push($this->currentWeek, $this->day);
    }
  }

  public function render()
  {
    return view('livewire.calendar');
  }
}
```

- `resources/views/livewire/calendar.blade.php`を編集<br>

```php:calendar.blade.php
<div>
    カレンダー
    <input
        id="calendar"
        class="block mt-1 w-full"
        type="text"
        name="calendar"
        value="{{ $currentDate }}"
        wire:change="getDate($event.target.value)"
    />
    <div class="flex">
        @for ($day = 0; $day < 7; $day++)
            {{ $currentWeek[$day] }}
        @endfor
    </div>
    // 追加
    @foreach ($events as $event)
        {{ $event->start_date }}<br>
    @endforeach
</div>
```
