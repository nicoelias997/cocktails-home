<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Cocktail extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'cocktails';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'alcohol_type',
        'photo_path',
        'attributes',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attributes' => 'array',
        ];
    }
}
