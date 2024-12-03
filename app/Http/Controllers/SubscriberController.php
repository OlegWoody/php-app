<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubscriberCollection;
use App\Http\Resources\SubscriberResource;
use App\Models\Subscriber;
use App\Http\Requests\StoreSubscriberRequest;
use App\Http\Requests\UpdateSubscriberRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class SubscriberController extends Controller
{
    public function __construct(private Request $request)
    {
    }

    public function index(Request $request)
    {
        // Логируем входные данные запроса
        Log::info('Request data for index method:', $request->all());

        // Валидация данных запроса
        $request->validate([
            'search' => 'string|nullable',
            'page' => 'integer|nullable|min:1',
            'limit' => 'integer|nullable|min:1|max:100',
        ]);

        $query = Subscriber::with('subscription'); // Загрузка подписки для всех подписчиков

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $subscribers = $query->paginate($request->input('limit', 10));

        return new SubscriberCollection($subscribers);
    }

    public function store(StoreSubscriberRequest $request)
    {
        // Логируем данные запроса перед созданием
        Log::info('Request data for store method:', $request->all());

        // Создаем подписчика с валидацией
        $subscriber = Subscriber::create($request->validated());

        // Логируем успешное создание
        Log::info('Subscriber created:', ['subscriber_id' => $subscriber->id]);

        // Возвращаем подписчика с его подпиской
        return new SubscriberResource($subscriber->load('subscription'));
    }

    public function show(Subscriber $subscriber)
    {
        // Логируем запрос на показ подписчика
        Log::info('Request to show subscriber:', ['subscriber_id' => $subscriber->id]);

        // Возвращаем подписчика с его подпиской
        return new SubscriberResource($subscriber->load('subscription'));
    }

    public function update(UpdateSubscriberRequest $request, Subscriber $subscriber)
    {
        // Логируем данные запроса для обновления
        Log::info('Request data for update method:', $request->all());

        // Обновляем подписчика
        $subscriber->update($request->validated());

        // Логируем успешное обновление
        Log::info('Subscriber updated:', ['subscriber_id' => $subscriber->id]);

        return new SubscriberResource($subscriber->fresh()->load('subscription'));
    }

    public function destroy(Subscriber $subscriber)
    {
        // Логируем запрос на удаление подписчика
        Log::info('Request to destroy subscriber:', ['subscriber_id' => $subscriber->id]);

        $subscriber->delete();

        return response()->noContent();
    }
}
