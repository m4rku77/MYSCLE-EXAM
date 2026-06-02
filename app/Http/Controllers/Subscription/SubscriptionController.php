<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Repositories\Subscription\SubscriptionLogicRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionLogicRepository $logic
    ) {}

    // GET /my/subscription
    public function show(): JsonResponse
    {
        return response()->json(
            $this->logic->getActiveForUser(auth()->id())
        );
    }

    // DELETE /my/subscription
    public function cancel(): JsonResponse
    {
        try {
            $this->logic->cancel(auth()->id());
            return response()->json(['message' => 'Cancelled']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    // POST /stripe/checkout
    public function checkout(): JsonResponse
    {
        $url = $this->logic->createCheckoutSession(auth()->id());
        return response()->json(['url' => $url]);
    }

    // POST /stripe/webhook
    public function webhook(Request $request): JsonResponse
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $sig     = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig, config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        match ($event->type) {
            'checkout.session.completed'   => $this->logic->handleCheckoutCompleted($event->data->object),
            'customer.subscription.updated' => $this->logic->handleSubscriptionUpdated($event->data->object),
            'customer.subscription.deleted' => $this->logic->handleSubscriptionDeleted($event->data->object),
            default => null,
        };

        return response()->json(['status' => 'ok']);
    }
}