<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MyPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyPageController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id());
        $events = $user->events;
        $fromTodayEvents = MyPageService::reservedEvent($events, 'fromToday');
        $pastEvents = MyPageService::reservedEvent($events, 'past');
        dd($events, $fromTodayEvents, $pastEvents);
    }
}
