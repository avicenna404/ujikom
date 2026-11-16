<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alat extends Model
{
    protected $fillable = ['nama_alat', 'kategori_id', 'stok', 'kondisi', 'created_at', 'updated_at'];

    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Kategori::class);
    }
}
