## 89 2 つの for 文の設定(日付と時間)

- `views/livewire/calendar.blade.php`<br>

```php:calendar.blade.php
<div class="flex border border-green-400 mx-auto">
  <x-calendar-time />
  @for ($i = 0; $i < 7; $i++)
    <div class="w-32">
    <div class="略">{{ $currentWeek[$i]['day'] }}</div>
    <div class="略">{{ $currentWeek[$i]['dayOfWeek'] }}</div>
    @for($j = 0; $j < 21; $j++)
      @foreach($events as $event)
        // イベントの判定
        <div></div>
      @endforeach
    @endfor
    </div>
  @endfor
</div>
```

### ハンズオン

- `resources/views/livewire/calendar.blade.php`を編集<br>

```php:calendar.blade.php
<div>
    <div class="text-center text-sm">
        日付を選択してください。本日から最大30日先まで選択可能です。
    </div>
    <input id="calendar" class="block mt-1 mb-2 mx-auto" type="text" name="calendar" value="{{ $currentDate }}"
        wire:change="getDate($event.target.value)" />

    <div class="flex border border-green-400 mx-auto">
        <x-calendar-time />
        @for ($i = 0; $i < 7; $i++)
            <div class="w-32">
                <div class="py-1 px-2 border border-gray-200 text-center">{{ $currentWeek[$i]['day'] }}</div>
                <div class="py-1 px-2 border border-gray-200 text-center">{{ $currentWeek[$i]['dayOfWeek'] }}</div>
                @for ($j = 0; $j < 21; $j++)
                    <div class="py-1 px-2 h-8 border border-gray-200 text-center">10:00</div>
                @endfor
            </div>
        @endfor
    </div>

    @foreach ($events as $event)
        {{ $event->start_date }}<br>
    @endforeach
</div>
```

## 90 1 週間以内にイベントがあるかの判定(isNotEmpty)

### 1 週間通じてイベントがない可能性

https://readouble.com/laravel/9.x/ja/collections.html#method-isnotempty <br>

```
@if($events->isNotEmpty())
  判定処理
@else
  <div></div>
@endif
```

### ハンズオン

- `resources/views/livewire/calendar.blade.php`を編集<br>

```php:calendar.blade.php
<div>
    <div class="text-center text-sm">
        日付を選択してください。本日から最大30日先まで選択可能です。
    </div>
    <input id="calendar" class="block mt-1 mb-2 mx-auto" type="text" name="calendar" value="{{ $currentDate }}"
        wire:change="getDate($event.target.value)" />

    <div class="flex border border-green-400 mx-auto">
        <x-calendar-time />
        @for ($i = 0; $i < 7; $i++)
            <div class="w-32">
                <div class="py-1 px-2 border border-gray-200 text-center">{{ $currentWeek[$i]['day'] }}</div>
                <div class="py-1 px-2 border border-gray-200 text-center">{{ $currentWeek[$i]['dayOfWeek'] }}</div>
                @for ($j = 0; $j < 21; $j++)
                    // 編集
                    @if ($events->isNotEmpty())
                        <div class="py-1 px-2 h-8 border border-gray-200 text-center">{{ \Constant::EVENT_TIME[$j] }}</div>
                    @else
                        <div class="py-1 px-2 h-8 border border-gray-200 text-center"></div>
                    @endif
                    // ここまで
                @endfor
            </div>
        @endfor
    </div>

    @foreach ($events as $event)
        {{ $event->start_date }}<br>
    @endforeach
</div>
```

## 91 判定 1 カレンダー日時と開始時間(DB)が同じならイベント名を表示(firstWhere)

### 判定は 2 つ

1. イベント開始時間(DB) = 対象時間(入力した日付+時間)<br>
   -> イベント名を追記<br>

2. イベント開始時間 <= 対象時間 < イベント終了時間<br>
   ->背景色を変更<br>

`views/livewire/calendar.blade.php`<br>

```php:calendar.blade.php
<div class="flex border border-green-400 mx-auto">
  <x-calendar-time />
  @for ($i = 0; $i < 7; $i++)
    <div class="w-32">
      <div class="略">{{ $currentWeek[$i]['day'] }}</div>
      <div class="略">{{ $currentWeek[$i]['dayOfWeek'] }}</div>
      @for ($j = 0; $j < 21; $j++)
        // イベントの判定
      @endfor
    </div>
  @endfor
</div>
```

### 1. イベント開始時間 = 対象時間

https://readouble.com/laravel/9.x/ja/collections.html#method-first-where <br>

判定 `firstWhere`で条件に合う 1 つ目を返す。なければ`null`<br>

```php:calendar.blade.php
@if(!is_null($events->firstWhere('start_date', $currentWeek[$i]['checkDay'] . " " .\Constant::EVENT_TIME[$j])))

イベント名取得
<div class="text-xs">
  {{ $events->firstWhere('start_date', $currentWeek[$i]['checkDay'] . " " . \Constant::EVENT_TIME[$j])->name }}
</div>
```

### ハンズオン

- `resources/views/livewire/calendar.blade.php`を編集<br>

```php:calendar.blade.php
<div>
    <div class="text-center text-sm">
        日付を選択してください。本日から最大30日先まで選択可能です。
    </div>
    <input id="calendar" class="block mt-1 mb-2 mx-auto" type="text" name="calendar" value="{{ $currentDate }}"
        wire:change="getDate($event.target.value)" />

    <div class="flex border border-green-400 mx-auto">
        <x-calendar-time />
        @for ($i = 0; $i < 7; $i++)
            <div class="w-32">
                <div class="py-1 px-2 border border-gray-200 text-center">{{ $currentWeek[$i]['day'] }}</div>
                <div class="py-1 px-2 border border-gray-200 text-center">{{ $currentWeek[$i]['dayOfWeek'] }}</div>
                @for ($j = 0; $j < 21; $j++)
                    @if ($events->isNotEmpty())
                        // 編集
                        @if (!is_null($events->firstWhere('start_date', $currentWeek[$i]['checkDay'] . ' ' . \Constant::EVENT_TIME[$j])))
                            <div class="py-1 px-2 h-8 border border-gray-200 text-xs">
                                {{ $events->firstWhere('start_date', $currentWeek[$i]['checkDay'] . ' ' . \Constant::EVENT_TIME[$j])->name }}
                            </div>
                        @else
                            <div class="py-1 px-2 h-8 border border-gray-200 text-center"></div>
                        @endif
                        // ここまで
                    @else
                        <div class="py-1 px-2 h-8 border border-gray-200 text-center"></div>
                    @endif
                @endfor
            </div>
        @endfor
    </div>

    @foreach ($events as $event)
        {{ $event->start_date }}<br>
    @endforeach
</div>
```

## 92 判定 2 イベント開始〜終了時間の背景色を変更する

2. 開始時間 <= 対象時間 < 終了時間

判定 1 の方で<br>
イベント開始時間 = 対象時間の情報は取れているので、その情報を活用する<br>

### 計算の考え方

10:00 - 11:00 ・・差分 60 分<br>
30 で割ると 2<br>
開始時間の行は既に背景色を塗っているので -1 する<br>

## 判定 2 開始 - 終了の差分を計算

```php:calender.blade.php
@php // 開始 - 終了の差分を計算
$eventInfo = $events->firstWhere('start_date', $currentWeek[$i]['checkDay'] . " " . \Constant::EVENT_TIME[$j]); // 判定1と同じオブジェクト
$eventPeriod = \Carbon\Carbon::parse($eventInfo->start_date)->diffInMinutes($eventInfo->end_date) / 30 -1; // 差分
@endphp

@if ($eventPeriod > 0)
    @for ($k = 0; $k < $eventPeriod; $k++)
      <div class="py-1 px-2 h-8 border border-gray-200 bg-blue-100"></div>
    @endfor
    @php $j += $eventPeriod @endphp // 追加した分 $jを増やす
@endif
```

`views/livewire/calendar.blade.php`<br>

```php:calendar.blade.php
<div class="flex border border-green-400 mx-auto">
  <x-calendar-time />
  @for ($i = 0; $i < 7; $i++)
    <div class="w-32">
        <div class="略">{{ $currentWeek[$i]['day'] }}</div>
        <div class="略">{{ $currentWeek[$i]['dayOfWeek'] }}</div>
        @for($j = 0; $j < 21; $j++)
          // イベントの判定
        @endfor
    </div>
  @endfor
</div>
```

### ハンズオン

- `resources/views/livewire/calendar.blade.php`を編集<br>

```php:calendar.blade.php
<div>
    <div class="text-center text-sm">
        日付を選択してください。本日から最大30日先まで選択可能です。
    </div>
    <input id="calendar" class="block mt-1 mb-2 mx-auto" type="text" name="calendar" value="{{ $currentDate }}"
        wire:change="getDate($event.target.value)" />

    <div class="flex border border-green-400 mx-auto">
        <x-calendar-time />
        @for ($i = 0; $i < 7; $i++)
            <div class="w-32">
                <div class="py-1 px-2 border border-gray-200 text-center">{{ $currentWeek[$i]['day'] }}</div>
                <div class="py-1 px-2 border border-gray-200 text-center">{{ $currentWeek[$i]['dayOfWeek'] }}</div>
                @for ($j = 0; $j < 21; $j++)
                    @if ($events->isNotEmpty())
                        @if (!is_null($events->firstWhere('start_date', $currentWeek[$i]['checkDay'] . ' ' . \Constant::EVENT_TIME[$j])))
                            // 編集
                            @php
                                $eventName = $events->firstWhere('start_date', $currentWeek[$i]['checkDay'] . ' ' . \Constant::EVENT_TIME[$j])->name;
                                $eventInfo = $events->firstWhere('start_date', $currentWeek[$i]['checkDay'] . ' ' . \Constant::EVENT_TIME[$j]);
                                $eventPeriod = \Carbon\Carbon::parse($eventInfo->start_date)->diffInMinutes($eventInfo->end_date) / 30 - 1;
                            @endphp
                            <div class="py-1 px-2 h-8 border border-gray-200 text-xs bg-blue-100">
                                {{ $eventName }}
                            </div>
                            @if ($eventPeriod > 0)
                                @for ($k = 0; $k < $eventPeriod; $k++)
                                    <div class="py-1 px-2 h-8 border border-gray-200 bg-blue-100"></div>
                                @endfor
                                @php $j += $eventPeriod @endphp
                            @endif
                            // ここまで
                        @else
                            <div class="py-1 px-2 h-8 border border-gray-200 text-center"></div>
                        @endif
                    @else
                        <div class="py-1 px-2 h-8 border border-gray-200 text-center"></div>
                    @endif
                @endfor
            </div>
        @endfor
    </div>
</div>
```
