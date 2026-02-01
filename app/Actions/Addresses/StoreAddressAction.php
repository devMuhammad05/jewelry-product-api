<?php

declare(strict_types=1);

namespace App\Actions\Addresses;

use App\Models\Address;
use Illuminate\Database\DatabaseManager;

final readonly class StoreAddressAction
{
    public function __construct(private DatabaseManager $databaseManager) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(int $userId, array $data): Address
    {
        return $this->databaseManager->transaction(function () use ($userId, $data) {
            if ($data['is_default'] ?? false) {
                Address::query()
                    ->where('user_id', $userId)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            return Address::create(array_merge($data, [
                'user_id' => $userId,
            ]));
        });
    }
}
