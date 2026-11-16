<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('detail_peminjamans')
            && Schema::hasColumn('detail_peminjamans', 'kodisi_awal')
            && ! Schema::hasColumn('detail_peminjamans', 'kondisi_awal')
        ) {
            Schema::table('detail_peminjamans', function (Blueprint $table): void {
                $table->renameColumn('kodisi_awal', 'kondisi_awal');
            });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('detail_peminjamans')
            && Schema::hasColumn('detail_peminjamans', 'kondisi_awal')
            && ! Schema::hasColumn('detail_peminjamans', 'kodisi_awal')
        ) {
            Schema::table('detail_peminjamans', function (Blueprint $table): void {
                $table->renameColumn('kondisi_awal', 'kodisi_awal');
            });
        }
    }
};