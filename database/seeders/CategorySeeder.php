<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::truncate();
        
        $categories = [
            'Makeup' => [
                'Face' => [
                    'Face Primer',
                    'Concealer',
                    'Foundation',
                    'Compact & Pressed Powder',
                    'Contour',
                    'Loose Powder',
                    'Blush',
                    'Bronzer',
                    'Tinted Moisturizer',
                    'BB & CC cream',
                    'Highlighters',
                    'Makeup Remover',
                    'Setting Spray',
                    'Face Pallete'
                ],
                'Eyes' => [
                    'Kajal',
                    'Eyeliner',
                    'Mascara',
                    'Eye Shadow',
                    'Eye Brow Enhancers',
                    'Eye Primer',
                    'False Eyelashes',
                    'Eye Makeup Remover',
                    'Under Eye Concealer'
                ],
                'Lips' => [
                    'Lipstick',
                    'Liquid Lipsticks',
                    'Lip Crayon',
                    'Lip Gloss',
                    'Lip Liner',
                    'Lip Plumper',
                    'Lip Stain',
                    'Lip Balm',
                    'Lip Exfoliator'
                ],
                'Nails' => [
                    'Nail Polish',
                    'Nail Art Kits',
                    'Nail Polish Sets',
                    'Nail Care',
                    'Nail Polish Remover',
                    'Manicure & Pedicure Kits'
                ],
                'Tools & Brushes' => [
                    'Face Brush',
                    'Blush Brush',
                    'Eye Brush',
                    'Lip Brush',
                    'Brush Sets',
                    'Eyelash Curlers',
                    'Sponges & Applicators',
                    'Tweezers',
                    'Sharpeners',
                    'Mirrors',
                    'Makeup Pouches'
                ],
                'Top Brands' => [
                    "L'Oreal",
                    'MAC',
                    'The Body Shop'
                ]
            ],
            'Skin' => [
                'Face' => [
                    'Cleanser',
                    'Day Cream',
                    'Night Cream',
                    'Day & Night Cream',
                    'Facewash',
                    'Masks & Peels',
                    'Sleeping Mask',
                    'Scrubs & Exfoliators',
                    'Toners & Astringents',
                    'Sheet Mask',
                    'Facial Wipes',
                    'Serums/Oils',
                    'Moisturizer',
                    'Facial Kit',
                    'Spot Remover',
                    'LIP BALMS/LIP CARE'
                ],
                'K-Beauty' => [
                    'Sunscreen',
                    'Moisturizers',
                    'Serums & Oils',
                    'Essence',
                    'Toners & Astringents',
                    'Sheet Mask',
                    'Face Packs & Peels',
                    'Scrubs & Exfoliators',
                    'Cleanser/Cleansing Oil',
                    'Sleeping mask/Mask',
                    'Face wash/Cleanser',
                    'Hair care',
                    'Eye cream/gel/patch',
                    'Facial Wipes',
                    'Cleansing oil',
                    'Ampoule'
                ],
                'Hand & Feet' => [
                    'Hand Creams',
                    'Foot Creams'
                ],
                'Body' => [
                    'Sunscreen',
                    'Body Butter',
                    'Body Mist/Spray',
                    'Deodorants/Roll-Ons',
                    'Lotions & Creams',
                    'Anti-Stretch Mark Creams',
                    'Body Scrubs'
                ],
                'Eye Care' => [
                    'Eye Cream',
                    'Eye Gel',
                    'Eye Roller',
                    'Under Eye Cream',
                    'Dark Circles / Wrinkles',
                    'Puffiness',
                    'Day/Night Cream',
                    'Eye Makeup Remover'
                ],
                'Shop By Concern' => [
                    'Skin Lightening',
                    'Sun Protection',
                    'Tan Removal',
                    'Pigmentation',
                    'Acne Treatment',
                    'Anti Aging',
                    'Oil Control',
                    'Cold Protection',
                    'Dull Skin Treatment',
                    'Pore Care',
                    'Dry Skin Remedy'
                ]
            ],
            'Hair' => [
                'Hair Care' => [
                    'Shampoo',
                    'Conditioner',
                    'Hair Oil',
                    'Hair Cream & Masks',
                    'Hair Serum',
                    'Rebonding Kit',
                    'Value Pack'
                ],
                'Hair Styling' => [
                    'Hair Color',
                    'Hair Spray',
                    'Gels & Waxes',
                    'Hair Primer'
                ],
                'Tools & Accessories' => [
                    'Hair Combs',
                    'Hair straightener',
                    'Hair Dryer',
                    'Hair Band'
                ],
                'Shop By Hair Type' => [
                    'Straight',
                    'Curly & Wavy'
                ],
                'Shop By Concern' => [
                    'Hairfall & Thinning',
                    'Dandruff',
                    'Dry & Frizzy Hair',
                    'Split Ends',
                    'Color Protection'
                ]
            ],
            'Personal Care' => [
                'Bath & Shower' => [
                    'Scrubs & Exfoliants',
                    'Shower Gels & Body Wash',
                    'Soaps',
                    'Talcum Powder'
                ],
                'Home Care' => [
                    'Candles',
                    'Air Freshener',
                    'Bathroom Essentials'
                ],
                'Tools & Accessories' => [
                    'Loofahs & Sponges',
                    'Travel Makeup Bag',
                    'Hair straightener'
                ],
                'Oral Care' => [
                    'Toothpaste',
                    'Tooth Brush',
                    'Mouthwash'
                ],
                'Feminine Hygiene' => [
                    'Shaving & Hair Removal',
                    'Feminine Cleanser',
                    'Sanitary Napkins'
                ],
                'Feminine Care' => [
                    'Breast Cream',
                    'Clothing & More'
                ],
                'Body' => [
                    'Toiletries',
                    'Lotions & Creams',
                    'Talcum Powder',
                    'Deodorants/Roll Ons',
                    'Sunscreen'
                ],
                'Face' => [
                    'Facewash',
                    'Moisturizer',
                    'Face Wipes'
                ],
                'Hands & Feet' => [
                    'Feet Puff',
                    'Foot Care',
                    'Foot Set',
                    'Foot Scrub',
                    'Hand Sanitizer',
                    'Hand Wash'
                ],
                'Wellness' => [
                    'Weightloss',
                    'Shop By Concern',
                    'Face Mask',
                    'Sexual Wellness',
                    'Health & Fitness'
                ]
            ],
            'Mom & Baby' => [
                'Baby Care' => [
                    'Bath Time',
                    'Creams, Lotions & Oils',
                    'Moisturizer',
                    'Sunscreen',
                    'Baby Care',
                    'Baby Products',
                    'Oil',
                    'Creams & Moisturizers',
                    'Lotion',
                    'Shampoo',
                    'Wipes',
                    'Soap & Bodywash',
                    'Powder'
                ]
            ],
            'Fragrance' => [
                'Perfumes' => [
                    'Perfume',
                    'High-end Perfume',
                    'EDP',
                    'EDT',
                    'Body Spray',
                    'Body Mist',
                    'Cologne',
                    'Ator',
                    'Deodorants/Roll-Ons',
                    'Unisex',
                    'For Women',
                    'For Men'
                ]
            ],
            'Men' => [
                'Shaving' => [
                    'Razor',
                    'Shaving cream, Foam & Gel',
                    'Shaving Brush',
                    'Beard and Moustache care',
                    'Aftershave Lotion/Balm',
                    'Trimmers'
                ],
                'Hair Care' => [
                    'Shampoo',
                    'Conditioner',
                    'Hair Oil',
                    'Hair Color',
                    'Hair Styling'
                ],
                'Skin Care' => [
                    'Face Wash',
                    'Moisturizer',
                    'Sunscreen',
                    'Face Masks & Peels',
                    'Scrubs & Exfoliators',
                    'Fairness',
                    'Whitening Cream'
                ],
                'Fragrances' => [
                    'Deodorant & Roll On',
                    'Body Spray',
                    'Perfume & Cologne'
                ],
                'Bath & Body' => [
                    'Body Wash & Shower Gel',
                    'Soap',
                    'Talcum Powder',
                    'Body Lotion'
                ],
                'Shop By Concern' => [
                    'Anti-Dandruff',
                    'Anti-Hairfall',
                    'Anti-Aging',
                    'Acne Solution',
                    'Oil Control',
                    'Dry Skin Care',
                    'Pore Care'
                ],
                'Top Brands' => [
                    'HE',
                    'Garnier',
                    'Addidas',
                    'Dunhill London',
                    'Nautica',
                    'Bigen',
                    'Neotrogena',
                    'Nivea Men',
                    'Fair And Lovely',
                    "Pond's Men"
                ]
            ],
            'Jewellery' => [
                'Accessories' => [
                    'Necklaces',
                    'Necklace Sets',
                    'Earring',
                    'Bracelets & Bangles'
                ]
            ]
        ];

        foreach ($categories as $mainCategory => $subCategories) {
            // Create main category
            $mainCat = Category::create([
                'name' => $mainCategory,
                'level' => 1
            ]);

            foreach ($subCategories as $subCategory => $items) {
                // Create sub category
                $subCat = Category::create([
                    'name' => $subCategory,
                    'parent_id' => $mainCat->id,
                    'level' => 2
                ]);

                // Create items under sub category
                foreach ($items as $item) {
                    Category::create([
                        'name' => $item,
                        'parent_id' => $subCat->id,
                        'level' => 3
                    ]);
                }
            }
        }
    }
}
