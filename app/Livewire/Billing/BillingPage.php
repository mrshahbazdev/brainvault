<?php

namespace App\Livewire\Billing;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BillingPage extends Component
{
    public function render()
    {
        $user = Auth::user();

        $plans = [
            [
                'id' => 'free',
                'name' => 'Free',
                'price' => 0,
                'period' => 'forever',
                'features' => [
                    '100 bookmarks',
                    '50 notes',
                    '20 highlights/day',
                    'Basic search',
                    '3 collections',
                    'Chrome extension',
                ],
            ],
            [
                'id' => 'pro',
                'name' => 'Pro',
                'price' => 8,
                'period' => 'month',
                'stripe_price_id' => config('services.stripe.pro_price_id'),
                'features' => [
                    'Unlimited everything',
                    'AI summaries & keywords',
                    'Semantic search',
                    'Knowledge graph',
                    'Research projects',
                    'Import/export',
                    'API access',
                    'Priority support',
                ],
            ],
            [
                'id' => 'team',
                'name' => 'Team',
                'price' => 15,
                'period' => 'user/month',
                'stripe_price_id' => config('services.stripe.team_price_id'),
                'features' => [
                    'Everything in Pro',
                    'Team workspaces',
                    'Shared collections',
                    'Role-based access',
                    'Admin dashboard',
                    'Activity audit logs',
                    'Dedicated support',
                ],
            ],
        ];

        $subscription = null;
        if ($user->subscribed('default')) {
            $subscription = $user->subscription('default');
        }

        return view('livewire.billing.billing-page', [
            'plans' => $plans,
            'currentPlan' => $user->plan ?? 'free',
            'subscription' => $subscription,
        ])->layout('layouts.app', ['title' => 'Billing']);
    }

    public function subscribe(string $priceId): void
    {
        $user = Auth::user();

        if (!$user->hasStripeId()) {
            $user->createAsStripeCustomer();
        }

        $checkout = $user->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => route('billing') . '?success=1',
                'cancel_url' => route('billing') . '?canceled=1',
            ]);

        $this->redirect($checkout->url);
    }

    public function manageBilling(): void
    {
        $user = Auth::user();

        if ($user->hasStripeId()) {
            $url = $user->billingPortalUrl(route('billing'));
            $this->redirect($url);
        }
    }
}
