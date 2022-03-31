<div>
    カレンダー
    <x-jet-input id="calendar" class="block mt-1 w-full" type="text" name="calendar" />
    {{ $currentDate }}
    <div class="flex">
        @for ($day = 0; $day < 7; $day++)
            {{ $currentWeek[$day] }}
        @endfor
    </div>
</div>
