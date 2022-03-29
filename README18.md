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
