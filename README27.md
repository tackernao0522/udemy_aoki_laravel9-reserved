# セクション 8: 利用者側その 2 予約処理

## 94 dashboard のルーティングを変更

### ログイン後のルーティング

今回は API 認証は使わないので<br>
`Route::middleware(['auth:sanctum', 'verified'])`の箇所はコメントアウト<br>

代わりに<br>

```php:web.php
Route::middleware('can:user-higher)
  ->group(function() {
    Route::get('dashboard', [ReservationController::class, 'dashboard'])->name('dashboard');
  });
```

`ReservationController@dashboard`<br>

```php:ReservationController.php
public function dashboard()
{
  return view('dashboard');
}
```

### ハンズオン

- `$ php artisan make:controller ReservationController`を実行<br>

* `app/Http/Controllers/ReservationController.php`を編集<br>

```php:ReservationController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReservationController extends Controller
{
  public function dashboard()
  {
    return view('dashboard');
  }
}
```

- `routes/web.php`を編集<br>

```php:web.php
<?php

use App\Http\Controllers\AlpineTestController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LivewireTestController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return view('calendar');
});

// Route::middleware(['auth:sanctum', 'verified'])
//     ->get('/dashboard', function () {
//         return view('dashboard');
//     })
//     ->name('dashboard');

Route::prefix('manager')
  ->middleware('can:manager-higher')
  ->group(function () {
    Route::get('events/past', [EventController::class, 'past'])->name(
      'events.past'
    );
    Route::resource('events', EventController::class);
  });

Route::middleware('can:user-higher')->group(function () {
  // 編集
  Route::get('/dashboard', [ReservationController::class, 'dashboard'])->name(
    'dashboard'
  );
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

### もし dashboard を変えたい場合

1. HOME 定数を変更する<br>

`app/Providers/RouteServiceProvider.php`<br>

```php:RouteServiceProvider.php
public const HOME = '/dashboard';
```

2. 変更した定数名で Blade ファイルを作成<br>
3. `routes/web.php`の route 名の合わせて変更<br>
