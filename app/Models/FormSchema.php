<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class FormSchema extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'form_schemas';

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'sections',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'active'   => 'boolean',
        ];
    }
}
