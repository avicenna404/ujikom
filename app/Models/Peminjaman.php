<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peminjaman extends Model
{
    protected $fillable = ['user_id', 'tanggal_pinjam', 'rencana_kembali', 'status', 'created_at', 'updated_at'];

    protected $table = 'peminjamans';

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'rencana_kembali' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detailPeminjamans(): HasMany
    {
        return $this->hasMany(DetailPeminjaman::class);
    }
}