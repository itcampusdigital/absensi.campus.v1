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
    @include('admin.dashboard.card.card',['data' => $users_count,'judul' => 'Jumlah Pegawai', 'bg' => 'success'])
    @include('admin.dashboard.card.card',['data' => $kontrak_count ,'judul' => 'Kontrak Kerja', 'bg' => 'success'])
    @include('admin.dashboard.card.card',['data' => $magang_count,'judul' => 'Magang Aktif', 'bg' => 'success'])
    {{-- @include('admin.dashboard.card.card',['data' => 4,'judul' => 'Pengajuan Cuti']) --}}
    @include('admin.dashboard.card.card',['data' => $lembur_count,'judul' => 'Pengajuan Lembur', 'bg' => 'success'])
    {{-- @include('admin.dashboard.card.card',['data' => 6,'judul' => 'Pengajuan Izin']) --}} 
</div>
<div class="mt-5">
    <table class="table table-responsive">
        <thead class="bg-success">
          <tr class="text-white">
            <th scope="col">Nama</th>
            <th scope="col">Tentang</th>
            <th scope="col">Status</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($data_all as $data)
                <tr>
                    <td>{{ $data->user->name }}</td>
                    <td>cek</td>
                    <td><span class="badge bg-warning">Pending</span></td>
                </tr>
            @endforeach
            
        </tbody>
      </table>
</div>
@endsection