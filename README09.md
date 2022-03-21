## 45 Event 関連ファイル生成

### Event 情報を作成

php artisan make:model Event -a (-a は all の略)<br>

`Models/Event.php`<br>
`Controllers/EventController.php`(各メソッド付き CRUD)<br>
`database/migrations/event_table.php`<br>
`database/seeders/EventSeeder.php`<br>
`database/factories/EventFactory.php`<br>
`Requests/StoreEventRequest.php`<br>
`Requests/UpdataEventRequest.php`<br>
`Policies/EventPolicy.php`<br>

### ルーティング設定

```php:web.php
use App\Http\Controllers\EventController;

Route::prefix('manager')
  ->middleware('can:manager-higher)
  ->group(function() {
    Route::resource('events', EventController::class);
  });
```

マニュアル・・コントローラ/リソースコントローラ<br>

### ハンズオン

- `$ php artisan make:model Event -a`を実行<br>

* `routes/web.php`を編集<br>

```php:web.php
<?php

use App\Http\Controllers\AlpineTestController;
use App\Http\Controllers\EventController; // 追加
use App\Http\Controllers\LivewireTestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])
  ->get('/dashboard', function () {
    return view('dashboard');
  })
  ->name('dashboard');

// 編集
Route::prefix('manager')
  ->middleware('can:manager-higher')
  ->group(function () {
    Route::resource('events', EventController::class);
  });

Route::middleware('can:user-higher')->group(function () {
  Route::get('index', function () {
    dd('user');
  });
});

// localhost/livewire-test/index
Route::controller(LivewireTestController::class)
  ->prefix('livewire-test')
  ->name('livewire-test.')
  ->group(function () {
    Route::get('index', 'index')->name('index');
    Route::get('register', 'register')->name('register');
  });

Route::get('alpine-test/index', [AlpineTestController::class, 'index']);
```

## 46 Event のマイグレーションファイル

### マイグレーション設定

`database/migrations/events_table.php`<br>

```php:events_table.php
public function up()
{
  Schema::create('events', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('information');
    $table->integer('max_people');
    $table->datetime('start_date');
    $table->datetime('end_date');
    $table->boolean('is_visible');
    $table->timestamps();
  })
}
```

### ハンズオン

- `database/migration/create_events_table.php`を編集<br>

```php:create_events_table.php
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
    Schema::create('events', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->text('information');
      $table->integer('max_people');
      $table->datetime('start_date');
      $table->datetime('end_date');
      $table->boolean('is_visible');
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
    Schema::dropIfExists('events');
  }
};
```

## 47 ダミーデータの注意(開始日時と終了日時)

### ダミーデータ生成

`config/app.php`<br>

```php:app.php
'faker_locale' => 'ja_JP'
```

php artisan cache:clear<br>
php artisan config:clear<br>

faker チートシート<br>
https://qiita.com/tosite0345/items/1d47961947a6770053af <br>

開始日時より終了日時を後にする必要がある<br>

faker で datetime を作ると DateTime 型
https://www.php.net/manual/ja/class.datetime.php <br>

### 開始時間と終了時間の整合

`database/factories/EventFacgtory.php`<br>

```php:EventFactory.php
public function definition() {
    $dummyDate = $this->faker->dateTimeThisMonth;
    return [
    'name' => $this->faker->name,
    'information' => $this->faker->realText,
    'max_people' => $this->faker->numberBetween(1,20),
    'start_date' => $dummyDate->format('Y-m-d H:i:s'),
    'end_date' => $dummyDate->modify('+1hour')->format('Y-m-d H:i:s'), 'is_visible' => $this->faker->boolean
    ];
  }
}
```
