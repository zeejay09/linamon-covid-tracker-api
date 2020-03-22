<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DEFINED ROLES
        DB::table('roles')->insert([
            'title' => 'Admin',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('roles')->insert([
            'title' => 'User',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
