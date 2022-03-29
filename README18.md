# セクション 6: 施設側(manager) その 2

### Reservation

複数のユーザーが複数のイベントを予約できる・・多対多 `User`多ー多`Event`<br>

中間(pivot)テーブルを挟み 1 対多 `User`1-多 Reservation 多ー 1`Event`<br>

自動で生成するなら event_user(アルファベット順)<br>
今回は Reservation というモデルを作成し設定<br>

### reservation table

|      論理      |       物理       | データ型  | キー |  メモ   |
| :------------: | :--------------: | :-------: | :--: | :-----: |
|       id       |        id        |  bigInt   |  UK  |         |
|    user_id     |     user_id      |  bigInt   |  FK  |         |
|    event_id    |     event_id     |  bigInt   |  FK  |         |
|    予約人数    | number_of_people |  integer  |      |         |
| キャンセル日時 |  canceled_date   | datetime  |      | null 可 |
|    作成日時    |    created_at    | timestamp |      |         |
|    更新日時    |    updated_at    | timestamp |      |         |

### モデル

php artisan make:model Reservation -m<br>

`app/models/Reservation.php`<br>

まとめて登録できるように設定

```php:Reservation.php
protected $fillable = [
  'user_id',
  'event_id',
  'number_of_people'
];
```

### ハンズオン

- `$ php artisan make:model Reservation -m`を実行<br>

* `app/Models/Reservation.php`を編集<br>

```php:Reservation.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
  use HasFactory;

  protected $fillable = ['user_id', 'event_id', 'number_of_people'];
}
```

- `database/migrations/create_reservations_table.php`を編集<br>

```php:create_reservations_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('reservations', function (Blueprint $table) {
      $table->id();
      $table
        ->foreignId('user_id')
        ->constrained()
        ->onUpdate('cascade');
      $table
        ->foreignId('event_id')
        ->constrained()
        ->onUpdate('cascade');
      $table->integer('number_of_people');
      $table->datetime('canceled_date')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('reservations');
  }
};
```

- `$ php artisan make:seeder ReservationsTableSeeder`を実行<br>

* `database/seeders/DatabaseSeeder.php`を編集<br>

```php:DatabaseSeeder.php
<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    // \App\Models\User::factory(10)->create();

    // 最上部に記述
    Event::factory(100)->create();

    $this->call([UsersTableSeeder::class, ReservationsSeederTable::class]);
  }
}
```

- `database/seeders/ReservationsSeederTable.php`を編集<br>

```php:ReservationsSeederTable.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationsSeederTable extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('reservations')->insert([
      [
        'user_id' => 1,
        'event_id' => 1,
        'number_of_people' => 5,
      ],
      [
        'user_id' => 2,
        'event_id' => 1,
        'number_of_people' => 3,
      ],
      [
        'user_id' => 1,
        'event_id' => 2,
        'number_of_people' => 2,
      ],
    ]);
  }
}
```

`$ php artisan migrate:fresh --seed`を実行<br>

## 73 予約数の合計クエリ

### 予約人数の確認

#### SQL の場合

```
SELECT `event_id`,
sum(`number_of_people`) FROM
`reservations` GROUP by `event_id`
```

select 内で sum を使うためクエリビルダの DB::raw で対応<br>

```
$reservedPeople = DB::table('reservations')
  ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
  ->groupBy('event_id');
```

### ハンズオン

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
      ->select('event_id', DB::raw('sum(number_of_people) as numbe_of_people'))
      ->groupBy('event_id');
    dd($reservedPeople);

    $events = DB::table('events')
      ->whereDate('start_date', '>=', $today)
      ->orderBy('start_date', 'desc')
      ->paginate(10);

    return view('manager.events.index', compact('events', 'reservedPeople'));
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
    $eventDate = $event->editEventDate;
    $startTime = $event->startTime;
    $endTime = $event->endTime;
    // dd($eventDate, $startTime, $endTime);

    return view(
      'manager.events.show',
      compact('event', 'eventDate', 'startTime', 'endTime')
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
    $events = DB::table('events')
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

### サブクエリを外部結合で

https://readouble.com/laravel/9.x/ja/queries.html (JOIN)<br>

内部結合・・合計人数がない場合データが表示されない<br>
外部結合・・合計人数がない場合、null として表示される<br>

```
$events = DB::table('events')
  ->leftJoinSub($reservedPeople, 'reservedPeople', function($join) {
    $join->on('events.id', '=', 'reservedPeople.event_id');
  })
  ->whereDate('events.start_date', '<', $today)
  ->orderBy('events.start_date', 'desc')
  ->paginate(10);
```
