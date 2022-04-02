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
    public $sevenDaysLater;
    public $events;

    public function mount()
    {
        $this->currentDate = Carbon::today();
        $this->sevenDaysLater = $this->currentDate->addDays(7);
        $this->currentWeek = [];

        $this->events = EventService::getWeekEvents(
            $this->currentDate->format('Y-m-d'),
            $this->sevenDaysLater->format('Y-m-d')
        ); //

        for ($i = 0; $i < 7; $i++) {
            $this->day = Carbon::today()->addDays($i)->format('m月d日');
            array_push($this->currentWeek, $this->day);
        }
        // dd($this->currentWeek);
    }

    public function getDate($date)
    {
        $this->currentDate = $date; // 文字列
        $this->currentWeek = [];
        $this->sevenDaysLater = Carbon::parse($this->currentDate)->addDays(7);

        $this->events = EventService::getWeekEvents(
            $this->currentDate,
            $this->sevenDaysLater->format('Y-m-d')
        ); //

        for ($i = 0; $i < 7; $i++) {
            $this->day = Carbon::parse($this->currentDate)->addDays($i)
                ->format('m月d日'); // parseでCarbonインスタンスに変換後 日付を計算
            array_push($this->currentWeek, $this->day);
        }
    }

    public function render()
    {
        return view('livewire.calendar');
    }
}
