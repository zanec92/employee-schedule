<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user1 = factory(User::class)->create([
            'email' => 'john@doe.com'
        ]);

        $user2 = factory(User::class)->create([
            'email' => 'jane@doe.com'
        ]);

        $schedule1 =  DB::table('schedules')->insert([
            'user_id' => $user1->id,
            'start' => '10:00',
            'end' => '13:00'
        ]);

        $schedule2 =  DB::table('schedules')->insert([
            'user_id' => $user1->id,
            'start' => '14:00',
            'end' => '19:00'
        ]);

        $schedule3 =  DB::table('schedules')->insert([
            'user_id' => $user2->id,
            'start' => '09:00',
            'end' => '12:00'
        ]);

        $schedule4 =  DB::table('schedules')->insert([
            'user_id' => $user2->id,
            'start' => '13:00',
            'end' => '18:00'
        ]);

        $vacation1 =  DB::table('vacations')->insert([
            'user_id' => $user1->id,
            'vacation_from' => '2020-01-11',
            'vacation_to' => '2020-01-25'
        ]);

        $vacation2 =  DB::table('vacations')->insert([
            'user_id' => $user1->id,
            'vacation_from' => '2020-02-01',
            'vacation_to' => '2020-02-15'
        ]);

        $vacation3 =  DB::table('vacations')->insert([
            'user_id' => $user2->id,
            'vacation_from' => '2020-02-01',
            'vacation_to' => '2020-03-01'
        ]);
    }
}
