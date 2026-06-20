<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Locations;

class LocalidadesLaRiojaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createLocalidades();
    }

    private function createLocalidades(){
        $data = [
            ["name"=>"Chañar", "state_id"=>1829],
            ["name"=>"Olta", "state_id"=>1829],
            ["name"=>"Portezuelo", "state_id"=>1829],
            ["name"=>"Malanzán", "state_id"=>1829],
            ["name"=>"Nácate", "state_id"=>1829],
            ["name"=>"San Antonio", "state_id"=>1829],
            ["name"=>"Milagro", "state_id"=>1829],
            ["name"=>"Ambil", "state_id"=>1829],
            ["name"=>"Colonia Ortiz de Ocampo", "state_id"=>1829],
            ["name"=>"Olpas", "state_id"=>1829],
            ["name"=>"Santa Rita de Catuna", "state_id"=>1829],
            ["name"=>"Ulapes", "state_id"=>1829],
            ["name"=>"Chepes", "state_id"=>1829],
            ["name"=>"Desiderio Tello", "state_id"=>1829],
            ["name"=>"Estación Mazán", "state_id"=>1829],
            ["name"=>"Aimogasta", "state_id"=>1829],
            ["name"=>"Bañado de los Pantanos", "state_id"=>1829],
            ["name"=>"Termas de Santa Teresita", "state_id"=>1829],
            ["name"=>"Villa Mazán", "state_id"=>1829],
            ["name"=>"La Rioja", "state_id"=>1829],
            ["name"=>"Aminga", "state_id"=>1829],
            ["name"=>"Anillaco", "state_id"=>1829],
            ["name"=>"Anjullón", "state_id"=>1829],
            ["name"=>"Chuquis", "state_id"=>1829],
            ["name"=>"Los Molinos", "state_id"=>1829],
            ["name"=>"Pinchas", "state_id"=>1829],
            ["name"=>"San Pedro", "state_id"=>1829],
            ["name"=>"Santa Vera Cruz", "state_id"=>1829],
            ["name"=>"Pagancillo", "state_id"=>1829],
            ["name"=>"Aicuña", "state_id"=>1829],
            ["name"=>"Los Palacios", "state_id"=>1829],
            ["name"=>"Villa Unión", "state_id"=>1829],
            ["name"=>"Chamical", "state_id"=>1829],
            ["name"=>"Polco", "state_id"=>1829],
            ["name"=>"Vichigasta", "state_id"=>1829],
            ["name"=>"Nonogasta", "state_id"=>1829],
            ["name"=>"Chilecito", "state_id"=>1829],
            ["name"=>"Anguinán", "state_id"=>1829],
            ["name"=>"Malligasta", "state_id"=>1829],
            ["name"=>"Guanchín", "state_id"=>1829],
            ["name"=>"Malligasta", "state_id"=>1829],
            ["name"=>"Miranda", "state_id"=>1829],
            ["name"=>"San Nicolás", "state_id"=>1829],
            ["name"=>"Santa Florentina", "state_id"=>1829],
            ["name"=>"Sañogasta", "state_id"=>1829],
            ["name"=>"Tilimuqui", "state_id"=>1829],
            ["name"=>"Vichigasta", "state_id"=>1829],
            ["name"=>"Angulos", "state_id"=>1829],
            ["name"=>"Chañarmuyo", "state_id"=>1829],
            ["name"=>"Alto Carrizal", "state_id"=>1829],
            ["name"=>"Antinaco", "state_id"=>1829],
            ["name"=>"Bajo Carrizal", "state_id"=>1829],
            ["name"=>"Campanas", "state_id"=>1829],
            ["name"=>"Famatina", "state_id"=>1829],
            ["name"=>"La Cuadra", "state_id"=>1829],
            ["name"=>"Pituil", "state_id"=>1829],
            ["name"=>"Plaza Vieja", "state_id"=>1829],
            ["name"=>"Santa Cruz", "state_id"=>1829],
            ["name"=>"Santo Domingo", "state_id"=>1829],
            ["name"=>"Punta de los Llanos", "state_id"=>1829],
            ["name"=>"Tama", "state_id"=>1829],
            ["name"=>"Villa Castelli", "state_id"=>1829],
            ["name"=>"Jagüé", "state_id"=>1829],
            ["name"=>"Villa San José de Vinchina", "state_id"=>1829],
            ["name"=>"Amaná", "state_id"=>1829],
            ["name"=>"Patquía", "state_id"=>1829],
            ["name"=>"Salicas - San Blas", "state_id"=>1829],
            ["name"=>"Villa Sanagasta", "state_id"=>1829],
            ["name"=>"Guandacol", "state_id"=>1829],
            ["name"=>"Castro Barros", "state_id"=>1829],
            ["name"=>"Loma Blanca", "state_id"=>1829],
            ["name"=>"Los Sarmientos", "state_id"=>1829],
            ["name"=>"Machigasta", "state_id"=>1829],
            ["name"=>"Aimogasta", "state_id"=>1829],
            ["name"=>"San Antonio", "state_id"=>1829],
            ["name"=>"Guandacol", "state_id"=>1829],
            ["name"=>"Santa Clara", "state_id"=>1829],
            ["name"=>"Banda Florida", "state_id"=>1829],
            ["name"=>"Villa Unión", "state_id"=>1829],
            ["name"=>"San Miguel", "state_id"=>1829],
            ["name"=>"Anguinán", "state_id"=>1829],
            ["name"=>"La Puntilla", "state_id"=>1829],
            ["name"=>"Amuschina", "state_id"=>1829],
            ["name"=>"Andolucas", "state_id"=>1829],
            ["name"=>"Chaupihuasi", "state_id"=>1829],
            ["name"=>"Las Talas", "state_id"=>1829],
            ["name"=>"Los Robles", "state_id"=>1829],
            ["name"=>"Salicas", "state_id"=>1829],
            ["name"=>"Shaqui", "state_id"=>1829],
            ["name"=>"Suriyaco", "state_id"=>1829],
            ["name"=>"Tuyubil", "state_id"=>1829],
            ["name"=>"Alpasinche", "state_id"=>1829],
            ["name"=>"Cuipán", "state_id"=>1829],
            ["name"=>"San Blas", "state_id"=>1829]
        ];
        Locations::insert($data);
    }
}
