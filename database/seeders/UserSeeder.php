<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        $lastInsertedId = DB::table('users')->insertGetId([
            'username' => 'spider',
            'phone_number' => rand(1111111111,9999999999),
            'password' => Hash::make('spider.ph_origin'),
            'credits' => 110,
            'activation' => 1,
            'user_role' => 3,
            'referral_code' => 5912,
            'registered_date' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('users_info')->insert([
            'user_id' => $lastInsertedId
        ]);

        DB::table('fights')->insert([
            'betting_status' => 0,
            'fight_status' => 0,
            'fight_declaration' => 0,
            'fight_no' => 1
        ]);
    }
}
