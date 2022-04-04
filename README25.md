## 88 判定用データ用意(定数など)

### 時間を定数でつくる

判定は 2022-03-01 10:00:00 の形式<br>

`app/Constants/EventConst.php`<br>

```php:EventConst.php
<?php
  namespace App\Constants;

  class EventConst
  {
    const EVENT_TIME = [
      '10:00:00',
      '10:30:00',
      '11:00:00',
      〜20:00:00 まで
    ];
  }
```

### エイリアスに設定

`config/app.php`<br>

```php:app.php
'aliases' => Facade::defaultAliases()->merge([
  'Constant' => App\Constants\EventConst::class,
])->toArray(),
```

`\Constant::EVENT_TIME[0]`などで使えるようになる<br>

`app/Livewire/Calendar.php`<br>

```php:Calendar.php
public $checkDay; // 日付判定用
public $dayOfWeek; // 曜日

for($i = 0; $i < 7; $i++) {
  $this->day = CarbonImmutable::today()->addDays($i)->format('m月d日');
  $this->checkDay = CarbonImmutable::today()->addDays($i)->format('Y-m-d');
  $this->dayOfWeek = CarbonImmutable::today()->addDays($i)->dayName;
  array_push($this->currentWeek, [ // 連想配列に変更
    'day' => $this->day, // カレンダー表示用(○月△日)
    'checkDay' => $this->checkDay, // 判定用(○○○○-△△-□□)
    'dayOfWeek' => $this->dayOfWeek // 曜日
  ]);
  // dd($this->currentWeek)
  }
```

### ハンズオン

- `$ mkdir app/Constants && touch $_/EventConst.php`を実行<br>

* `app/Constants/EventConst.php`を編集<br>

```php:EventConst.php
<?php

namespace App\Constants;

class EventConst
{
  const EVENT_TIME = [
    '10:00:00',
    '10:30:00',
    '11:00:00',
    '11:30:00',
    '12:00:00',
    '12:30:00',
    '13:00:00',
    '13:30:00',
    '14:00:00',
    '14:30:00',
    '15:00:00',
    '15:30:00',
    '16:00:00',
    '16:30:00',
    '17:00:00',
    '17:30:00',
    '18:00:00',
    '18:30:00',
    '19:00:00',
    '19:30:00',
    '20:00:00',
  ];
}
```

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
      // 追加
      'Constant' => App\Constants\EventConst::class,
    ])
    ->toArray(),
];
```

- `app/Http/Livewire/Calendar.php`を編集<br>

```php:Calendar.php
<?php

namespace App\Http\Livewire;

use App\Services\EventService;
use Carbon\CarbonImmutable;
use Livewire\Component;

class Calendar extends Component
{
  public $currentDate;
  public $day;
  // 追加
  public $checkDay; // 日付判定用
  public $dayOfWeek; // 曜日
  // ここまで
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

    // 編集
    for ($i = 0; $i < 7; $i++) {
      $this->day = CarbonImmutable::today()
        ->addDays($i)
        ->format('m月d日');
      $this->checkDay = CarbonImmutable::today()
        ->addDays($i)
        ->format('Y-m-d');
      $this->dayOfWeek = CarbonImmutable::today()->addDays($i)->dayName;
      array_push($this->currentWeek, [
        'day' => $this->day,
        'checkDay' => $this->checkDay,
        'dayOfWeek' => $this->dayOfWeek,
      ]);
    }
    dd($this->currentWeek); // 確認後削除
  }

  public function getDate($date)
  {
    $this->currentDate = $date; // 文字列
    $this->currentWeek = [];
    $this->sevenDaysLater = CarbonImmutable::parse($this->currentDate)->addDays(
      7
    );

    $this->events = EventService::getWeekEvents(
      $this->currentDate,
      $this->sevenDaysLater->format('Y-m-d')
    ); //

    for ($i = 0; $i < 7; $i++) {
      $this->day = CarbonImmutable::parse($this->currentDate)
        ->addDays($i)
        ->format('m月d日'); // parseでCarbonインスタンスに変換後 日付を計算
      array_push($this->currentWeek, $this->day);
    }
  }

  public function render()
  {
    return view('livewire.calendar');
  }
}
```

```:browser
^ array:7 [▼
  0 => array:3 [▼
    "day" => "04月04日"
    "checkDay" => "2022-04-04"
    "dayOfWeek" => "月曜日"
  ]
  1 => array:3 [▼
    "day" => "04月05日"
    "checkDay" => "2022-04-05"
    "dayOfWeek" => "火曜日"
  ]
  2 => array:3 [▼
    "day" => "04月06日"
    "checkDay" => "2022-04-06"
    "dayOfWeek" => "水曜日"
  ]
  3 => array:3 [▼
    "day" => "04月07日"
    "checkDay" => "2022-04-07"
    "dayOfWeek" => "木曜日"
  ]
  4 => array:3 [▼
    "day" => "04月08日"
    "checkDay" => "2022-04-08"
    "dayOfWeek" => "金曜日"
  ]
  5 => array:3 [▼
    "day" => "04月09日"
    "checkDay" => "2022-04-09"
    "dayOfWeek" => "土曜日"
  ]
  6 => array:3 [▼
    "day" => "04月10日"
    "checkDay" => "2022-04-10"
    "dayOfWeek" => "日曜日"
  ]
]
```

- `app/Http/Livewire/Calendar.php`を編集<br>

```php:Calendar.php
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
      $this->day = CarbonImmutable::today()
        ->addDays($i)
        ->format('m月d日');
      $this->checkDay = CarbonImmutable::today()
        ->addDays($i)
        ->format('Y-m-d');
      $this->dayOfWeek = CarbonImmutable::today()->addDays($i)->dayName;
      array_push($this->currentWeek, [
        'day' => $this->day,
        'checkDay' => $this->checkDay,
        'dayOfWeek' => $this->dayOfWeek,
      ]);
    }
    // dd($this->currentWeek);
  }

  public function getDate($date)
  {
    $this->currentDate = $date; // 文字列
    $this->currentWeek = [];
    $this->sevenDaysLater = CarbonImmutable::parse($this->currentDate)->addDays(
      7
    );

    $this->events = EventService::getWeekEvents(
      $this->currentDate,
      $this->sevenDaysLater->format('Y-m-d')
    );

    // 編集
    for ($i = 0; $i < 7; $i++) {
      $this->day = CarbonImmutable::parse($this->currentDate)
        ->addDays($i)
        ->format('m月d日'); // parseでCarbonインスタンスに変換後 日付を計算
      $this->checkDay = CarbonImmutable::parse($this->currentDate)
        ->addDays($i)
        ->format('Y-m-d');
      $this->dayOfWeek = CarbonImmutable::parse($this->currentDate)->addDays(
        $i
      )->dayName;
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
```
