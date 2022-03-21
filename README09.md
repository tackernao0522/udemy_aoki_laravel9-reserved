## 45 Event 関連ファイル生成

### Event 情報を作成

php artisan make:model Event -a (-a は all の略)<br>

`Models/Event.php`<br>
`Controllers/EventCOntroller.php`(各メソッド付き CRUD)<br>
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
