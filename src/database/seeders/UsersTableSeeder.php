<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::updateOrCreate(
            ['email' => '123@456'],
            ['name' => 'オカモト', 'password' => Hash::make('passpass')]
        );
        User::updateOrCreate(
            ['email' => '456@789'],
            ['name' => 'ヤマダ', 'password' => Hash::make('passpass')]
        );
        User::updateOrCreate(
            ['email' => '789@123'],
            ['name' => 'サトウ', 'password' => Hash::make('passpass')]
        );
    }
}
