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
                'name'         => 'Negroni',
                'alcohol_type' => 'gin',
                'attributes'   => [
                    'glass'        => 'rocks',
                    'garnish'      => 'orange peel',
                    'difficulty'   => 'easy',
                    'instructions' => 'Stir all ingredients with ice and strain into a rocks glass over a large ice cube.',
                    'ingredients'  => [
                        ['name' => 'Gin',           'amount' => '30ml'],
                        ['name' => 'Campari',        'amount' => '30ml'],
                        ['name' => 'Sweet vermouth', 'amount' => '30ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Gin & Tonic',
                'alcohol_type' => 'gin',
                'attributes'   => [
                    'glass'        => 'highball',
                    'garnish'      => 'lime wedge & juniper berries',
                    'difficulty'   => 'easy',
                    'instructions' => 'Fill a highball glass with ice, pour gin, top with tonic water and garnish.',
                    'ingredients'  => [
                        ['name' => 'Gin',        'amount' => '50ml'],
                        ['name' => 'Tonic water', 'amount' => '150ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Tom Collins',
                'alcohol_type' => 'gin',
                'attributes'   => [
                    'glass'        => 'collins',
                    'garnish'      => 'lemon slice & cherry',
                    'difficulty'   => 'easy',
                    'instructions' => 'Shake gin, lemon juice and syrup with ice. Strain into ice-filled glass and top with soda.',
                    'ingredients'  => [
                        ['name' => 'Gin',          'amount' => '45ml'],
                        ['name' => 'Lemon juice',  'amount' => '30ml'],
                        ['name' => 'Simple syrup', 'amount' => '15ml'],
                        ['name' => 'Soda water',   'amount' => 'top'],
                    ],
                ],
            ],
            [
                'name'         => 'Gimlet',
                'alcohol_type' => 'gin',
                'attributes'   => [
                    'glass'        => 'coupe',
                    'garnish'      => 'lime wheel',
                    'difficulty'   => 'easy',
                    'instructions' => 'Shake gin and lime juice with ice and double-strain into a chilled coupe.',
                    'ingredients'  => [
                        ['name' => 'Gin',         'amount' => '60ml'],
                        ['name' => 'Lime juice',  'amount' => '20ml'],
                        ['name' => 'Sugar syrup', 'amount' => '10ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Aviation',
                'alcohol_type' => 'gin',
                'attributes'   => [
                    'glass'        => 'coupe',
                    'garnish'      => 'maraschino cherry',
                    'difficulty'   => 'medium',
                    'instructions' => 'Shake all ingredients with ice and double-strain into a chilled coupe.',
                    'ingredients'  => [
                        ['name' => 'Gin',               'amount' => '45ml'],
                        ['name' => 'Maraschino liqueur', 'amount' => '15ml'],
                        ['name' => 'Crème de violette',  'amount' => '7ml'],
                        ['name' => 'Lemon juice',        'amount' => '15ml'],
                    ],
                ],
            ],
            [
                'name'         => 'French 75',
                'alcohol_type' => 'gin',
                'attributes'   => [
                    'glass'        => 'champagne flute',
                    'garnish'      => 'lemon twist',
                    'difficulty'   => 'medium',
                    'instructions' => 'Shake gin, lemon juice and syrup with ice. Strain into a flute and top with champagne.',
                    'ingredients'  => [
                        ['name' => 'Gin',          'amount' => '30ml'],
                        ['name' => 'Lemon juice',  'amount' => '15ml'],
                        ['name' => 'Simple syrup', 'amount' => '10ml'],
                        ['name' => 'Champagne',    'amount' => 'top'],
                    ],
                ],
            ],
            [
                'name'         => 'Clover Club',
                'alcohol_type' => 'gin',
                'attributes'   => [
                    'glass'        => 'coupe',
                    'garnish'      => 'raspberries',
                    'difficulty'   => 'medium',
                    'instructions' => 'Dry shake all ingredients, then shake with ice and double-strain into a chilled coupe.',
                    'ingredients'  => [
                        ['name' => 'Gin',             'amount' => '45ml'],
                        ['name' => 'Lemon juice',     'amount' => '20ml'],
                        ['name' => 'Raspberry syrup', 'amount' => '15ml'],
                        ['name' => 'Egg white',       'amount' => '1'],
                    ],
                ],
            ],
            [
                'name'         => 'Margarita',
                'alcohol_type' => 'tequila',
                'attributes'   => [
                    'glass'        => 'coupe',
                    'garnish'      => 'salt rim & lime wheel',
                    'difficulty'   => 'easy',
                    'instructions' => 'Shake all ingredients with ice and strain into a salt-rimmed glass.',
                    'ingredients'  => [
                        ['name' => 'Tequila blanco', 'amount' => '50ml'],
                        ['name' => 'Triple sec',      'amount' => '20ml'],
                        ['name' => 'Lime juice',      'amount' => '20ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Paloma',
                'alcohol_type' => 'tequila',
                'attributes'   => [
                    'glass'        => 'highball',
                    'garnish'      => 'salt rim & grapefruit slice',
                    'difficulty'   => 'easy',
                    'instructions' => 'Build in a salt-rimmed highball glass over ice. Add tequila and top with grapefruit soda.',
                    'ingredients'  => [
                        ['name' => 'Tequila blanco',  'amount' => '50ml'],
                        ['name' => 'Lime juice',       'amount' => '15ml'],
                        ['name' => 'Grapefruit soda',  'amount' => '120ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Tequila Sunrise',
                'alcohol_type' => 'tequila',
                'attributes'   => [
                    'glass'        => 'highball',
                    'garnish'      => 'orange slice & cherry',
                    'difficulty'   => 'easy',
                    'instructions' => 'Pour tequila and OJ over ice. Slowly pour grenadine down the side of the glass.',
                    'ingredients'  => [
                        ['name' => 'Tequila',      'amount' => '45ml'],
                        ['name' => 'Orange juice', 'amount' => '90ml'],
                        ['name' => 'Grenadine',    'amount' => '15ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Tommy\'s Margarita',
                'alcohol_type' => 'tequila',
                'attributes'   => [
                    'glass'        => 'rocks',
                    'garnish'      => 'lime wedge',
                    'difficulty'   => 'easy',
                    'instructions' => 'Shake all ingredients with ice and strain into a rocks glass over ice.',
                    'ingredients'  => [
                        ['name' => 'Tequila reposado', 'amount' => '60ml'],
                        ['name' => 'Lime juice',        'amount' => '30ml'],
                        ['name' => 'Agave syrup',       'amount' => '20ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Daiquiri',
                'alcohol_type' => 'rum',
                'attributes'   => [
                    'glass'        => 'coupe',
                    'garnish'      => 'lime wheel',
                    'difficulty'   => 'easy',
                    'instructions' => 'Shake hard with ice and double-strain into a chilled coupe.',
                    'ingredients'  => [
                        ['name' => 'White rum',    'amount' => '60ml'],
                        ['name' => 'Lime juice',   'amount' => '30ml'],
                        ['name' => 'Simple syrup', 'amount' => '15ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Mojito',
                'alcohol_type' => 'rum',
                'attributes'   => [
                    'glass'        => 'highball',
                    'garnish'      => 'mint sprig & lime wheel',
                    'difficulty'   => 'easy',
                    'instructions' => 'Muddle mint and lime in glass. Add rum and syrup, fill with crushed ice and top with soda.',
                    'ingredients'  => [
                        ['name' => 'White rum',    'amount' => '50ml'],
                        ['name' => 'Lime juice',   'amount' => '25ml'],
                        ['name' => 'Simple syrup', 'amount' => '20ml'],
                        ['name' => 'Fresh mint',   'amount' => '10 leaves'],
                        ['name' => 'Soda water',   'amount' => 'top'],
                    ],
                ],
            ],
            [
                'name'         => 'Dark & Stormy',
                'alcohol_type' => 'rum',
                'attributes'   => [
                    'glass'        => 'highball',
                    'garnish'      => 'lime wedge',
                    'difficulty'   => 'easy',
                    'instructions' => 'Fill glass with ice, add dark rum and top with ginger beer. Squeeze lime over top.',
                    'ingredients'  => [
                        ['name' => 'Dark rum',    'amount' => '60ml'],
                        ['name' => 'Ginger beer', 'amount' => '120ml'],
                        ['name' => 'Lime juice',  'amount' => '15ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Piña Colada',
                'alcohol_type' => 'rum',
                'attributes'   => [
                    'glass'        => 'hurricane',
                    'garnish'      => 'pineapple slice & cherry',
                    'difficulty'   => 'easy',
                    'instructions' => 'Blend all ingredients with crushed ice until smooth. Pour into a chilled glass.',
                    'ingredients'  => [
                        ['name' => 'White rum',       'amount' => '50ml'],
                        ['name' => 'Coconut cream',   'amount' => '30ml'],
                        ['name' => 'Pineapple juice', 'amount' => '90ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Old Fashioned',
                'alcohol_type' => 'whiskey',
                'attributes'   => [
                    'glass'        => 'rocks',
                    'garnish'      => 'orange peel & cherry',
                    'difficulty'   => 'easy',
                    'instructions' => 'Add syrup and bitters to glass, add ice, pour bourbon and stir gently.',
                    'ingredients'  => [
                        ['name' => 'Bourbon',          'amount' => '60ml'],
                        ['name' => 'Simple syrup',     'amount' => '5ml'],
                        ['name' => 'Angostura bitters', 'amount' => '2 dashes'],
                    ],
                ],
            ],
            [
                'name'         => 'Whiskey Sour',
                'alcohol_type' => 'whiskey',
                'attributes'   => [
                    'glass'        => 'rocks',
                    'garnish'      => 'orange slice & cherry',
                    'difficulty'   => 'easy',
                    'instructions' => 'Dry shake all ingredients, then shake with ice and strain into a rocks glass.',
                    'ingredients'  => [
                        ['name' => 'Bourbon',      'amount' => '60ml'],
                        ['name' => 'Lemon juice',  'amount' => '30ml'],
                        ['name' => 'Simple syrup', 'amount' => '15ml'],
                        ['name' => 'Egg white',    'amount' => '1'],
                    ],
                ],
            ],
            [
                'name'         => 'Manhattan',
                'alcohol_type' => 'whiskey',
                'attributes'   => [
                    'glass'        => 'coupe',
                    'garnish'      => 'maraschino cherry',
                    'difficulty'   => 'easy',
                    'instructions' => 'Stir all ingredients with ice and strain into a chilled coupe.',
                    'ingredients'  => [
                        ['name' => 'Rye whiskey',       'amount' => '50ml'],
                        ['name' => 'Sweet vermouth',    'amount' => '25ml'],
                        ['name' => 'Angostura bitters', 'amount' => '2 dashes'],
                    ],
                ],
            ],
            [
                'name'         => 'Mint Julep',
                'alcohol_type' => 'whiskey',
                'attributes'   => [
                    'glass'        => 'julep cup',
                    'garnish'      => 'fresh mint bouquet',
                    'difficulty'   => 'easy',
                    'instructions' => 'Muddle mint and syrup, fill with crushed ice and pour bourbon. Stir until cup frosts.',
                    'ingredients'  => [
                        ['name' => 'Bourbon',      'amount' => '60ml'],
                        ['name' => 'Simple syrup', 'amount' => '15ml'],
                        ['name' => 'Fresh mint',   'amount' => '8 leaves'],
                    ],
                ],
            ],
            [
                'name'         => 'Paper Plane',
                'alcohol_type' => 'whiskey',
                'attributes'   => [
                    'glass'        => 'coupe',
                    'garnish'      => 'none',
                    'difficulty'   => 'medium',
                    'instructions' => 'Combine equal parts of all ingredients, shake with ice and double-strain into a coupe.',
                    'ingredients'  => [
                        ['name' => 'Bourbon',      'amount' => '22ml'],
                        ['name' => 'Aperol',       'amount' => '22ml'],
                        ['name' => 'Amaro Nonino', 'amount' => '22ml'],
                        ['name' => 'Lemon juice',  'amount' => '22ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Penicillin',
                'alcohol_type' => 'whiskey',
                'attributes'   => [
                    'glass'        => 'rocks',
                    'garnish'      => 'candied ginger',
                    'difficulty'   => 'hard',
                    'instructions' => 'Shake blended scotch, lemon juice, honey-ginger syrup with ice. Strain and float peaty scotch on top.',
                    'ingredients'  => [
                        ['name' => 'Blended scotch',       'amount' => '50ml'],
                        ['name' => 'Islay scotch (float)',  'amount' => '10ml'],
                        ['name' => 'Lemon juice',           'amount' => '25ml'],
                        ['name' => 'Honey-ginger syrup',    'amount' => '20ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Moscow Mule',
                'alcohol_type' => 'vodka',
                'attributes'   => [
                    'glass'        => 'copper mug',
                    'garnish'      => 'lime wedge & mint sprig',
                    'difficulty'   => 'easy',
                    'instructions' => 'Fill a copper mug with ice. Add vodka and lime juice, top with ginger beer.',
                    'ingredients'  => [
                        ['name' => 'Vodka',       'amount' => '50ml'],
                        ['name' => 'Lime juice',  'amount' => '15ml'],
                        ['name' => 'Ginger beer', 'amount' => '120ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Cosmopolitan',
                'alcohol_type' => 'vodka',
                'attributes'   => [
                    'glass'        => 'martini',
                    'garnish'      => 'flamed orange peel',
                    'difficulty'   => 'easy',
                    'instructions' => 'Shake all ingredients with ice and double-strain into a chilled martini glass.',
                    'ingredients'  => [
                        ['name' => 'Citrus vodka',   'amount' => '40ml'],
                        ['name' => 'Triple sec',      'amount' => '15ml'],
                        ['name' => 'Cranberry juice', 'amount' => '30ml'],
                        ['name' => 'Lime juice',      'amount' => '15ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Espresso Martini',
                'alcohol_type' => 'vodka',
                'attributes'   => [
                    'glass'        => 'martini',
                    'garnish'      => '3 coffee beans',
                    'difficulty'   => 'medium',
                    'instructions' => 'Shake all ingredients hard with ice and double-strain into a chilled glass.',
                    'ingredients'  => [
                        ['name' => 'Vodka',          'amount' => '50ml'],
                        ['name' => 'Coffee liqueur', 'amount' => '30ml'],
                        ['name' => 'Espresso',       'amount' => '30ml'],
                        ['name' => 'Simple syrup',   'amount' => '10ml'],
                    ],
                ],
            ],
            [
                'name'         => 'Aperol Spritz',
                'alcohol_type' => 'other',
                'attributes'   => [
                    'glass'        => 'wine glass',
                    'garnish'      => 'orange slice',
                    'difficulty'   => 'easy',
                    'instructions' => 'Build in a wine glass over ice: prosecco first, then Aperol, then soda.',
                    'ingredients'  => [
                        ['name' => 'Aperol',     'amount' => '60ml'],
                        ['name' => 'Prosecco',   'amount' => '90ml'],
                        ['name' => 'Soda water', 'amount' => 'splash'],
                    ],
                ],
            ],
        ];

        foreach ($cocktails as $data) {
            Cocktail::create([...$data, 'user_id' => $user?->id]);
        }
    }
}
