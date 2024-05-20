@extends('faturhelper::layouts/admin/main')

@section('title', 'Dashboard')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Dashboard</h1>
</div>
<div class="alert alert-success" role="alert">
    <div class="alert-message">
        <h4 class="alert-heading">Selamat Datang!</h4>
        <p class="mb-0">Selamat datang kembali <strong>{{ Auth::user()->name }}</strong> di {{ config('app.name') }}.</p>
    </div>
</div>
<div class="row">
    @include('admin.dashboard.card.card',['data' => $users_count,'judul' => 'Jumlah Pegawai', 'keterangan' => 'Jumlah Semua Pegawai'])
    @include('admin.dashboard.card.card',['data' => $notif_kontrak ,'judul' => 'Kontrak Kerja', 'keterangan' => 'Kontrak kerja < 7'])
    @include('admin.dashboard.card.card',['data' => $magang_count,'judul' => 'Magang Aktif', 'keterangan' => 'jumlah Magang aktif'])
    {{-- @include('admin.dashboard.card.card',['data' => 4,'judul' => 'Pengajuan Cuti']) --}}
    @include('admin.dashboard.card.card',['data' => $lembur_count,'judul' => 'Pengajuan Lembur', 'keterangan' => 'Pengajuan status Pending'])
    {{-- @include('admin.dashboard.card.card',['data' => 6,'judul' => 'Pengajuan Izin']) --}} 
</div>

@endsection