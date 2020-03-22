<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DEFINED USERS
        DB::table('users')->insert([
            'first_name' => 'Jeff Zeejay',
            'last_name' => 'Belamide',
            'email' => 'zeejaybelamide@gmail.com',
            'password' => bcrypt('adminadmin'),
            'department' => 'IT Department',
            'position' => 'Admin',
            'barangay_id' => 5,
            'role_id' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
