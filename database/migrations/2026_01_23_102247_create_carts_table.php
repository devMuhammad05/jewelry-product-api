<?php

declare(strict_types=1);

use App\Enums\CartStatus;
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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->uuid('guest_token')->nullable()->index();
            $table->string('status')->default(CartStatus::Active->value)->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id'], 'active_user_cart')
                ->where('status', CartStatus::Active->value)
                ->where('user_id', 'IS NOT NULL');

            $table->unique(['guest_token'], 'active_guest_cart')
                ->where('status', CartStatus::Active->value)
                ->where('guest_token', 'IS NOT NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
