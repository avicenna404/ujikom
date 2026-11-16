<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('role')->default('peminjam')->change();
            });

            DB::table('users')->where('role', 'user')->update(['role' => 'peminjam']);
        }

        if (Schema::hasTable('peminjamans') && Schema::hasColumn('peminjamans', 'status')) {
            Schema::table('peminjamans', function (Blueprint $table): void {
                $table->string('status')->default('pending')->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('peminjamans') && Schema::hasColumn('peminjamans', 'status')) {
            DB::table('peminjamans')->whereIn('status', ['pending', 'ditolak'])->update(['status' => 'dipinjam']);

            Schema::table('peminjamans', function (Blueprint $table): void {
                $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam')->change();
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            DB::table('users')->where('role', 'peminjam')->update(['role' => 'user']);

            Schema::table('users', function (Blueprint $table): void {
                $table->enum('role', ['admin', 'petugas', 'user'])->default('user')->change();
            });
        }
    }
};