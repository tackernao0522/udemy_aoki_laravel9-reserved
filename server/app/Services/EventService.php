<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EventService
{
  public static function checkEventDuplication($eventDate, $startTime, $endTime)
  {
    return DB::table('events')
      ->whereDate('start_date', $eventDate) // 日にち
      ->whereTime('end_date', '>', $startTime)
      ->whereTime('start_date', '<', $endTime)
      ->exists(); // 存在確認

    // $check = DB::table('events')
    //   ->whereDate('start_date', $eventDate) // 日にち
    //   ->whereTime('end_date', '>', $startTime)
    //   ->whereTime('start_date', '<', $endTime)
    //   ->exists(); // 存在確認

    // return $check;
  }

  public static function joinDateAndTime($date, $time)
  {
    $join = $date . " " . $time;
    return Carbon::createFromFormat('Y-m-d H:i', $join);

    //   $dateTime = Carbon::createFromFormat('Y-m-d H:i', $join);

    //   return $dateTime;
  }
}
