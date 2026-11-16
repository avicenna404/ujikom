<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengembalian extends Model
{
    protected $fillable = [
        'peminjaman_id',
        'user_id',
        'tanggal_kembali_aktual',
        'kondisi_kembali',
        'denda',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_kembali_aktual' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}