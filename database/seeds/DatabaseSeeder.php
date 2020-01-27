<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Employee;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $employee1 = factory(Employee::class)->create([
            'name' => 'john@doe.com'
        ]);

        $employee2 = factory(Employee::class)->create([
            'name' => 'jane@doe.com'
        ]);

        $employeeTime1 =  DB::table('employee_time')->insert([
            'employee_id' => $employee1->id,
            'start' => '10:00',
            'end' => '13:00'
        ]);

        $employeeTime2 =  DB::table('employee_time')->insert([
            'employee_id' => $employee1->id,
            'start' => '14:00',
            'end' => '19:00'
        ]);

        $employeeTime3 =  DB::table('employee_time')->insert([
            'employee_id' => $employee2->id,
            'start' => '09:00',
            'end' => '12:00'
        ]);

        $employeeTime4 =  DB::table('employee_time')->insert([
            'employee_id' => $employee2->id,
            'start' => '13:00',
            'end' => '18:00'
        ]);

        $vacation1 =  DB::table('vacations')->insert([
            'employee_id' => $employee1->id,
            'vacation_from' => '2020-01-11',
            'vacation_to' => '2020-01-25'
        ]);

        $vacation2 =  DB::table('vacations')->insert([
            'employee_id' => $employee1->id,
            'vacation_from' => '2020-02-01',
            'vacation_to' => '2020-02-15'
        ]);

        $vacation3 =  DB::table('vacations')->insert([
            'employee_id' => $employee2->id,
            'vacation_from' => '2020-02-01',
            'vacation_to' => '2020-03-01'
        ]);
    }
}
