<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CarOldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // URL XML-файла
        $url = 'https://tapir.ws/files/used_cars.xml';

        // Загрузка данных из XML
        $response = Http::get($url);

        if ($response->successful()) {
            $xmlString = $response->body();
            $xml = new \SimpleXMLElement($xmlString);

            // Обработка данных
            foreach ($xml->vehicle as $vehicleData) {
                Car::create([
                    'brand' => (string) $vehicleData->brand,
                    'model' => (string) $vehicleData->model,
                    'vin' => (string) $vehicleData->vin,
                    'price' => (int) $vehicleData->price,
                    'year' => (int) $vehicleData->year,
                    'mileage' => (int) $vehicleData->mileage,
                ]);
            }

            $this->command->info('Old vehicles data seeded successfully!');
        } else {
            $this->command->error('Failed to fetch data from the URL.');
        }
    }
}
