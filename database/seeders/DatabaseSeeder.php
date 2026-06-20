<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Countries;
use App\Models\Currencies;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if(config('app.env') === 'develop' || config('app.env') === 'local' || config('app.env') === 'testing'){
            $this->createUserAdmin();
        }else{
            $this->createUserAdmin(true);
        }
        $this->createCountries();
        $this->createCurrencies();
        $StatesSeeder = new StatesSeeder();
        $StatesSeeder->run();
        $BuenosAires = new LocalidadesBuenosAiresSeeder();
        $BuenosAires->run();
        $catamarca = new LocalidadesCatamarcaSeeder();
        $catamarca->run();
        $LaRioja = new LocalidadesLaRiojaSeeder();
        $LaRioja->run();
        $products = new Productos26151Seeder();
        $products->run();
    }

    private function createUserAdmin($prod = false)
    {
        if ($prod) {
            $users = [
                [
                    'name' => 'Jorge Gonzalo',
                    'last_name' => 'Gaitan',
                    'email' => 'gaitan.jorgegonzalo959@gmail.com',
                    'role_id' => 1,
                    'country_id' => 5,
                    'province_id'=>1819,
                    'first_login'=>0,
                    'password' => bcrypt('Hsrrc*12Fg'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ];
        } else {
            $users = [
                [
                    'name' => 'Jorge Gonzalo',
                    'last_name' => 'Gaitan',
                    'email' => 'gaitan.jorgegonzalo959@gmail.com',
                    'role_id' => 1,
                    'country_id' => 5,
                    'province_id'=>1819,
                    'first_login'=>0,
                    'password' => bcrypt('Prueba123'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ];
        }
        User::insert($users);
    }

     private function createCountries(){
        $data= [
            ['id'=>5, 'name'=>'Argentina'],
            ['id'=>12,'name'=>'Brasil'],
            ['id'=>81,'name'=>'Chile'],
            ['id'=>89,'name'=>'Perú'],
            ['id'=>110,	'name'=>'Paraguay'],
            ['id'=>111,	'name'=>'Uruguay'],
            ['id'=>123,	'name'=>'Bolivia'],
        ];
        Countries::insert($data);
    }

    

     private function createCurrencies(){
        $currencies = [
            [
                'name'=>'ARS',
                'symbol'=>'$',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'=>'USD',
                'symbol'=>'u$s',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'=>'EUR',
                'symbol'=>'€',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        Currencies::insert($currencies);
    }
}
