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
