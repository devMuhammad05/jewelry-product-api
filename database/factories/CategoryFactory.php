<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
final class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        return [
            'parent_id' => null,
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'position' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the category is a subcategory.
     */
    public function sub(?int $parentId = null): static
    {
        return $this->state(fn(array $attributes) => [
            'parent_id' => $parentId ?? Category::factory(),
        ]);
    }
}
