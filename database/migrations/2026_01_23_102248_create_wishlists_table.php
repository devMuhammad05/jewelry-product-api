<?php

declare(strict_types=1);

use App\Enums\WishlistVisibility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->uuid('guest_token')->nullable()->index();
            $table->string('name')->default('My Wishlist');
            $table->boolean('is_default')->default(false);
            $table->enum('visibility', array_column(WishlistVisibility::cases(), 'value'))
                ->default(WishlistVisibility::Private->value);
            $table->uuid('share_token')->nullable()->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'is_default'], 'unique_default_wishlist')
                ->where('is_default', true)
                ->where('user_id', 'IS NOT NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
