## 43 role(役割) の追加

### 認可(アクセス制限)

manager・・表示・操作できる<br>
user・・表示・操作できない<br>

Laravel では Gate、Policy で対応<br>
Gate・・シンプル<br>
Policy・・特定のモデル・アクションを認可<br>

事前に role 列を追記しておく<br>

### Role の考え方

追って追加する可能性も考慮しておく<br>
数字の少ない方が権限が強い<br>

1・・admin,<br>
2, 3, 4,<br>
5・・manager,<br>
6, 7, 8,<br>
9・・user<br>

あとから manager と user の間(有料会員)の権限なども追加しやすい<br>

### Role の追加

`database/migrations/create_users_table.php`<br>

```
tinyInteger('role') // 追加
```

`app/Actions/Fortify/CreateNewUser.php`<br>

```
return User::create([
  'role' => 9, // ユーザー登録する際はuserとして追加
]);
```

`app/Models/User.php`<br>

```
protected $fillable = [
  'role' // User::create()でまとめて登録できるようにするため
];
```

### ハンズオン

`database/migrations/create_users_table.php`を編集<br>

```php:create_users_table.php
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
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('email')->unique();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('password');
      $table->rememberToken();
      $table->tinyInteger('role'); // 追加
      $table->foreignId('current_team_id')->nullable();
      $table->string('profile_photo_path', 2048)->nullable();
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
    Schema::dropIfExists('users');
  }
};
```

- `app/Actions/Fortify/CreateNewUser.php`を編集<br>

```php:CreateNewUser.php
<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
  use PasswordValidationRules;

  /**
   * Validate and create a newly registered user.
   *
   * @param  array  $input
   * @return \App\Models\User
   */
  public function create(array $input)
  {
    Validator::make($input, [
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'password' => $this->passwordRules(),
      'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature()
        ? ['accepted', 'required']
        : '',
    ])->validate();

    return User::create([
      'name' => $input['name'],
      'email' => $input['email'],
      'password' => Hash::make($input['password']),
      'role' => 9, // 追加
    ]);
  }
}
```

- `app/Models/User.php`を編集<br>

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
  protected $fillable = ['name', 'email', 'password', 'role']; // 編集

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
}
```

- `$ php artisan make:seeder UsersTableSeeder`を実行<br>

- `database/seeders/UsersTableSeeder.php`を編集<br>

```php:UsersTableSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('users')->insert([
      [
        'name' => 'admin',
        'email' => 'admin@admin.com',
        'password' => Hash::make('password123'),
        'role' => 1,
      ],
      [
        'name' => 'manager',
        'email' => 'manager@manager.com',
        'password' => Hash::make('password123'),
        'role' => 5,
      ],
      [
        'name' => 'test',
        'email' => 'test@test.com',
        'password' => Hash::make('password123'),
        'role' => 9,
      ],
    ]);
  }
}
```

- `database/seeders/DatabaseSeeder.php`を編集<br>

```php:DatabaseSeeder.php
<?php

namespace Database\Seeders;

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
  }
}
```

- `$ php artisan migrate:fresh --seed`を実行<br>

## 44 Gate 設定・ルーティングの確認

### Gate(門、入り口)の設定

参考: https://readouble.com/laravel/8.x/ja/authorization.html <br>

`app/Providers/AuthServiceProvider.php`<br>

```
Gate::define('admin', function($user) {
  return $user->role === 1;
});

Gate::difine('manager-higher', function($user) {
  return $user->role > 0 && $user->role <= 5;
});

Gate::difine('user-higher', function($user) {
  return $user->role > 0 && $user->role <= 9;
});
```

### ハンズオン

- `app/Providers/AuthServiceProvider.php`を編集<br>

```php:AuthServiceProvider.php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * The policy mappings for the application.
   *
   * @var array<class-string, class-string>
   */
  protected $policies = [
    // 'App\Models\Model' => 'App\Policies\ModelPolicy',
  ];

  /**
   * Register any authentication / authorization services.
   *
   * @return void
   */
  public function boot()
  {
    $this->registerPolicies();

    // 追加
    Gate::define('admin', function ($user) {
      return $user->role === 1;
    });

    Gate::define('manager-higher', function ($user) {
      return $user->role > 0 && $user->role <= 5;
    });

    Gate::define('user-higher', function ($user) {
      return $user->role > 0 && $user->role <= 9;
    });
    // ここまで
  }
}
```

### ルートに Gate の設定

`例`<br>

```
Route::prefix('manager')
  ->middleware('can:manager-higer')->group(function() {
    Route::get('index', function () {
      dd('manager');
    });
  });

Route::middleware('can:user-higher')->group(function() {
  Route::get('index', function () {
    dd('user');
  });
});
```

### ハンズオン

- `routes/web.php`を編集<br>

```php:web.php
<?php

use App\Http\Controllers\AlpineTestController;
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

// 追加
Route::prefix('manager')
  ->middleware('can:manager-higher')
  ->group(function () {
    Route::get('index', function () {
      dd('manager');
    });
  });

Route::middleware('can:user-higher')->group(function () {
  Route::get('index', function () {
    dd('user');
  });
});
// ここまで

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

- locahost/manager/index にアクセスしてみる<br>

### カスタムエラーページ

php artisan vendor:publish --tag=laravel-errors<br>

views/errors フォルダがコピーされる<br>

マニュアルの基礎/エラー処理を参照<br>

### ハンズオン

- `$ php artisan vendor:publish --tag=laravel-errors`を実行<br>
