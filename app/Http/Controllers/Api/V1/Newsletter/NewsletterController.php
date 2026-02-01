<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Newsletter;

use Illuminate\Contracts\Routing\ResponseFactory;
use App\Actions\Newsletter\SubscribeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Newsletter\SubscribeRequest;
use Illuminate\Http\JsonResponse;

final class NewsletterController extends Controller
{
    public function __construct(private readonly ResponseFactory $responseFactory)
    {
    }
    /**
     * Subscribe to the newsletter.
     */
    public function subscribe(SubscribeRequest $request, SubscribeAction $action): JsonResponse
    {
        $subscriber = $action->execute($request->validated('email'));

        return $this->responseFactory->json([
            'message' => 'Successfully subscribed to the newsletter.',
            'subscriber' => [
                'email' => $subscriber->email,
                'is_active' => $subscriber->is_active,
            ],
        ]);
    }
}
