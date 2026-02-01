<?php

declare(strict_types=1);

namespace App\Actions\Newsletter;

use App\Models\Subscriber;
use Illuminate\Support\Str;

final readonly class SubscribeAction
{
    /**
     * Subscribe an email to the newsletter.
     */
    public function execute(string $email): Subscriber
    {
        $subscriber = Subscriber::where('email', $email)->first();

        if ($subscriber) {
            if (! $subscriber->is_active) {
                $subscriber->update([
                    'is_active' => true,
                    'last_active_at' => now(),
                ]);
            }

            return $subscriber;
        }

        return Subscriber::create([
            'email' => $email,
            'unsubscribe_token' => (string) Str::uuid(),
            'is_active' => true,
            'last_active_at' => now(),
        ]);
    }
}
