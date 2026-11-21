<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SistemPeminjamanTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create([
            'nama_lengkap' => ucfirst($role) . ' Test',
            'username' => $role . '_test',
            'password' => 'password123',
            'role' => $role,
        ]);
    }

    private function kategori(): Kategori
    {
        return Kategori::create(['nama_kategori' => 'Elektronik']);
    }

    private function alat(int $stok = 10, string $nama = 'Kamera'): Alat
    {
        return Alat::create([
            'nama_alat' => $nama,
            'kategori_id' => $this->kategori()->id,
            'stok' => $stok,
            'kondisi' => 'baik',
        ]);
    }

    public function test_guest_diarahkan_ke_login_dan_role_memiliki_akses_sesuai(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/peminjamans')->assertRedirect('/login');

        $peminjam = $this->user('peminjam');
        $this->actingAs($peminjam)
            ->get('/katalog-alat')->assertOk();
        $this->get('/ajukan-peminjaman')->assertOk();
        $this->get('/users')->assertForbidden();
        $this->get('/kategoris')->assertForbidden();
        $this->get('/peminjamans')->assertForbidden();

        $petugas = $this->user('petugas');
        $this->actingAs($petugas)
            ->get('/')->assertOk();
        $this->get('/peminjamans')->assertOk();
        $this->get('/pengembalians')->assertOk();
        $this->get('/laporan')->assertOk();
        $this->get('/users')->assertForbidden();
        $alat = $this->alat();
        $this->delete('/alats/' . $alat->id)->assertForbidden();

        $this->actingAs($this->user('admin'));
        foreach (['/', '/dashboard', '/users', '/kategoris', '/alats', '/peminjamans', '/pengembalians', '/detail-peminjamans', '/laporan', '/log-aktivitas'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_admin_dapat_mengelola_kategori_dan_alat(): void
    {
        $this->actingAs($this->user('admin'));
        $this->post('/kategoris', ['nama_kategori' => 'Audio'])->assertRedirect('/kategoris');
        $kategori = Kategori::where('nama_kategori', 'Audio')->firstOrFail();
        $this->put('/kategoris/' . $kategori->id, ['nama_kategori' => 'Video'])->assertRedirect('/kategoris');
        $this->assertDatabaseHas('kategoris', ['id' => $kategori->id, 'nama_kategori' => 'Video']);

        $this->post('/alats', ['nama_alat' => 'Tripod', 'kategori_id' => $kategori->id, 'stok' => 8, 'kondisi' => 'baik'])->assertRedirect('/alats');
        $alat = Alat::where('nama_alat', 'Tripod')->firstOrFail();
        $this->put('/alats/' . $alat->id, ['nama_alat' => 'Tripod Baru', 'kategori_id' => $kategori->id, 'stok' => 9, 'kondisi' => 'baik'])->assertRedirect('/alats');
        $this->assertDatabaseHas('alats', ['id' => $alat->id, 'nama_alat' => 'Tripod Baru', 'stok' => 9]);
        $this->delete('/alats/' . $alat->id)->assertRedirect('/alats');
        $this->delete('/kategoris/' . $kategori->id)->assertRedirect('/kategoris');
        $this->assertDatabaseMissing('alats', ['id' => $alat->id]);
        $this->assertDatabaseMissing('kategoris', ['id' => $kategori->id]);
    }

    public function test_pengajuan_memvalidasi_kuota_duplikasi_dan_stok_hanya_berkurang_saat_disetujui(): void
    {
        $peminjam = $this->user('peminjam');
        $alat = $this->alat(10);
        $payload = ['tanggal_kembali_rencana' => now()->addDays(3)->toDateString(), 'alat_id' => [$alat->id], 'jumlah' => [1]];

        $this->actingAs($peminjam)->post('/ajukan-peminjaman', array_replace($payload, ['jumlah' => [4]]))->assertSessionHasErrors('jumlah.0');
        $this->post('/ajukan-peminjaman', array_replace($payload, ['jumlah' => [6]]))->assertSessionHasErrors('jumlah.0');
        $this->post('/ajukan-peminjaman', [
            'tanggal_kembali_rencana' => now()->addDays(3)->toDateString(),
            'alat_id' => [$alat->id, $alat->id], 'jumlah' => [1, 1],
        ])->assertSessionHasErrors('alat_id.1');
        $this->assertSame(10, $alat->fresh()->stok);

        $this->post('/ajukan-peminjaman', $payload)->assertRedirect('/peminjaman-saya');
        $peminjaman = Peminjaman::where('user_id', $peminjam->id)->firstOrFail();
        $this->assertSame('pending', $peminjaman->status);
        $this->assertDatabaseHas('detail_peminjamans', ['peminjaman_id' => $peminjaman->id, 'alat_id' => $alat->id, 'jumlah' => 1]);
        $this->assertSame(10, $alat->fresh()->stok);

        $petugas = $this->user('petugas');
        $this->actingAs($petugas)->post('/peminjamans/' . $peminjaman->id . '/keputusan', ['keputusan' => 'dipinjam'])->assertRedirect();
        $this->assertSame('dipinjam', $peminjaman->fresh()->status);
        $this->assertSame(9, $alat->fresh()->stok);

        $this->actingAs($peminjam)->post('/pengembalian-saya', [
            'peminjaman_id' => $peminjaman->id,
            'tanggal_kembali_aktual' => now()->toDateString(),
            'kondisi_kembali' => 'baik',
        ])->assertRedirect('/pengembalian-saya');
        $pengembalian = Pengembalian::where('peminjaman_id', $peminjaman->id)->firstOrFail();
        $this->assertSame('pending', $pengembalian->status);
        $this->actingAs($petugas)->post('/pengembalians/' . $pengembalian->id . '/verifikasi', ['keputusan' => 'diterima'])->assertRedirect();
        $this->assertSame('diterima', $pengembalian->fresh()->status);
        $this->assertSame('dikembalikan', $peminjaman->fresh()->status);
        $this->assertSame(10, $alat->fresh()->stok);
    }

    public function test_filter_tanggal_berfungsi_pada_transaksi_log_dan_laporan(): void
    {
        $admin = $this->user('admin');
        $peminjam = $this->user('peminjam');
        $alat = $this->alat();
        $dalam = Peminjaman::create(['user_id' => $peminjam->id, 'tanggal_pinjam' => '2026-11-10', 'tanggal_kembali_rencana' => '2026-11-20', 'status' => 'dipinjam']);
        $luar = Peminjaman::create(['user_id' => $peminjam->id, 'tanggal_pinjam' => '2026-10-10', 'tanggal_kembali_rencana' => '2026-10-20', 'status' => 'dipinjam']);
        Pengembalian::create(['peminjaman_id' => $dalam->id, 'tanggal_kembali_aktual' => '2026-11-12', 'kondisi_kembali' => 'baik', 'denda' => 0, 'status' => 'diterima']);
        Pengembalian::create(['peminjaman_id' => $luar->id, 'tanggal_kembali_aktual' => '2026-10-12', 'kondisi_kembali' => 'baik', 'denda' => 0, 'status' => 'diterima']);
        LogAktivitas::create(['user_id' => $admin->id, 'aktivitas' => 'Dalam rentang', 'modul' => 'Test', 'created_at' => '2026-11-11 10:00:00']);
        LogAktivitas::create(['user_id' => $admin->id, 'aktivitas' => 'Di luar rentang', 'modul' => 'Test', 'created_at' => '2026-10-11 10:00:00']);

        $this->actingAs($admin);
        $filter = ['tanggal_awal' => '2026-11-01', 'tanggal_akhir' => '2026-11-30'];
        $this->get('/peminjamans?' . http_build_query($filter))->assertOk()->assertSee('10 Nov 2026')->assertDontSee('10 Oct 2026');
        $this->get('/pengembalians?' . http_build_query($filter))->assertOk()->assertSee('12 Nov 2026')->assertDontSee('12 Oct 2026');
        $this->get('/log-aktivitas?' . http_build_query($filter))->assertOk()->assertSee('Dalam rentang')->assertDontSee('Di luar rentang');
        $this->get('/laporan?' . http_build_query($filter))->assertOk()->assertSee('10 Nov 2026')->assertDontSee('10 Oct 2026');
    }
}
