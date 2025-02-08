<?php

namespace App\Http\Controllers;

use App\Jobs\SendOrderToCrm;
use App\Mail\CrmErrorNotification;
use App\Mail\OrderCreated;
use App\Models\Car;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::query();

        // Фильтрация по году выпуска
        if ($request->has('year_from')) {
            $query->where('year', '>=', $request->input('year_from'));
        }

        if ($request->has('year_to')) {
            $query->where('year', '<=', $request->input('year_to'));
        }

        // Фильтрация по цене ниже
        if ($request->has('price_less')) {
            $query->where('price', '<=', $request->input('price_less'));
        }

        // Фильтрация по цене выше
        if ($request->has('price_higher')) {
            $query->where('price', '>=', $request->input('price_higher'));
        }

        // Фильтрация по бренду (используем LIKE)
        if ($request->has('brand')) {
            $query->whereRaw('LOWER(brand) LIKE ?', ['%' . strtolower($request->input('brand')) . '%']);
        }

        // Фильтрация по модели (используем LIKE)
        if ($request->has('model')) {
            $query->whereRaw('LOWER(model) LIKE ?', ['%' . strtolower($request->input('model')) . '%']);
        }

        // Фильтрация по минимальному пробегу
        if ($request->has('mileage_from')) {
            $query->where('mileage', '>=', $request->input('mileage_from'));
        }

        // Фильтрация по максимальному пробегу
        if ($request->has('mileage_to')) {
            $query->where('mileage', '<=', $request->input('mileage_to'));
        }



        // Получение отфильтрованных данных
        $cars = $query->paginate(10);

        return response()->json($cars);
    }

    function store(Request $request)
    {
        // Валидация входных данных
        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^\+?[0-9]{10,15}$/'], // Пример регулярного выражения для номера телефона
            'vin' => ['required', 'string', 'exists:cars,vin'], // Проверка, что VIN существует в таблице cars
        ]);

        // Создание записи в таблице orders
        $order = Order::create([
            'phone' => $validated['phone'],
            'vin' => $validated['vin'],
        ]);

        // Mail::to('admin@example.com')->send(new OrderCreated($order));
        Mail::to('admin@example.com')->send(new OrderCreated($order));

        // Отправка задачи в очередь
        SendOrderToCrm::dispatch($order);

        // Возвращаем успешный ответ
        return response()->json([
            'message' => 'Order created successfully.',
            'data' => $order,
        ], 201);
    }
}
