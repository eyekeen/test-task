<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CarNewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // URL JSON-файла
        $url = 'https://tapir.ws/files/new_cars.json';

        // Загрузка данных из JSON
        $response = Http::get($url);

        if ($response->successful()) {

            $cars = $response->json();

            // Car::create($cars);

            // Сохранение данных в базу
            foreach ($cars as $carData) {
                Car::create([
                    'brand' => $carData['brand'],
                    'model' => $carData['model'],
                    'vin' => $carData['vin'],
                    'price' => $carData['price'],
                ]);
            }

            $this->command->info('Cars data seeded successfully!');
        } else {
            $this->command->error('Failed to fetch data from the URL.');
        }
    }
}
