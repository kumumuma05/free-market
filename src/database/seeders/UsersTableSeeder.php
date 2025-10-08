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
            [
                'name' => 'オカモト',
                'password' => Hash::make('passpass'),
                'profile_image' => 'profile_images/red.png',
                'postal' => '061-2222',
                'address' => '北海道',
                'building' => '山'
            ]
        );
        User::updateOrCreate(
            ['email' => '456@789'],
            [
                'name' => 'ヤマダ',
                'password' => Hash::make('passpass'),
                'profile_image' => 'profile_images/blue.png',
                'postal' => '061-5555',
                'address' => 'あおもり',
                'building' => '川'
            ]
        );
        User::updateOrCreate(
            ['email' => '789@123'],
            [
                'name' => 'サトウ',
                'password' => Hash::make('passpass'),
                'profile_image' => 'profile_images/yellow.png',
                'postal' => '555-2222',
                'address' => '山形',
                'building' => '海'
            ]
        );
    }
}
