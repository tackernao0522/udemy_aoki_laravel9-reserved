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
