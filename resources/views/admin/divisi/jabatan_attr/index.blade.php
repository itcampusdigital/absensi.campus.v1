@extends('faturhelper::layouts/admin/main')

@section('title', 'Kelola Jabatan')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Kelola Divisi {{ $divisi->name }}</h1>
    {{-- <a href="{{ route('admin.jabatan.divisi.create',['id_divisi' => $divisi_id]) }}" class="btn btn-sm btn-primary"><i class="bi-plus me-1"></i> Tambah Karyawan</a> --}}
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            @if(Auth::user()->role_id == role('super-admin'))
            <div class="card-header d-sm-flex justify-content-end align-items-center">
                <div></div>
                <div class="ms-sm-2 ms-0">
                    <select id="group" name="group" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Perusahaan">
                        <option value="0">Semua Perusahaan</option>
                        @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ Request::query('group') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <hr class="my-0">
            @endif
            <div class="card-body">
                @if(Session::get('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-message">{{ Session::get('message') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered" id="datatable">
                        <thead class="bg-light">
                            <tr>
                                <th width="40" align="center">No</th>
                                <th align="center">Karyawan</th>
                                <th>Posisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user_divisi as $karyawan)
                                <tr>
                                    <td align="center">{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('admin.user.detail', ['id' => $karyawan->user->id]) }}">
                                        {{ $karyawan->user->name }}
                                        </a>
                                        <br>
                                        <small class="text-dark">{{ $karyawan->user->email }}</small>
                                        <br>
                                        <small class="text-muted">{{ $karyawan->user->phone_number }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.position.detail', ['id' => $karyawan->user->position->id]) }}">{{ $karyawan->user->position->name }}</a>
                                        <br>
                                        <small class="text-muted">{{ $karyawan->user->position->group->name }}</small>
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<form class="form-delete d-none" method="post" action="#">
    @csrf
    <input type="hidden" name="id">
</form>


@endsection

@section('js')

<script type="text/javascript">
    // DataTable
    Spandiv.DataTable("#datatable");

    // Button Delete
    Spandiv.ButtonDelete(".btn-delete", ".form-delete");

    // Change the Group
    $(document).on("change", ".card-header select[name=group]", function() {
        var group = $(this).val();
        if(group === "0") window.location.href = Spandiv.URL("{{ route('admin.position.index') }}");
        else window.location.href = Spandiv.URL("{{ route('admin.position.index') }}", {group: group});
    });
</script>

@endsection