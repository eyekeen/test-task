<?php

namespace App\Jobs;

use App\Models\Car;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportOldCar implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
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
