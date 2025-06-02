<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Attribute;
use App\Models\AttributeValue;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Size',
                'type' => 'select',
                'values' => ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL']
            ],
            [
                'name' => 'Color',
                'type' => 'select',
                'values' => ['Red', 'Pink', 'Nude', 'Coral', 'Peach', 'Rose Gold', 'Purple', 'Blue', 'Green', 'Black', 'White', 'Silver', 'Gold']
            ],
            [
                'name' => 'Material',
                'type' => 'select',
                'values' => ['Cotton', 'Polyester', 'Spandex', 'Lace', 'Silk', 'Satin', 'Velvet', 'Wool', 'Cashmere', 'Denim']
            ],
            [
                'name' => 'Skin Type',
                'type' => 'select',
                'values' => ['Normal', 'Dry', 'Oily', 'Combination', 'Sensitive', 'Mature']
            ],
            [
                'name' => 'Hair Type',
                'type' => 'select',
                'values' => ['Straight', 'Wavy', 'Curly', 'Coily', 'Fine', 'Medium', 'Thick', 'Damaged', 'Color-Treated']
            ],
            [
                'name' => 'Age Group',
                'type' => 'select',
                'values' => ['Teen', 'Young Adult', 'Adult', 'Mature']
            ],
            [
                'name' => 'Season',
                'type' => 'select',
                'values' => ['Spring', 'Summer', 'Fall', 'Winter', 'All Seasons']
            ],
            [
                'name' => 'Style',
                'type' => 'select',
                'values' => ['Casual', 'Elegant', 'Classic', 'Bohemian', 'Vintage', 'Modern', 'Sporty', 'Formal']
            ],
            [
                'name' => 'Fragrance Type',
                'type' => 'select',
                'values' => ['Floral', 'Fruity', 'Woody', 'Fresh', 'Oriental', 'Gourmand', 'Citrus', 'Aquatic']
            ],
            [
                'name' => 'Makeup Type',
                'type' => 'select',
                'values' => ['Foundation', 'Concealer', 'Blush', 'Eyeshadow', 'Mascara', 'Lipstick', 'Lip Gloss', 'Eyeliner', 'Bronzer', 'Highlighter']
            ],
            [
                'name' => 'Skin Concern',
                'type' => 'select',
                'values' => ['Acne', 'Anti-aging', 'Brightening', 'Hydration', 'Pores', 'Redness', 'Dark Spots', 'Sensitivity', 'Uneven Texture']
            ],
            [
                'name' => 'Hair Concern',
                'type' => 'select',
                'values' => ['Dryness', 'Frizz', 'Dandruff', 'Hair Loss', 'Split Ends', 'Color Protection', 'Volume', 'Smoothness']
            ]
        ];

        Attribute::truncate();
        AttributeValue::truncate();

        foreach ($attributes as $attributeData) {
            $attribute = Attribute::create([
                'name' => $attributeData['name'],
                'type' => $attributeData['type'],
                'is_visible' => true
            ]);

            foreach ($attributeData['values'] as $index => $value) {
                AttributeValue::create([
                    'attribute_id' => $attribute->id,
                    'value' => $value
                ]);
            }
        }
    }
}
