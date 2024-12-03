<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubscriptionCollection;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class SubscriptionController extends Controller
{
    public function __construct(private Request $request)
    {
    }


    public function index(Request $request)
    {
        $request->validate([
            'search' => 'string|nullable',
            'page' => 'integer|nullable|min:1',
            'limit' => 'integer|nullable|min:1|max:100',
        ]);

        $query = Subscription::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
        }

        $subscriptions = $query->paginate($request->input('limit', 10));

        return new SubscriptionCollection($subscriptions);
    }

    public function store(StoreSubscriptionRequest $request)
    {
        $subscription = Subscription::create($request->validated());

        return new SubscriptionResource($subscription);
    }


    public function show(Subscription $subscription)
    {
        return new SubscriptionResource($subscription);
    }

    public function update(UpdateSubscriberRequest $request, Subscriber $subscriber)
    {
        // Обновляем данные подписчика
        $subscriber->update($request->validated());

        // Загружаем подписку и возвращаем результат
        return new SubscriberResource($subscriber->fresh()->load('subscription'));
    }


    public function destroy(Subscription $subscription)
    {
        // Проверяем, привязана ли подписка к подписчику
        if ($subscription->subscribers()->exists()) {
            return response()->json(['message' => 'Невозможно удалить подписку, она привязана к подписчику.'], 400);
        }

        // Если подписка не привязана к подписчику, удаляем
        $subscription->delete();

        return response()->noContent();
    }

}
