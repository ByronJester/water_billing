<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\ClientPayment;
use Illuminate\Support\Str;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Client::create([
            'first_name' => 'Jessa',
            'middle_name' => 'Palabrica',
            'last_name' => 'Ruedas',
            'address' => 'Catandaan, Nasugbu, Batangas',
            'phone' => '09657657443',
            'reference' => strtoupper(Str::random(8))
        ]);
    }
}
