<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Config\Repository;
use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(array $attributes = [], private readonly Repository $repository)
    {
        parent::__construct($attributes);
    }

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

    /** @return HasMany<Address, $this> */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /** @return HasOne<Address, $this> */
    public function defaultAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('is_default', true);
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

    public function getFilamentName(): string
    {
        return $this->first_name ?? 'Administrator';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! app()->isProduction()) {
            return true;
        }

        if ($this->role === UserRole::Admin) {
            return str_ends_with($this->email, $this->repository->get('admin.email')) && $this->hasVerifiedEmail();
        }

        return false;
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
}
