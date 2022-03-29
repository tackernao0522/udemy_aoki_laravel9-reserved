## 76 予約情報(リレーション設定)

### リレーションの設定

https://readouble.com/laravel/9.x/ja/eloquent-relationships.html#many-to-many <br>

belongsToMany・・多対多のリレーション、第 2 引数は中間テーブル名<br>
withPivot で中間テーブル内の取得したい情報を指定<br>

`app/Models/Event.php`<br>

```php:Event.php
public function users()
{
  return $this->belongsToMany(User::class, 'reservations')
    ->withPivot('id', 'number_of_people', 'canceled_date');
}
```

`app/Models/User.php`<br>

```php:User.php
public function events()
{
  return $this->belongsToMany(Event::class, 'reservations)
    ->withPivot('id', 'number_of_people', 'canceled_date');
}
```

### EventController@show

```php:EventController.php
public function show(Event $event)
{
  $event = Event::findOrFail($event->id);

  $users = $event->users;

  // dd($event, $users);

  略

  return view('manager.events.show', compact('event', 'users', 'eventDate', 'startTime, 'endTime'));
}
```

### events/show.blade.php

```php:show.blade.php
<div class="py-4">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
      <div class="max-w-2xl mx-auto">
        @if (!$users->isEmpty())
          予約情報
        @endif
      </div>
    </div>
  </div>
</div>
```

## ハンズオン

- `app/Models/Event.php`を編集<br>

```php:Event.php
<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// 追加
use App\Models\User;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'information',
        'max_people',
        'start_date',
        'end_date',
        'is_visible'
    ];

    protected function eventDate(): Attribute
    {
        return new Attribute(
            get: fn () => Carbon::parse($this->start_date)->format('Y年m月d日')
        );
    }

    protected function editEventDate(): Attribute
    {
        return new Attribute(
            get: fn () => Carbon::parse($this->start_date)->format('Y-m-d')
        );
    }

    protected function startTime(): Attribute
    {
        return new Attribute(
            get: fn () => Carbon::parse($this->start_date)->format('H時i分')
        );
    }

    protected function endTime(): Attribute
    {
        return new Attribute(
            get: fn () => Carbon::parse($this->end_date)->format('H時i分')
        );
    }

    // 追加
    public function users()
    {
        return $this->belongsToMany(User::class, 'reservations')
            ->withPivot('id', 'number_of_people', 'canceled_date');
    }
}
```

- `app/Models/User.php ｀を編集<br>

```php:User.php
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
// 追加
use App\Models\Event;

class User extends Authenticatable
{
  use HasApiTokens;
  use HasFactory;
  use HasProfilePhoto;
  use Notifiable;
  use TwoFactorAuthenticatable;

  /**
   * The attributes that are mass assignable.
   *
   * @var string[]
   */
  protected $fillable = ['name', 'email', 'password', 'role'];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
    'two_factor_recovery_codes',
    'two_factor_secret',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  /**
   * The accessors to append to the model's array form.
   *
   * @var array
   */
  protected $appends = ['profile_photo_url'];

  // 追加
  public function events()
  {
    return $this->belongsToMany(Event::class, 'reservations')->withPivot(
      'id',
      'number_of_people',
      'canceled_date'
    );
  }
}
```

- `app/Http/Controllers/EventController.php`を編集<br>

```php:EventController.php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $today = Carbon::today();

    $reservedPeople = DB::table('reservations')
      ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
      ->groupBy('event_id');

    $events = DB::table('events')
      ->leftJoinSub($reservedPeople, 'reservedPeople', function ($join) {
        $join->on('events.id', '=', 'reservedPeople.event_id');
      })
      ->whereDate('start_date', '>=', $today)
      ->orderBy('start_date', 'desc')
      ->paginate(10);

    return view('manager.events.index', compact('events'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('manager.events.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Requests\StoreEventRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(StoreEventRequest $request)
  {
    $check = EventService::checkEventDuplication(
      $request['event_date'],
      $request['start_time'],
      $request['end_time']
    );

    if ($check) {
      // 存在したら
      session()->flash('status', 'この時間帯は既に他の予約が存在します。');

      return redirect()->back();
    }

    $startDate = EventService::joinDateAndTime(
      $request['event_date'],
      $request['start_time']
    );
    $endDate = EventService::joinDateAndTime(
      $request['event_date'],
      $request['end_time']
    );

    Event::create([
      'name' => $request['event_name'],
      'information' => $request['information'],
      'start_date' => $startDate,
      'end_date' => $endDate,
      'max_people' => $request['max_people'],
      'is_visible' => $request['is_visible'],
    ]);

    session()->flash('status', '登録okです');

    return to_route('events.index'); // 名前付きルート
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function show(Event $event)
  {
    // dd($event);
    $event = Event::findOrFail($event->id);
    // 追加
    $users = $event->users;
    // dd($event, $users);
    $eventDate = $event->editEventDate;
    $startTime = $event->startTime;
    $endTime = $event->endTime;
    // dd($eventDate, $startTime, $endTime);

    // 編集
    return view(
      'manager.events.show',
      compact('event', 'users', 'eventDate', 'startTime', 'endTime')
    );
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function edit(Event $event)
  {
    if ($event->eventDate >= Carbon::today()->format('Y年m月d日')) {
      $event = Event::findOrFail($event->id);
      $eventDate = $event->editEventDate;
      $startTime = $event->startTime;
      $endTime = $event->endTime;

      return view(
        'manager.events.edit',
        compact('event', 'eventDate', 'startTime', 'endTime')
      );
    } else {
      session()->flash('status', '過去のイベントは更新できません。');
      return redirect()->route('events.index');
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Requests\UpdateEventRequest  $request
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateEventRequest $request, Event $event)
  {
    $check = EventService::countEventDuplication(
      $request['event_date'],
      $request['start_time'],
      $request['end_time']
    );

    if ($check > 1) {
      $event = Event::findOrFail($event->id);
      $eventDate = $event->editEventDate;
      $startTime = $event->startTime;
      $endTime = $event->endTime;
      session()->flash('status', 'この時間帯は既に他の予約が存在します。');
      return view(
        'manager.events.edit',
        compact('event', 'eventDate', 'startTime', 'endTime')
      );
    }

    $startDate = EventService::joinDateAndTime(
      $request['event_date'],
      $request['start_time']
    );
    $endDate = EventService::joinDateAndTime(
      $request['event_date'],
      $request['end_time']
    );

    $event = Event::findOrFail($event->id);
    $event->name = $request->event_name;
    $event->information = $request->information;
    $event->start_date = $startDate;
    $event->end_date = $endDate;
    $event->max_people = $request->max_people;
    $event->is_visible = $request->is_visible;
    $event->save();

    session()->flash('status', '更新しました。');

    return to_route('events.index'); // 名前付きルート
  }

  public function past()
  {
    $today = Carbon::today();

    $reservedPeople = DB::table('reservations')
      ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
      ->groupBy('event_id');

    $events = DB::table('events')
      ->leftJoinSub($reservedPeople, 'reservedPeople', function ($join) {
        $join->on('events.id', '=', 'reservedPeople.event_id');
      })
      ->whereDate('start_date', '<', $today)
      ->orderBy('start_date', 'desc')
      ->paginate(10);

    return view('manager.events.past', compact('events'));
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Event  $event
   * @return \Illuminate\Http\Response
   */
  public function destroy(Event $event)
  {
    //
  }
}
```

- `resources/views/manager/events/show.blade.php`を編集<br>

```php:show.blade.php
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            イベント詳細
        </h2>
    </x-slot>
    // 編集 pt-4 pb-2にする
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

                    <form method="get" action="{{ route('events.edit', $event->id) }}">
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
                            <div class="flex space-x-4 justify-around">
                                {{ $event->is_visible ? '表示中' : '非表示' }}
                            </div>
                            @if ($event->eventDate >= \Carbon\Carbon::today()->format('Y年m月d日'))
                                <x-jet-button class="ml-4">
                                    編集する
                                </x-jet-button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    // 追加
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="max-w-2xl py-4 mx-auto">
                    @if (!$users->isEmpty())
                        予約状況
                    @endif
                </div>
            </div>
        </div>
    </div>
    // ここまで
    <script src="{{ mix('js/flatpickr.js') }}"></script>
</x-app-layout>
```