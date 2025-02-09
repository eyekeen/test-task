<?php

namespace App\Orchid\Screens;

use App\Jobs\SendOrderToCrm;
use App\Models\Order;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Color;
use Orchid\Support\Facades\Toast;

class RetryOrdersScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {

        return [
            'orders' => Order::where('status', 'error')->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Retry Failed Orders';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Refresh')
                ->icon('refresh') // Иконка обновления
                ->type(Color::INFO)
                ->method('refreshData'), // Метод, который будет вызван при нажатии
        ];
    }

    public function refreshData()
    {
        // Показываем уведомление
        Toast::info('Data refreshed successfully.');

        // Перенаправляем пользователя обратно на экран
        return redirect()->route('platform.retry-orders');
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {

        return [
            Layout::table('orders', [
                TD::make('id', 'ID'),
                TD::make('phone', 'Phone'),
                TD::make('vin', 'VIN'),
                TD::make('status', 'Status'),
                TD::make('actions', 'Actions')
                    ->render(function (Order $order) {
                        return Button::make('Retry')
                            ->icon('refresh')
                            ->type(Color::PRIMARY)
                            ->method('retryOrder', ['id' => $order->id]);
                    }),
            ]),
        ];
    }

    /**
     * Retry order and update status.
     *
     * @param Request $request
     */
    public function retryOrder(Request $request)
    {
        $order = Order::findOrFail($request->get('id'));

        try {
            // Отправляем задачу в очередь
            SendOrderToCrm::dispatch($order);

            // Обновляем статус на 'processing'
            $order->update(['status' => 'processing']);

            // Выводим уведомление
            Toast::info('Order #' . $order->id . ' has been sent for processing.');
        } catch (\Throwable $th) {
            Toast::error('Failed to process order #' . $order->id . ': ' . $th->getMessage(), 'error');
        }
    }
}
