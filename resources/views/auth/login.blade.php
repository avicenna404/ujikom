<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Sistem Peminjaman Alat</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <style>
        body { background: #F3F5F8; color: #676a6d; font-family: Arial, sans-serif; }
        .login-wrap { max-width: 420px; margin: 8vh auto; padding: 0 15px; }
        .login-panel { background: #fff; border: 1px solid #e7e9ed; border-radius: 5px; box-shadow: 0 4px 18px rgba(30, 40, 55, .08); }
        .login-heading { padding: 24px 28px; border-bottom: 1px solid #f0f2f5; text-align: center; }
        .login-heading img { max-width: 90px; max-height: 70px; margin-bottom: 10px; }
        .login-heading h1 { margin: 5px 0; color: #2B333E; font-size: 22px; }
        .login-body { padding: 28px; }
    </style>
</head>
<body>
    <main class="login-wrap">
        <div class="login-panel">
            <div class="login-heading">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo SMK Marhas">
                <h1>Sistem Peminjaman Alat</h1>
                <p class="text-muted">Masuk dengan username Anda</p>
            </div>
            <div class="login-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input id="username" name="username" type="text" class="form-control" value="{{ old('username') }}" required autofocus autocomplete="username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" class="form-control" required autocomplete="current-password">
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" name="remember" value="1"> Ingat saya</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>