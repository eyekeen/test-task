<?php

namespace App\Console\Commands;

use App\Models\Car;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportNewCars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-new-cars';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import new cars data from JSON';

    /**
     * Execute the console command.
     */
    public function handle()
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

            $this->info('New cars imported successfully.');
        } else {
            $this->error('Failed to fetch new cars data.');
        }
    }
}
