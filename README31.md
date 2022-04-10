## 107 予約済みイベントを考える

Reservation モデル<br>
予約済みイベントは再度予約できない。キャンセルしていたら再度予約可能。<br>
`created_at`が最新のレコードが最新の情報<br>

| user_id | event_id | number_of_people |    canceled_date    |     created_at      |
| :-----: | :------: | :--------------: | :-----------------: | :-----------------: |
|    1    |    2     |        2         | 2022-03-02 00:00:00 | 2022-03-01 00:00:00 |
|    1    |    2     |        1         | 2022-03-03 00:00:00 | 2022-03-02 10:00:00 |
|    1    |    2     |        3         |        null         | 2022-03-03 00:00:00 |

### マイページ詳細 コントローラ

#### MyPageController

`created_at`が最新の情報を取得<br>

```php:MyPageController.php
public function show($id)
{
  $event = Event::findOrFail($id);
  $reservation = Reservation::where('user_id', '=', Auth::id())
    ->where('event_id', '=', $id)
    ->latest() // 引数なしだとcreated_atが新しい順
    ->first();
    // dd($reservation);

    return view('mypage/show', compact('event', 'reservation'));
}
```

### ハンズオン

- `app/Http/Controllers/MyPageController.php`を編集<br>

```php:MyPageController.php
<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Reservation;
use App\Models\User;
use App\Services\MyPageService;
use Carbon\Carbon;
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
    // dd($user, $events, $fromTodayEvents, $pastEvents);

    return view('mypage/index', compact('fromTodayEvents', 'pastEvents'));
  }

  public function show($id)
  {
    $event = Event::findOrFail($id);
    // 編集 latest()を追加
    $reservation = Reservation::where('user_id', '=', Auth::id())
      ->where('event_id', '=', $id)
      ->latest()
      ->first();
    // dd($reserveation);

    return view('mypage/show', compact('event', 'reservation'));
  }

  public function cancel($id)
  {
    // 編集 latest()を追加
    $reservation = Reservation::where('user_id', '=', Auth::id())
      ->where('event_id', '=', $id)
      ->latest()
      ->first();

    $reservation->canceled_date = Carbon::now()->format('Y-m-d H:i:s');
    $reservation->save();

    session()->flash('status', 'キャンセルしました。');

    return to_route('dashboard');
  }
}
```

## 108 予約済みのイベントは予約できないように変更

### ReservationController@detail

```php:ReservationController.php
public function detail($id)
{
  略
  $isReserved = Reservation::where('user_id', '=', Auth::id())
    ->where('event_id', '=', $id)
    ->where('canceled_date', '=', null)
    ->latest()
    ->first();

    return view('', compact('isReserved'));
}
```

### events-detail.blade.php

```php:events-detail.blade.php
@if($isReserved === null)
  <input type="hidden" name="id" value="{{ $event->id }}">
    <div class="flex items-center justify-center mt-4">
      <x-jet-button class="ml-4">
        予約する
      </x-jet-button>
    </div>
@else
  <span class="text-xs">このイベントは既に予約済みです。</span>
@endif
```

### ハンズオン

- `app/Http/Controllers/ReservationController.php`を編集<br>

```php:ReservationController.php
<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
  public function dashboard()
  {
    return view('dashboard');
  }

  public function detail($id)
  {
    $event = Event::findOrFail($id);

    $reservedPeople = DB::table('reservations')
      ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
      ->whereNull('canceled_date')
      ->groupBy('event_id')
      ->having('event_id', $event->id)
      ->first();

    if (!is_null($reservedPeople)) {
      $reservablePeople =
        $event->max_people - $reservedPeople->number_of_people;
    } else {
      $reservablePeople = $event->max_people;
    }

    // 追加
    $isReserved = Reservation::where('user_id', '=', Auth::id())
      ->where('event_id', '=', $id)
      ->where('canceled_date', '=', null)
      ->latest()
      ->first();
    // ここまで

    // 編集 isReservedを追加
    return view(
      'event-detail',
      compact('event', 'reservablePeople', 'isReserved')
    );
  }

  public function reserve(Request $request)
  {
    $event = Event::findOrFail($request->id);

    $reservedPeople = DB::table('reservations')
      ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
      ->whereNull('canceled_date')
      ->groupBy('event_id')
      ->having('event_id', $event->id)
      ->first();

    if (
      is_null($reservedPeople) ||
      $event->max_people >=
        $reservedPeople->number_of_people + $request->reserved_people
    ) {
      Reservation::create([
        'user_id' => Auth::id(),
        'event_id' => $request['id'],
        'number_of_people' => $request['reserved_people'],
      ]);

      session()->flash('status', '登録okです');

      return to_route('dashboard');
    } else {
      session()->flash('status', 'この人数は予約できません。');
      return view('dashboard');
    }
  }
}
```

- `resources/views/event-detail.blade.php`を編集<br>

```php:event-detail.blade.php
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            イベント詳細
        </h2>
    </x-slot>

    <div class="pt-4 pb-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="max-w-2xl py-4 mx-auto">
                    <x-jet-validation-errors class="mb-4" />

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="post" action="{{ route('events.reserve', $event->id) }}">
                        @csrf
                        <div>
                            <x-jet-label for="event_name" value="イベント名" />
                            {{ $event->name }}
                        </div>
                        <div class="mt-4">
                            <x-jet-label for="information" value="イベント詳細" />
                            {!! nl2br(e($event->information)) !!}
                        </div>

                        <div class="md:flex justify-between">
                            <div class="mt-4">
                                <x-jet-label for="event_date" value="イベント日付" />
                                {{ $event->eventDate }}
                            </div>

                            <div class="mt-4">
                                <x-jet-label for="start_time" value="開始時間" />
                                {{ $event->startTime }}
                            </div>

                            <div class="mt-4">
                                <x-jet-label for="end_time" value="終了時間" />
                                {{ $event->endTime }}
                            </div>
                        </div>
                        <div class="md:flex justify-between items-end">
                            <div class="mt-4">
                                <x-jet-label for="max_people" value="定員数" />
                                {{ $event->max_people }}
                            </div>
                            <div class="mt-4">
                                @if ($reservablePeople <= 0)
                                    <span class="text-red-500 text-xs">このイベントは満員です。</span>
                                @else
                                    <x-jet-label for="reserved_people" value="予約人数" />
                                    <select name="reserved_people">
                                        @for ($i = 1; $i <= $reservablePeople; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                @endif
                            </div>
                            // 編集
                            @if ($isReserved === null)
                                <input type="hidden" name="id" value="{{ $event->id }}">
                                @if ($reservablePeople > 0)
                                    <x-jet-button class="ml-4">
                                        予約する
                                    </x-jet-button>
                                @endif
                            @else
                                <span class="text-xs">このイベントは既に予約済みです。</span>
                            @endif
                            // ここまで
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

## 109 mypage のメニュー追加

- `resources/views/navigation-menu.blade.php`を編集<br>

```php:navigation-menu.blade.php
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="w-20 shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-jet-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-jet-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        イベントカレンダー
                    </x-jet-nav-link>
                    // 追加 マイページlinkの追加
                    <x-jet-nav-link href="{{ route('mypage.index') }}" :active="request()->routeIs('mypage.index')">
                        マイページ
                    </x-jet-nav-link>
                    @can('manager-higher')
                        <x-jet-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')">
                            イベント管理
                        </x-jet-nav-link>
                    @endcan
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="ml-3 relative">
                        <x-jet-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                        {{ Auth::user()->currentTeam->name }}

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <!-- Team Settings -->
                                    <x-jet-dropdown-link
                                        href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                        {{ __('Team Settings') }}
                                    </x-jet-dropdown-link>

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-jet-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-jet-dropdown-link>
                                    @endcan

                                    <div class="border-t border-gray-100"></div>

                                    <!-- Team Switcher -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Switch Teams') }}
                                    </div>

                                    @foreach (Auth::user()->allTeams() as $team)
                                        <x-jet-switchable-team :team="$team" />
                                    @endforeach
                                </div>
                            </x-slot>
                        </x-jet-dropdown>
                    </div>
                @endif

                <!-- Settings Dropdown -->
                <div class="ml-3 relative">
                    <x-jet-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button
                                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="h-8 w-8 rounded-full object-cover"
                                        src="{{ Auth::user()->profile_photo_url }}"
                                        alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                        {{ Auth::user()->name }}

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-jet-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-jet-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-jet-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-jet-dropdown-link>
                            @endif

                            <div class="border-t border-gray-100"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-jet-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-jet-dropdown-link>
                            </form>
                        </x-slot>
                    </x-jet-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-jet-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                イベントカレンダー
            </x-jet-responsive-nav-link>
            // 追加 マイページlinkの追加
            <x-jet-responsive-nav-link href="{{ route('mypage.index') }}" :active="request()->routeIs('mypage.index')">
                マイページ
            </x-jet-responsive-nav-link>
            @can('manager-higher')
                <x-jet-responsive-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')">
                    イベント管理
                </x-jet-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 mr-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                            alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-jet-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-jet-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-jet-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-jet-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-jet-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-jet-responsive-nav-link>
                </form>

                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-gray-200"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    <x-jet-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}"
                        :active="request()->routeIs('teams.show')">
                        {{ __('Team Settings') }}
                    </x-jet-responsive-nav-link>

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-jet-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                            {{ __('Create New Team') }}
                        </x-jet-responsive-nav-link>
                    @endcan

                    <div class="border-t border-gray-200"></div>

                    <!-- Team Switcher -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Switch Teams') }}
                    </div>

                    @foreach (Auth::user()->allTeams() as $team)
                        <x-jet-switchable-team :team="$team" component="jet-responsive-nav-link" />
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</nav>
```
