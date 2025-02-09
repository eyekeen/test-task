<?php

namespace App\Console\Commands;

use App\Models\Car;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportOldCars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-old-cars';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import old cars data from XML';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = 'https://tapir.ws/files/used_cars.xml';
        $response = Http::get($url);

        if ($response->successful()) {
            $xmlString = $response->body();
            $xml = new \SimpleXMLElement($xmlString);

            foreach ($xml->vehicle as $vehicleData) {
                Car::updateOrCreate(
                    ['vin' => $vehicleData->vin], // Уникальный ключ для проверки
                    [
                        'brand' => (string) $vehicleData->brand,
                        'model' => (string) $vehicleData->model,
                        'price' => (int) $vehicleData->price,
                        'year' => (int) $vehicleData->year,
                        'mileage' => (int) $vehicleData->mileage,
                    ]
                );
            }

            Log::info('Old cars imported successfully.');
        } else {
            Log::error('Failed to fetch old cars data.');
        }
    }
}
