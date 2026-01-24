<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use App\Enums\UserRole;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

final class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'role',
        'password',
        'remember_token',
    ];

    /** @return HasMany<Cart, $this> */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /** @return HasMany<Wishlist, $this> */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /** @return HasOne<Wishlist, $this> */
    public function defaultWishlist(): HasOne
    {
        return $this->hasOne(Wishlist::class)->where('is_default', true);
    }

    /** @return HasOne<Cart, $this> */
    public function activeCart(): HasOne
    {
        return $this->hasOne(Cart::class)->where('status', 'Active');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function getFilamentName(): string
    {
        return $this->first_name ?? "Administrator";
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! app()->isProduction()) {
            return true; 
        }

        if ($this->role === UserRole::Admin) {
            return str_ends_with($this->email, config('admin.email')) && $this->hasVerifiedEmail();
        }

        return false;
    }
}
