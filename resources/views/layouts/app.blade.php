<!doctype html>
<html lang="id">

<head>
	<title>@yield('title', 'Dashboard | Sistem Peminjaman Alat')</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- CSS LOKAL -->
	<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

	<!-- GOOGLE FONTS -->
	<link href="https://googleapis.com" rel="stylesheet">

	<!-- FAVICON -->
	<link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}">

	<style>
		body {
			background-color: #F3F5F8;
			font-family: 'Source Sans Pro', sans-serif;
			font-size: 15px;
			color: #676a6d;
			margin: 0;
			padding: 0;
		}

		.navbar-default {
			background-color: #ffffff;
			border: none;
			border-bottom: 1px solid #e7e9ed;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
			min-height: 60px;
			margin-bottom: 0;
		}
		.navbar-default .brand {
			float: left;
			width: 250px;
			height: 60px;
			padding: 12px 20px;
			background-color: #ffffff;
			border-right: 1px solid #e7e9ed;
			display: flex;
			align-items: center;
		}
		.navbar-default .brand img {
			max-height: 36px;
			width: auto;
		}
		.navbar-btn {
			float: left;
			padding: 12px 15px;
			margin: 0;
		}
		.btn-toggle-fullwidth {
			background: transparent;
			border: 1px solid #dce1e7;
			border-radius: 4px;
			padding: 6px 10px;
			color: #555;
		}
		.btn-toggle-fullwidth:hover {
			background-color: #f4f6f9;
		}

		#wrapper .sidebar {
			width: 250px;
			height: 100%;
			float: left;
			background-color: #2B333E;
			position: fixed;
			top: 60px;
			left: 0;
			bottom: 0;
			z-index: 1000;
			transition: all 0.3s ease;
			overflow-y: auto;
		}
		.sidebar .nav {
			padding-top: 15px;
		}
		.sidebar .nav > li > a {
			padding: 14px 25px;
			color: #AEB7C2;
			border-left: 4px solid transparent;
			font-weight: 400;
			transition: all 0.2s;
		}
		.sidebar .nav > li > a:hover,
		.sidebar .nav > li > a:focus {
			background-color: #252c35;
			color: #ffffff;
		}
		.sidebar .nav > li > a.active {
			background-color: #252c35;
			color: #ffffff;
			border-left-color: #00AAFF;
		}
		.sidebar .nav > li > a i {
			margin-right: 12px;
			font-size: 16px;
			width: 20px;
			text-align: center;
		}

		#wrapper .main {
			width: calc(100% - 250px);
			margin-left: 250px;
			padding-top: 60px;
			min-height: calc(100vh - 50px);
			transition: all 0.3s ease;
		}
		.main-content {
			padding: 28px 25px;
		}

		.panel {
			background-color: #ffffff;
			border-radius: 4px;
			border: 1px solid #e7e9ed;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
			margin-bottom: 25px;
		}
		.panel-heading {
			padding: 18px 22px;
			border-bottom: 1px solid #f0f2f5;
		}
		.panel-title {
			font-size: 18px;
			font-weight: 600;
			color: #2B333E;
			margin: 0;
		}
		.panel-body {
			padding: 22px;
		}

		body.layout-fullwidth #wrapper .sidebar {
			left: -250px;
		}
		body.layout-fullwidth #wrapper .main {
			width: 100%;
			margin-left: 0;
		}
		/* Footer */
		footer {
			margin-left: 250px;
			padding: 15px 25px;
			background-color: #ffffff;
			border-top: 1px solid #e7e9ed;
			color: #8d9095;
			font-size: 13px;
			transition: all 0.3s ease;
		}
		body.layout-fullwidth footer {
			margin-left: 0;
		}
		@media (max-width: 768px) {
			#wrapper .sidebar {
				left: -250px;
			}
			body.layout-fullwidth #wrapper .sidebar {
				left: 0;
			}
			#wrapper .main, footer {
				width: 100%;
				margin-left: 0;
			}
			.navbar-default .brand {
				width: auto;
				border-right: none;
			}
		}
	</style>

	@stack('styles')
</head>

<body>
	<div id="wrapper">
		<!-- NAVBAR -->
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="brand">
				<a href="{{ auth()->check() ? route(auth()->user()->role === 'peminjam' ? 'katalog-alat.index' : 'dashboard') : route('login') }}">
					<img src="{{ asset('assets/img/logo.png') }}" alt="SMK Marhas Logo" class="img-responsive logo">
				</a>
			</div>
			<div class="container-fluid">
				<div class="navbar-btn">
					<button type="button" class="btn-toggle-fullwidth" title="Buka/Tutup Menu">
						<i class="glyphicon glyphicon-menu-hamburger"></i>
					</button>
				</div>
				<div id="navbar-menu">
					<ul class="nav navbar-nav navbar-right" style="margin-right: 10px;">
						<li>
							<a href="#" style="padding-top: 20px; padding-bottom: 20px;">
								<i class="glyphicon glyphicon-calendar"></i> <span>{{ date('d M Y') }}</span>
							</a>
						</li>
						@auth
							<li>
								<a href="#" style="padding-top: 20px; padding-bottom: 20px;">
									<i class="glyphicon glyphicon-user"></i>
									{{ auth()->user()->nama_lengkap }} <span class="text-muted">({{ ucfirst(auth()->user()->role) }})</span>
								</a>
							</li>
							<li>
								<form method="POST" action="{{ route('logout') }}" style="padding: 13px 10px;">
									@csrf
									<button type="submit" class="btn btn-default btn-sm">
										<i class="glyphicon glyphicon-log-out"></i> Logout
									</button>
								</form>
							</li>
						@endauth
					</ul>
				</div>
			</div>
		</nav>
		<!-- END NAVBAR -->

		<!-- LEFT SIDEBAR -->
		<div id="sidebar-nav" class="sidebar">
			<nav>
				@include('layouts.sidemenu')
			</nav>
		</div>
		<!-- END LEFT SIDEBAR -->

		<!-- MAIN -->
		<div class="main">
			<div class="main-content">
				<div class="container-fluid">
					<!-- Notifikasi Flash Message -->
					@if (session('success'))
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<i class="glyphicon glyphicon-ok-sign"></i> {{ session('success') }}
						</div>
					@endif

					@if (session('error'))
						<div class="alert alert-danger alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<i class="glyphicon glyphicon-exclamation-sign"></i> {{ session('error') }}
						</div>
					@endif

					@yield('content')
				</div>
			</div>
		</div>
		<!-- END MAIN -->

		<div class="clearfix"></div>
		<footer>
			<div class="container-fluid">
				<p class="copyright" style="margin: 0;">&copy; {{ date('Y') }} Sistem Peminjaman Alat - SMK Marhas Margahayu.</p>
			</div>
		</footer>
	</div>

	<!-- JAVASCRIPT -->
	<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
	<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

	<script>
		$(document).ready(function() {
			$('.btn-toggle-fullwidth').on('click', function() {
				$('body').toggleClass('layout-fullwidth');
			});
		});
	</script>

	@stack('scripts')
</body>

</html>
