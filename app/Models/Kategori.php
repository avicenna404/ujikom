<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $fillable = ['nama_kategori', 'created_at', 'updated_at'];

    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];
}
