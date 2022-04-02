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

## 84 CarbonImmutable(初期表示で 7 日間増えていた問題の対策)

### 初期表示で 7 日間増えていた問題

Carbon はミュータブル(可変)とイミュータブル(不変)がある。デフォルトはミュータブル<br>

```
$this->currentDate = Carbon::today(); // こっちも変わってしまう
$this->sevenDaysLater = $this->currentDate->addDays(7);
```

対策 1 ->copoy()を使ってコピーしてから処理する<br>

```
$this->currentDate = Carbon::today();

$this->sevenDaysLater = $this->currentDate->copy()->addDays(7);
```

対策 2 イミュータブル版を使う<br>

```
use Carbon\CarbonImmutable;

// Carbonの箇所をCarbonImmutable に変更する
```

### ハンズオン

- `app/Http/Livewire/Calendar.php`を編集<br>

```php:Calendar.php
<?php

namespace App\Http\Livewire;

use App\Services\EventService;
// 編集
use Carbon\CarbonImmutable;
use Livewire\Component;

class Calendar extends Component
{
  public $currentDate;
  public $day;
  public $currentWeek;
  public $sevenDaysLater;
  public $events;

  public function mount()
  {
    // 編集
    $this->currentDate = CarbonImmutable::today();
    $this->sevenDaysLater = $this->currentDate->addDays(7);
    $this->currentWeek = [];

    $this->events = EventService::getWeekEvents(
      $this->currentDate->format('Y-m-d'),
      $this->sevenDaysLater->format('Y-m-d')
    ); //

    for ($i = 0; $i < 7; $i++) {
      // 編集
      $this->day = CarbonImmutable::today()
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
    // 編集
    $this->sevenDaysLater = CarbonImmutable::parse($this->currentDate)->addDays(
      7
    );

    $this->events = EventService::getWeekEvents(
      $this->currentDate,
      $this->sevenDaysLater->format('Y-m-d')
    ); //

    for ($i = 0; $i < 7; $i++) {
      // 編集
      $this->day = CarbonImmutable::parse($this->currentDate)
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

## 85 ダミーデータの修正(分と時間を画面に合うように変更)

10 時〜20 時 30 分単位<br>

```
$availableHour = $this->faker->numberBetween(10, 18); // 10時〜18時
$minutes = [0, 30]; // 00分か30分
$mKey = array_rand($minutes); // ランダムにキーを取得
$addHour = $this->faker->numberBetween(1, 3); // イベント時間 1時間〜3時間

$dummyDate = $this->faker->dateTimeThisMonth; // 今月分をランダムに取得
$startDate = $dummyDate->setTime($availableHour, $minutes[$mKey]);
$clone = clone $startDate; // そのままmodifyするとstartDateも変わるためコピー
$endDate = $clone->modify('+' . $addHour . 'hour');

return [
  略
  'start_date' => $startDate,
  'end_date' => $endDate,
];
```

### ハンズオン

- `database/factories/EventFactory.php`を編集<br>

```php:EventFactory.php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition()
  {
    // 追加
    $availableHour = $this->faker->numberBetween(10, 18); // 10時〜18時
    $minutes = [0, 30]; // 00分か30分
    $mKey = array_rand($minutes); // ランダムにキーを取得
    $addHour = $this->faker->numberBetween(1, 3); // イベント時間 1時間〜3時間
    // ここまで

    $dummyDate = $this->faker->dateTimeThisMonth;
    // 追加
    $startDate = $dummyDate->setTime($availableHour, $minutes[$mKey]);
    $clone = clone $startDate;
    $endDate = $clone->modify('+' . $addHour . 'hour');
    dd($startDate, $endDate); // 確認後コメントアウト
    // ここまで

    return [
      'name' => $this->faker->name,
      'information' => $this->faker->realText,
      'max_people' => $this->faker->numberBetween(1, 20),
      // 編集
      'start_date' => $startDate,
      // 編集
      'end_date' => $endDate,
      'is_visible' => $this->faker->boolean,
    ];
  }
}
```

- `$ php artisan migrate:fresh --seed`を実行<br>

```:terminal
^ DateTime @1648778400 {#1919
  date: 2022-04-01 11:00:00.0 Asia/Tokyo (+09:00)
}
^ DateTime @1648789200 {#1923
  date: 2022-04-01 14:00:00.0 Asia/Tokyo (+09:00)
}
```

- `コメントアウト後 $ php artisan migrate:fresh --seed`を実行<br>
