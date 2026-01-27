<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrNew(['email' => 'admin@example.com']);

        $user->name = 'Dan Waldie';
        $user->password = Hash::make('password'); // local only
        $user->is_admin = true;

        $user->save();
    }
}
