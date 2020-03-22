<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DEFINED BARANGAY
        DB::table('barangays')->insert([
            'brgy_name' => 'Busque',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('barangays')->insert([
            'brgy_name' => 'Larapan',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('barangays')->insert([
            'brgy_name' => 'Magoong',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('barangays')->insert([
            'brgy_name' => 'Napo',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('barangays')->insert([
            'brgy_name' => 'Poblacion',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('barangays')->insert([
            'brgy_name' => 'Purakan',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('barangays')->insert([
            'brgy_name' => 'Robocon',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('barangays')->insert([
            'brgy_name' => 'Samburon',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
