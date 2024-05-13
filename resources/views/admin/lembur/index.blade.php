@extends('faturhelper::layouts/admin/main')

@section('title', 'Kelola Lembur')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Kelola Lembur Pegawai</h1>
    <a href="{{ route('admin.lembur.create') }}" class="btn btn-sm btn-primary"><i class="bi-plus me-1"></i> Tambah Lembur</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-sm-flex justify-content-center align-items-center">
                <form id="form-filter" class="d-lg-flex" method="get" action="">
                    <div class="mb-lg-0 mb-2">
                        <select name="year" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Periode Tahun">
                            @for($i=date("Y"); $i>=2020; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="status" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Status">
                            <option value="0" >--Pilih Status--</option>
                            <option value="1" >Diterima</option>
                            <option value="2" >Ditolak</option>
                            <option value="3" >Pending</option>
                        </select>
                    </div>
                    @if(Auth::user()->role_id == role('super-admin'))
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="group" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Perusahaan">
                            <option value="0">--Pilih Perusahaan--</option>
                            @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ Request::query('group') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="office" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Kantor">
                            <option value="0" disabled selected>--Pilih Kantor--</option>
                            @if(Auth::user()->role_id == role('super-admin'))
                                @if(Request::query('group') != 0)
                                    @foreach(\App\Models\Group::find(Request::query('group'))->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                    <option value="{{ $office->id }}" {{ Request::query('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                    @endforeach
                                @endif
                            @elseif(Auth::user()->role_id == role('admin'))
                                @foreach(\App\Models\Group::find(Auth::user()->group_id)->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                <option value="{{ $office->id }}" {{ Request::query('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @elseif(Auth::user()->role_id == role('manager'))
                                @foreach(Auth::user()->managed_offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                <option value="{{ $office->id }}" {{ Request::query('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="ms-lg-2 ms-0">
                        <button type="submit" class="btn btn-sm btn-info" {{ Request::query('office') != null ? '' : 'disabled' }}><i class="bi-filter-square me-1"></i> Filter</button>
                    </div>
                </form>
            </div>
            <hr class="my-0">
            {{-- @if(Request::query('office') != null) --}}
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
                                {{-- <th width="20"><input type="checkbox" class="form-check-input checkbox-all"></th> --}}
                                <th width="200">Nama</th>
                                <th width="80">Tanggal</th>
                                <th width="80">Waktu</th>
                                <th >Keterangan</th>
                                <th width="60">Status</th>
                                <th width="100">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lemburs as $lembur)
                                <tr>
                                    {{-- <td><input type="checkbox" class="form-check-input checkbox-one"></td> --}}
                                    <td>{{ $lembur->user->name }}</td>
                                    <td>{{ $lembur->date }}</td>
                                    <td>{{ $lembur->start_time }} - {{ $lembur->end_time }}</td>
                                    <td>{{ $lembur->keterangan }}</td>
                                    <td class="text-center ">
                                        @if($lembur->status == 1)
                                            <span class="badge bg-success">Diterima</span>
                                        @elseif($lembur->status == 2)
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.lembur.edit', $lembur->id) }}" title="Edit data" class="btn btn-sm btn-warning"><i class="bi-pencil"></i></a>
                                        @if(Auth::user()->role_id == role('super-admin') || Auth::user()->role_id == role('admin'))
                                            <a href="#" class="btn btn-sm btn-success btn-approval" data-id="{{ $lembur->id }}" title="Approval data"><i class="bi bi-check-lg"></i></a>
                                            <a href="#" class="btn btn-sm btn-danger btn-delete" data-id="{{ $lembur->id }}" title="Delete data"><i class="bi bi-trash"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- @endif --}}
        </div>
    </div>
</div>
<form id="formApproval" method="post" action="{{ route('admin.lembur.approval') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" id="id" >
    <input type="hidden" name="status" id="status" value="1">
</form>

<form class="form-delete d-none" method="post" action="{{ route('admin.lembur.delete') }}">
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

    $(document).ready(function() {
        $(document).on("click", ".btn-approval", function() {
            var id = $(this).data("id");
            $("#id").val(id);
            $("#status").val(1);
            $("#formApproval").submit();
        });
    });

    // Change Group
    $(document).on("change", "select[name=group]", function() {
        var group = $(this).val();
        $.ajax({
            type: "get",
            url: "{{ route('api.office.index') }}",
            data: {group: group},
            success: function(result){
                var html = '<option value="0" disabled selected>--Pilih Kantor--</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("select[name=office]").html(html).removeAttr("disabled");
            }
        });
    });

    // Change the Office
    $(document).on("change", "select[name=office]", function() {
        var office = $("select[name=office]").val();
        var status = $("select[name=status]").val();
        if(office !== null)
            $("#form-filter").find("button[type=submit]").removeAttr("disabled");
        else
            $("#form-filter").find("button[type=submit]").attr("disabled","disabled");
    });
</script>

@endsection