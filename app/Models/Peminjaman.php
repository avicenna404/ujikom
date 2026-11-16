<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    protected $fillable = ['user_id', 'tanggal_pinjam', 'rencana_kembali', 'status', 'created_at', 'updated_at'];

    protected $table = 'peminjamans';

    protected $casts = ['tanggal_pinjam' => 'datetime', 'rencana_kembali' => 'date', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
