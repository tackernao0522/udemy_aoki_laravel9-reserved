<?php

namespace App\Http\Livewire;

use App\Services\EventService;
use Carbon\CarbonImmutable;
use Livewire\Component;

class Calendar extends Component
{
    public $currentDate;
    public $day;
    public $checkDay;
    public $dayOfWeek;
    public $currentWeek;
    public $sevenDaysLater;
    public $events;

    public function mount()
    {
        $this->currentDate = CarbonImmutable::today();
        $this->sevenDaysLater = $this->currentDate->addDays(7);
        $this->currentWeek = [];

        $this->events = EventService::getWeekEvents(
            $this->currentDate->format('Y-m-d'),
            $this->sevenDaysLater->format('Y-m-d')
        );

        for ($i = 0; $i < 7; $i++) {
            $this->day = CarbonImmutable::today()->addDays($i)->format('m月d日');
            $this->checkDay = CarbonImmutable::today()->addDays($i)->format('Y-m-d');
            $this->dayOfWeek = CarbonImmutable::today()->addDays($i)->dayName;;
            array_push($this->currentWeek, [
                'day' => $this->day,
                'checkDay' => $this->checkDay,
                'dayOfWeek' => $this->dayOfWeek
            ]);
        }
        // dd($this->currentWeek);
    }

    public function getDate($date)
    {
        $this->currentDate = $date; // 文字列
        $this->currentWeek = [];
        $this->sevenDaysLater = CarbonImmutable::parse($this->currentDate)->addDays(7);

        $this->events = EventService::getWeekEvents(
            $this->currentDate,
            $this->sevenDaysLater->format('Y-m-d')
        );

        for ($i = 0; $i < 7; $i++) {
            $this->day = CarbonImmutable::parse($this->currentDate)->addDays($i)
                ->format('m月d日'); // parseでCarbonインスタンスに変換後 日付を計算
            $this->checkDay = CarbonImmutable::parse($this->currentDate)->addDays($i)
                ->format('Y-m-d');
            $this->dayOfWeek = CarbonImmutable::parse($this->currentDate)->addDays($i)
                ->dayName;
            array_push($this->currentWeek, [
                'day' => $this->day,
                'checkDay' => $this->checkDay,
                'dayOfWeek' => $this->dayOfWeek,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.calendar');
    }
}
