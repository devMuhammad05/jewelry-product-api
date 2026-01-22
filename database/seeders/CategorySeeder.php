<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::unguard();

        $categories = [
            'Jewelry' => [
                'Rings',
                'Necklaces',
                'Bracelets',
                'Earrings',
            ],
            'Watches' => [
                'Women\'s Watches',
                'Men\'s Watches',
            ],
            'Bag' => [], // No children for now
            'Accessories' => [], // No children for now
        ];

        $index = 1;
        foreach ($categories as $parentName => $children) {
            $parent = Category::create([
                'name' => $parentName,
                'slug' => Str::slug($parentName),
                'description' => "Luxury {$parentName} collection.",
                'position' => $index++,
                'parent_id' => null,
            ]);

            foreach ($children as $childIndex => $childName) {
                Category::create([
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'description' => "Exclusive selection of {$childName}.",
                    'position' => $childIndex + 1,
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }
}
