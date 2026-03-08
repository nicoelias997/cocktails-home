<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormSchema;

class FormSchemaSeeder extends Seeder
{
    public function run(): void
    {
        FormSchema::updateOrCreate(
            ['name' => 'cocktails'],
            [
                'active'   => true,
                'sections' => [
                    [
                        'title'  => 'Basic info',
                        'fields' => [
                            [
                                'key'      => 'name',
                                'type'     => 'text',
                                'label'    => 'Name',
                                'required' => true,
                            ],
                            [
                                'key'      => 'alcohol_type',
                                'type'     => 'select',
                                'label'    => 'Alcohol type',
                                'required' => true,
                                'options'  => [
                                    ['value' => 'gin',     'label' => 'Gin'],
                                    ['value' => 'vodka',   'label' => 'Vodka'],
                                    ['value' => 'rum',     'label' => 'Rum'],
                                    ['value' => 'whiskey', 'label' => 'Whiskey'],
                                    ['value' => 'tequila', 'label' => 'Tequila'],
                                    ['value' => 'other',   'label' => 'Other'],
                                ],
                            ],
                            [
                                'key'       => 'photo',
                                'type'      => 'file',
                                'label'     => 'Photo',
                                'accept'    => 'image/*',
                                'removable' => true,
                            ],
                        ],
                    ],
                    [
                        'title'  => 'Attributes',
                        'fields' => [
                            [
                                'key'         => 'attributes.glass',
                                'type'        => 'text',
                                'label'       => 'Glass',
                                'placeholder' => 'e.g. rocks, coupe',
                            ],
                            [
                                'key'         => 'attributes.garnish',
                                'type'        => 'text',
                                'label'       => 'Garnish',
                                'placeholder' => 'e.g. orange peel',
                            ],
                            [
                                'key'     => 'attributes.difficulty',
                                'type'    => 'select',
                                'label'   => 'Difficulty',
                                'options' => [
                                    ['value' => 'easy',   'label' => 'Easy'],
                                    ['value' => 'medium', 'label' => 'Medium'],
                                    ['value' => 'hard',   'label' => 'Hard'],
                                ],
                            ],
                            [
                                'key'   => 'attributes.instructions',
                                'type'  => 'textarea',
                                'label' => 'Instructions',
                                'rows'  => 3,
                            ],
                        ],
                    ],
                    [
                        'title'  => 'Ingredients',
                        'fields' => [
                            [
                                'key'       => 'attributes.ingredients',
                                'type'      => 'repeater',
                                'label'     => 'Ingredients',
                                'subfields' => [
                                    ['key' => 'name',   'type' => 'text', 'label' => 'Ingredient', 'flex' => 1],
                                    ['key' => 'amount', 'type' => 'text', 'label' => 'Amount'],
                                ],
                            ],
                        ],
                    ]
                ],
            ]
        );
    }
}
