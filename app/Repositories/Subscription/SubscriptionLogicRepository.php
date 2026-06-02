<?php

declare(strict_types=1);

namespace App\Repositories\Subscription;

use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionLogicRepository
{
    public function __construct(
        private readonly SubscriptionDbRepository $db
    ) {}

    public function getAll(): Collection
    {
        return $this->db->getAll();
    }

    public function getById(int $id): Subscription
    {
        return $this->db->getById($id);
    }

    public function create(array $data): Subscription
    {
        return $this->db->create($data);
    }

    public function update(int $id, array $data): Subscription
    {
        return $this->db->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->db->delete($id);
    }

    public function getActiveForUser(int $userId): ?Subscription
    {
        return $this->db->getActiveForUser($userId);
    }

    public function cancel(int $userId): void
    {
        $sub = $this->db->getActiveForUser($userId);

        if (!$sub) {
            throw new \InvalidArgumentException('No active subscription found');
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        \Stripe\Subscription::retrieve($sub->stripe_subscription_id)->cancel();

        $sub->update(['status' => 'cancelled']);
        User::find($userId)->update(['role' => 'user']);
    }

    public function createCheckoutSession(int $userId): string
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'product_data' => ['name' => 'Trainer Subscription'],
                    'unit_amount'  => 2999,
                    'recurring'    => ['interval' => 'month'],
                ],
                'quantity' => 1,
            ]],
            'mode'              => 'subscription',
            'subscription_data' => ['trial_period_days' => 30],
            'success_url'       => 'http://localhost:5173/payment/success',
            'cancel_url'        => 'http://localhost:5173/payment/cancel',
            'metadata'          => ['user_id' => $userId],
        ]);

        return $session->url;
    }

    public function handleCheckoutCompleted(object $data): void
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $userId         = $data->metadata->user_id;
        $subscriptionId = $data->subscription;
        $customerId     = $data->customer;
        $subscription   = \Stripe\Subscription::retrieve($subscriptionId);

        User::find($userId)?->update(['role' => 'trainer']);

        $this->db->createFromStripe(
            $userId,
            $customerId,
            $subscriptionId,
            'trialing',
            Carbon::createFromTimestamp($subscription->trial_end)->toDateTimeString()
        );
    }

    public function handleSubscriptionUpdated(object $data): void
    {
        $this->db->updateByStripeId($data->id, ['status' => $data->status]);
    }

    public function handleSubscriptionDeleted(object $data): void
    {
        $this->db->updateByStripeId($data->id, [
            'status'  => 'cancelled',
            'ends_at' => now(),
        ]);

        $sub = $this->db->findByStripeId($data->id);
        if ($sub) {
            User::find($sub->user_id)?->update(['role' => 'user']);
        }
    }
}