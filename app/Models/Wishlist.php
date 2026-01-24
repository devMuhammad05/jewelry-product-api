<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\WishlistFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Wishlist extends Model
{
    /** @use HasFactory<WishlistFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_token',
        'name',
        'expires_at',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<WishlistItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
