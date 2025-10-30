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
            ['email' => 'user1@test.com'],
            [
                'name' => '北海　太郎',
                'password' => Hash::make('password'),
                'profile_image' => 'profile_image/01.png',
                'postal' => '011-2222',
                'address' => '北海道札幌市大通１丁目１－１',
                'building' => ''
            ]
        );
        User::updateOrCreate(
            ['email' => 'user2@test.com'],
            [
                'name' => '札幌　花子',
                'password' => Hash::make('password'),
                'profile_image' => 'profile_image/02.png',
                'postal' => '022-3333',
                'address' => '北海道函館市五稜郭町１丁目２ー３',
                'building' => '五稜郭MS101'
            ]
        );
        User::updateOrCreate(
            ['email' => 'user3@test.com'],
            [
                'name' => 'hokki taro',
                'password' => Hash::make('password'),
                'profile_image' => 'profile_image/03.png',
                'postal' => '555-5555',
                'address' => '北海道虻田郡ニセコ町',
                'building' => 'アンヌプリMS202'
            ]
        );
    }
}
