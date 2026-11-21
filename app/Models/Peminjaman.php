<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Peminjaman extends Model
{
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeMilikUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    protected $fillable = [
        'user_id',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'status',
        'keperluan',
    ];

    protected $table = 'peminjamans';

    protected $casts = [
        'tanggal_pinjam' => 'date',
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

    public function pengembalians(): HasMany
    {
        return $this->hasMany(Pengembalian::class);
    }

    public function hitungDendaTerlambat(string $tanggalAktual): int
    {
        $rencana = Carbon::parse($this->tanggal_kembali_rencana)->startOfDay();
        $aktual = Carbon::parse($tanggalAktual)->startOfDay();
        $hariTerlambat = max(0, (int) $rencana->diffInDays($aktual, false));

        return $hariTerlambat * 5000;
    }
}
