<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Attribute::unguard();
        AttributeValue::unguard();

        $attributes = [

            'Metal' => [
                'Platinum',
                'White Gold',
                'Yellow Gold',
                'Rose Gold',
            ],
            'Stone Shape' => [
                'Cushion',
                'Emerald',
                'Oval',
                'Round',
                'Pear',
            ],
            'Gender' => [
                'Women',
                'Men',
                'Unisex',
            ],
        ];

        foreach ($attributes as $attrName => $values) {
            $attribute = Attribute::query()->updateOrCreate(
                ['slug' => Str::slug($attrName)],
                ['name' => $attrName, 'type' => 'select']
            );

            foreach ($values as $valueName) {
                AttributeValue::query()->updateOrCreate(
                    ['slug' => Str::slug($valueName)],
                    ['attribute_id' => $attribute->id, 'value' => $valueName]
                );
            }
        }
    }
}
