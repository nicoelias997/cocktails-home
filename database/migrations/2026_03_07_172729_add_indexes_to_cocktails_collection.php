<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'mongodb';

    public function up(): void
    {
        DB::connection('mongodb')
            ->getCollection('cocktails')
            ->createIndex(
                ['user_id' => 1, 'created_at' => -1],
                ['name' => 'user_created_at'],
            );

        DB::connection('mongodb')
            ->getCollection('cocktails')
            ->createIndex(
                ['user_id' => 1, 'alcohol_type' => 1, 'created_at' => -1],
                ['name' => 'user_alcohol_type_created_at'],
            );
    }

    public function down(): void
    {
        $collection = DB::connection('mongodb')->getCollection('cocktails');
        $collection->dropIndex('user_created_at');
        $collection->dropIndex('user_alcohol_type_created_at');
    }
};
