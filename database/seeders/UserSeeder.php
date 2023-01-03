<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserPlace;
use App\Models\Maintenance;
use App\Models\WaterBill;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'phone' => '09566814383',
            'email' => 'admin@gmail.com',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'role' => '1',
            'is_change_password' => true
        ]);

        // User::create([
        //     'name' => 'Client Client',
        //     'phone' => random_int(10000000000, 99999999999),
        //     'email' => 'client@gmail.com',
        //     'password' => Hash::make('password'),
        //     'user_type' => 'client',
        //     'role' => '2',
        //     'reference' => strtoupper(Str::random(8)) 
        // ]);

        Maintenance::create([
            'is_active' => false
        ]);

        WaterBill::create([
            'user_id' => 1,
            'amount' => 10
        ]);
    }
}
