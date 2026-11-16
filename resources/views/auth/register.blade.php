<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi | Sistem Peminjaman Alat</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <style>
        body { background: #F3F5F8; color: #676a6d; font-family: Arial, sans-serif; }
        .register-wrap { max-width: 460px; margin: 7vh auto; padding: 0 15px; }
        .register-panel { background: #fff; border: 1px solid #e7e9ed; border-radius: 5px; box-shadow: 0 4px 18px rgba(30, 40, 55, .08); }
        .register-heading { padding: 24px 28px; border-bottom: 1px solid #f0f2f5; text-align: center; }
        .register-heading img { max-width: 90px; max-height: 70px; margin-bottom: 10px; }
        .register-heading h1 { margin: 5px 0; color: #2B333E; font-size: 22px; }
        .register-body { padding: 28px; }
    </style>
</head>
<body>
    <main class="register-wrap">
        <div class="register-panel">
            <div class="register-heading">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo SMK Marhas">
                <h1>Buat Akun Peminjam</h1>
                <p class="text-muted">Daftar untuk mengajukan peminjaman alat</p>
            </div>
            <div class="register-body">
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input id="nama_lengkap" name="nama_lengkap" type="text" class="form-control" value="{{ old('nama_lengkap') }}" required autofocus autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input id="username" name="username" type="text" class="form-control" value="{{ old('username') }}" required autocomplete="username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" class="form-control" required minlength="8" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required minlength="8" autocomplete="new-password">
                    </div>
                    <p class="text-muted"><small>Akun yang dibuat melalui registrasi mendapat akses sebagai peminjam.</small></p>
                    <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                </form>
                <p class="text-center" style="margin-top:20px;">Sudah punya akun? <a href="{{ route('login') }}">Login</a></p>
            </div>
        </div>
    </main>
</body>
</html>