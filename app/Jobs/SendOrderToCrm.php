<?php

namespace App\Jobs;

use App\Mail\CrmErrorNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Orchid\Support\Facades\Toast;

class SendOrderToCrm implements ShouldQueue
{
    use Queueable;


    public $order;

    /**
     * Create a new job instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $crmUrl = 'https://crm.tapir.ws/api/crm';
        $crmData = [
            'phone' => "+7999999999",
            'VIN' => "vin auto",
        ];

        $maxAttempts = 30; // Максимум 30 попыток (по 10 секунд каждая = 5 минут)
        $attempt = 0;
        $success = false;

        while ($attempt < $maxAttempts) {
            $response = Http::post($crmUrl, $crmData);
            if ($response->successful()) {
                // Успешная отправка
                Log::info('CRM data sent successfully.', ['order_id' => $this->order->id]);

                // Обновляем статус на 'done'
                $this->order->update(['status' => 'done']);
                $success = true;
                break;
            } elseif ($response->status() === 500) {
                // Ошибка 500: повторяем через 10 секунд
                Log::warning('CRM returned 500 error. Retrying...', ['order_id' => $this->order->id]);
                sleep(10); // Задержка 10 секунд
            } else {
                // Другие ошибки: завершаем попытки
                Log::error('CRM returned unexpected error.', ['status' => $response->status(), 'order_id' => $this->order->id]);
                break;
            }

            $attempt++;
        }

        

        // Если отправка не удалась за 5 минут
        if (!$success) {
            Log::error('Failed to send CRM data after 5 minutes.', ['order_id' => $this->order->id]);

            Mail::to('admin@admin.com')->send(new CrmErrorNotification($this->order));

            // Обновляем статус на 'error'
            $this->order->update(['status' => 'error']);
        }
    }

    public function failed(\Throwable $exception)
    {
        // Логируем ошибку
        Log::error('Job failed for order ID: ' . $this->order->id, ['error' => $exception->getMessage()]);

        // Обновляем статус на 'error'
        $this->order->update(['status' => 'error']);
    }
}
