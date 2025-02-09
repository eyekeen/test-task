<?php

namespace App\Jobs;

use App\Models\Car;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportNewCar implements ShouldQueue
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
        $url = 'https://tapir.ws/files/new_cars.json';
        $response = Http::get($url);

        if ($response->successful()) {
            $cars = $response->json();

            foreach ($cars as $carData) {
                Car::updateOrCreate(
                    ['vin' => $carData['vin']], // Уникальный ключ для проверки
                    [
                        'brand' => $carData['brand'],
                        'model' => $carData['model'],
                        'price' => $carData['price'],
                        'year' => $carData['year'] ?? date('Y'),
                    ]
                );
            }

            Log::info('New cars imported successfully.');
        } else {
            Log::error('Failed to fetch new cars data.');
        }
    }
}
