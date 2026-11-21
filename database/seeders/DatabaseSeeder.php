<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'nama_lengkap' => 'Administrator',
                'username' => 'admin',
                'password' => 'admin12345',
                'role' => 'admin',
            ],
            [
                'nama_lengkap' => 'Petugas Peminjaman',
                'username' => 'petugas',
                'password' => 'petugas12345',
                'role' => 'petugas',
            ],
            [
                'nama_lengkap' => 'Peminjam',
                'username' => 'peminjam',
                'password' => 'peminjam12345',
                'role' => 'peminjam',
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['username' => $account['username']],
                [
                    'nama_lengkap' => $account['nama_lengkap'],
                    'password' => Hash::make($account['password']),
                    'role' => $account['role'],
                ],
            );
        }
    }
}
