<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::where('email', 'test@test.com')->first() ?? new User();

        $user->name              = 'Demo User';
        $user->email             = 'test@test.com';
        $user->password          = 'password';
        $user->email_verified_at = now();
        $user->is_admin          = true;
        $user->save();

        $this->call([
            FormSchemaSeeder::class,
            CocktailSeeder::class,
        ]);
    }
}
