<?php

declare(strict_types=1);

namespace App\Repositories\Subscription;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionDbRepository
{
    public function __construct(
        private readonly Subscription $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->get();
    }

    public function getById(int $id): Subscription
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Subscription
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Subscription
    {
        $subscription = $this->getById($id);
        $subscription->update($data);
        return $subscription->fresh();
    }

    public function delete(int $id): void
    {
        $this->getById($id)->delete();
    }

    public function getActiveForUser(int $userId): ?Subscription
    {
        return $this->model
            ->where(Subscription::USER_ID, $userId)
            ->whereIn(Subscription::STATUS, ['active', 'trialing'])
            ->first();
    }

    public function createFromStripe(int $userId, string $customerId, string $subscriptionId, string $status, ?string $trialEndsAt): Subscription
    {
        return $this->model->create([
            Subscription::USER_ID => $userId,
            Subscription::STATUS  => $status,
            'stripe_customer_id'     => $customerId,
            'stripe_subscription_id' => $subscriptionId,
            'trial_ends_at'          => $trialEndsAt,
        ]);
    }

    public function updateByStripeId(string $stripeSubscriptionId, array $data): void
    {
        \DB::table('subscriptions')
            ->where('stripe_subscription_id', $stripeSubscriptionId)
            ->update(array_merge($data, ['updated_at' => now()]));
    }

    public function findByStripeId(string $stripeSubscriptionId): ?object
    {
        return \DB::table('subscriptions')
            ->where('stripe_subscription_id', $stripeSubscriptionId)
            ->first();
    }
}