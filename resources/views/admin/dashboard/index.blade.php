@extends('template/main')

@section('title', 'Dashboard')

@section('content')

<main class="app-content">
	<div class="app-title">
		<div>
			<h1><i class="fa fa-dashboard"></i> Dashboard</h1>
			<!-- <p>Menu untuk menampilkan data dan statistik penting</p> -->
		</div>
		<ul class="app-breadcrumb breadcrumb">
			<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
		</ul>
	</div>
	<div class="row">
		<div class="col-12 text-center">
			<div class="alert alert-success">
				Selamat datang <strong>{{ Auth::user()->name }}</strong> di aplikasi <strong>{{ setting('name') }}</strong>.
			</div>
		</div>
	</div>
</main>

@endsection