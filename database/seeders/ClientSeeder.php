<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\ClientPayment;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client1 = Client::create([
            'first_name' => 'Jessa',
            'last_name' => 'Ruedas',
            'address' => 'Nasugbu, Batangas',
            'reference' => 'JESSA',
            'created_at' => '2022-04-19',
            'updated_at' => '2022-04-19'
        ]);

        ClientPayment::create([
            'client_id' => $client1->id,
            'amount' => 333,
            'date' => '2022-05-19',
            'status' => 'unpaid'
        ]);

        ClientPayment::create([
            'client_id' => $client1->id,
            'amount' => 444,
            'date' => '2022-06-19',
            'status' => 'unpaid'
        ]);

        ClientPayment::create([
            'client_id' => $client1->id,
            'amount' => 555,
            'date' => '2022-07-19',
            'status' => 'unpaid'
        ]);

        ClientPayment::create([
            'client_id' => $client1->id,
            'amount' => 666,
            'date' => '2022-08-19',
            'status' => 'unpaid'
        ]);

        ClientPayment::create([
            'client_id' => $client1->id,
            'amount' => 777,
            'date' => '2022-09-19',
            'status' => 'unpaid'
        ]);

        ClientPayment::create([
            'client_id' => $client1->id,
            'amount' => 888,
            'date' => '2022-10-19',
            'status' => 'unpaid'
        ]);

        $client2 = Client::create([
            'first_name' => 'Gian',
            'last_name' => 'Hernandez',
            'address' => 'Nasugbu, Batangas',
            'reference' => 'GIAN',
            'created_at' => '2022-05-19',
            'updated_at' => '2022-05-19'
        ]);

        ClientPayment::create([
            'client_id' => $client2->id,
            'amount' => 111,
            'date' => '2022-06-19',
            'status' => 'unpaid'
        ]);

        ClientPayment::create([
            'client_id' => $client2->id,
            'amount' => 555,
            'date' => '2022-07-19',
            'status' => 'unpaid'
        ]);

        ClientPayment::create([
            'client_id' => $client2->id,
            'amount' => 222,
            'date' => '2022-08-19',
            'status' => 'unpaid'
        ]);

        ClientPayment::create([
            'client_id' => $client2->id,
            'amount' => 400,
            'date' => '2022-09-19',
            'status' => 'unpaid'
        ]);

        ClientPayment::create([
            'client_id' => $client2->id,
            'amount' => 300,
            'date' => '2022-10-19',
            'status' => 'unpaid'
        ]);

        $client3 = Client::create([
            'first_name' => 'Ian',
            'last_name' => 'Vivas',
            'address' => 'Nasugbu, Batangas',
            'reference' => 'IAN',
            'created_at' => '2022-06-19',
            'updated_at' => '2022-06-19'
        ]);

        ClientPayment::create([
            'client_id' => $client3->id,
            'amount' => 555,
            'date' => '2022-07-19',
            'status' => 'unpaid'
        ]);

        ClientPayment::create([
            'client_id' => $client3->id,
            'amount' => 222,
            'date' => '2022-08-19',
            'status' => 'unpaid'
        ]);

        ClientPayment::create([
            'client_id' => $client3->id,
            'amount' => 400,
            'date' => '2022-09-19',
            'status' => 'unpaid'
        ]);

        ClientPayment::create([
            'client_id' => $client3->id,
            'amount' => 300,
            'date' => '2022-10-19',
            'status' => 'unpaid'
        ]);

        $client4 = Client::create([
            'first_name' => 'Koby',
            'last_name' => 'Caldozo',
            'address' => 'Nasugbu, Batangas',
            'reference' => 'KOBY',
            'created_at' => '2022-07-19',
            'updated_at' => '2022-07-19'
        ]);

        ClientPayment::create([
            'client_id' => $client4->id,
            'amount' => 222,
            'date' => '2022-08-19',
            'status' => 'unpaid'
        ]);

        ClientPayment::create([
            'client_id' => $client4->id,
            'amount' => 400,
            'date' => '2022-09-19',
            'status' => 'unpaid'
        ]);

        ClientPayment::create([
            'client_id' => $client4->id,
            'amount' => 300,
            'date' => '2022-10-19',
            'status' => 'unpaid'
        ]);
    }
}
