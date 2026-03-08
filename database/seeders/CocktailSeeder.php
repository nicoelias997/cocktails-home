<?php

namespace Database\Seeders;

use App\Models\Cocktail;
use App\Models\User;
use Illuminate\Database\Seeder;

class CocktailSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $cocktails = [
            [
                'user_id'      => $user?->id,
                'name'         => 'Negroni',
                'alcohol_type' => 'gin',
                'photo_path'   => 'https://picsum.photos/seed/negroni/800/600',
                'attributes'   => [
                    'glass'       => 'rocks',
                    'garnish'     => 'orange peel',
                    'ingredients' => [
                        ['name' => 'Gin',             'amount' => '30ml'],
                        ['name' => 'Campari',          'amount' => '30ml'],
                        ['name' => 'Sweet vermouth',   'amount' => '30ml'],
                    ],
                    'instructions' => 'Stir with ice and strain into a rocks glass over a large ice cube.',
                    'difficulty'   => 'easy',
                ],
            ],
            [
                'user_id'      => $user?->id,
                'name'         => 'Margarita',
                'alcohol_type' => 'tequila',
                'photo_path'   => 'https://picsum.photos/seed/margarita/800/600',
                'attributes'   => [
                    'glass'       => 'coupe',
                    'garnish'     => 'salt rim & lime wheel',
                    'ingredients' => [
                        ['name' => 'Tequila blanco',  'amount' => '50ml'],
                        ['name' => 'Triple sec',       'amount' => '20ml'],
                        ['name' => 'Lime juice',       'amount' => '20ml'],
                    ],
                    'instructions' => 'Shake all ingredients with ice and strain into a salt-rimmed glass.',
                    'difficulty'   => 'easy',
                ],
            ],
            [
                'user_id'      => $user?->id,
                'name'         => 'Old Fashioned',
                'alcohol_type' => 'whiskey',
                'photo_path'   => 'https://picsum.photos/seed/oldfashioned/800/600',
                'attributes'   => [
                    'glass'       => 'rocks',
                    'garnish'     => 'orange peel & cherry',
                    'ingredients' => [
                        ['name' => 'Bourbon',          'amount' => '60ml'],
                        ['name' => 'Simple syrup',     'amount' => '5ml'],
                        ['name' => 'Angostura bitters', 'amount' => '2 dashes'],
                    ],
                    'instructions' => 'Add syrup and bitters to glass, add ice, pour bourbon and stir gently.',
                    'difficulty'   => 'easy',
                ],
            ],
            [
                'user_id'      => $user?->id,
                'name'         => 'Aperol Spritz',
                'alcohol_type' => 'aperol',
                'photo_path'   => 'https://picsum.photos/seed/aperolspritz/800/600',
                'attributes'   => [
                    'glass'       => 'wine glass',
                    'garnish'     => 'orange slice',
                    'ingredients' => [
                        ['name' => 'Aperol',           'amount' => '60ml'],
                        ['name' => 'Prosecco',         'amount' => '90ml'],
                        ['name' => 'Soda water',       'amount' => 'splash'],
                    ],
                    'instructions' => 'Build in a wine glass over ice: prosecco first, then Aperol, then soda.',
                    'difficulty'   => 'easy',
                ],
            ],
            [
                'user_id'      => $user?->id,
                'name'         => 'Daiquiri',
                'alcohol_type' => 'rum',
                'photo_path'   => 'https://picsum.photos/seed/daiquiri/800/600',
                'attributes'   => [
                    'glass'       => 'coupe',
                    'garnish'     => 'lime wheel',
                    'ingredients' => [
                        ['name' => 'White rum',        'amount' => '60ml'],
                        ['name' => 'Lime juice',       'amount' => '30ml'],
                        ['name' => 'Simple syrup',     'amount' => '15ml'],
                    ],
                    'instructions' => 'Shake hard with ice and double-strain into a chilled coupe.',
                    'difficulty'   => 'easy',
                ],
            ],
        ];

        foreach ($cocktails as $data) {
            Cocktail::create($data);
        }
    }
}
