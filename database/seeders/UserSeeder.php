<?php

namespace Database\Seeders;

use App\Models\User;
use App\Actions\Uppercase;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entry = [
            'name' => 'Andy Wijaya',
            'email' => 'andykevonata@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('andy123'),
            'remember_token' => Str::random(10),
        ];
        $entry = Uppercase::execute($entry);
        User::create($entry);
        $entry = [
            'name' => 'Lidia Tri Wulansari',
            'email' => 'lidyatw@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('lid123'),
            'remember_token' => Str::random(10),
        ];
        $entry = Uppercase::execute($entry);
        User::create($entry);
    }
}
