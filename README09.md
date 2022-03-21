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

`database/factories/EventFactory.php`<br>

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

## 48 ダミーデータの生成(factory, faker)

- `config/app.php`を編集<br>

```php:app.php
<?php

use Illuminate\Support\Facades\Facade;

return [
  /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

  'name' => env('APP_NAME', 'Laravel'),

  /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

  'env' => env('APP_ENV', 'production'),

  /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

  'debug' => (bool) env('APP_DEBUG', false),

  /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

  'url' => env('APP_URL', 'http://localhost'),

  'asset_url' => env('ASSET_URL'),

  /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

  'timezone' => 'Asia/Tokyo',

  /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

  'locale' => 'ja',

  /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

  'fallback_locale' => 'en',

  /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

  // 編集
  'faker_locale' => 'ja_JP',

  /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

  'key' => env('APP_KEY'),

  'cipher' => 'AES-256-CBC',

  /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

  'providers' => [
    /*
     * Laravel Framework Service Providers...
     */
    Illuminate\Auth\AuthServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
    Illuminate\Bus\BusServiceProvider::class,
    Illuminate\Cache\CacheServiceProvider::class,
    Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
    Illuminate\Cookie\CookieServiceProvider::class,
    Illuminate\Database\DatabaseServiceProvider::class,
    Illuminate\Encryption\EncryptionServiceProvider::class,
    Illuminate\Filesystem\FilesystemServiceProvider::class,
    Illuminate\Foundation\Providers\FoundationServiceProvider::class,
    Illuminate\Hashing\HashServiceProvider::class,
    Illuminate\Mail\MailServiceProvider::class,
    Illuminate\Notifications\NotificationServiceProvider::class,
    Illuminate\Pagination\PaginationServiceProvider::class,
    Illuminate\Pipeline\PipelineServiceProvider::class,
    Illuminate\Queue\QueueServiceProvider::class,
    Illuminate\Redis\RedisServiceProvider::class,
    Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
    Illuminate\Session\SessionServiceProvider::class,
    Illuminate\Translation\TranslationServiceProvider::class,
    Illuminate\Validation\ValidationServiceProvider::class,
    Illuminate\View\ViewServiceProvider::class,

    /*
     * Package Service Providers...
     */

    /*
     * Application Service Providers...
     */
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    // App\Providers\BroadcastServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\JetstreamServiceProvider::class,
  ],

  /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

  'aliases' => Facade::defaultAliases()
    ->merge([
      // ...
    ])
    ->toArray(),
];
```

- `database/factories/EventFactory.php`を編集<br>

```php:EventFactory.php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition()
  {
    $dummyDate = $this->faker->dateTimeThisMonth;

    return [
      'name' => $this->faker->name,
      'information' => $this->faker->realText,
      'max_people' => $this->faker->numberBetween(1, 20),
      'start_date' => $dummyDate->format('Y-m-d H:i:s'),
      'end_date' => $dummyDate->modify('+1hour')->format('Y-m-d H:i:s'),
      'is_visible' => $this->faker->boolean,
    ];
  }
}
```

- `database/seeders/DatabaseSeeder.php`を編集<br>

```php:DatabaseSeeder.php
<?php

namespace Database\Seeders;

// 追加
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
    $this->call([UsersTableSeeder::class]);

    // 追加
    Event::factory(100)->create();
  }
}
```

- `$ php artisan migrate:fresh --seed`を実行<br>
