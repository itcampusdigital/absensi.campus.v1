@extends('faturhelper::layouts/admin/main')

@section('title', 'Rekapitulasi Absensi')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Rekapitulasi Absensi</h1>
    <a href="{{ route('admin.summary.attendance.monitor') }}" class="btn btn-sm btn-primary"><i class="bi-eye me-1"></i> Monitoring</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-sm-flex justify-content-center align-items-center">
                <form id="form-filter" class="d-lg-flex" method="get" action="">
                    @if(Auth::user()->role_id == role('super-admin'))
                    <div class="mb-lg-0 mb-2">
                        <select name="group" id="group" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Perusahaan">
                            <option value="0">Semua Perusahaan</option>
                            @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ Request::query('group') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="office" id="office" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Kantor">
                            <option value="0">Semua Kantor</option>
                            @if(Auth::user()->role_id == role('super-admin'))
                                @if(Request::query('group') != 0)
                                    @foreach(\App\Models\Group::find($_GET['group'])->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
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
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="position" id="position" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Jabatan">
                            <option value="0">--Pilih Jabatan--</option>
                            @if(Auth::user()->role_id == role('super-admin') )
                                @if(Request::query('group') != 0)
                                    @foreach(\App\Models\Group::find(Request::query('group'))->positions()->orderBy('name','asc')->get() as $position)
                                    <option value="{{ $position->id }}" {{ Request::query('position') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                    @endforeach
                                @endif
                            @elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager'))
                                @foreach(\App\Models\Group::find(Auth::user()->group_id)->positions()->orderBy('name','asc')->get() as $position)
                                <option value="{{ $position->id }}" {{ Request::query('position') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                @endforeach
                            @endif
                        </select>
                </div>
					<div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="status" id="status" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Status">
                            <option value="1" {{ $status == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ $status == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <input type="text" id="t1" name="t1" class="form-control form-control-sm input-tanggal" value="{{ date('d/m/Y', strtotime($t1)) }}" autocomplete="off" data-bs-toggle="tooltip" title="Dari Tanggal">
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <input type="text" id="t2" name="t2" class="form-control form-control-sm input-tanggal" value="{{ date('d/m/Y', strtotime($t2)) }}" autocomplete="off" data-bs-toggle="tooltip" title="Sampai Tanggal">
                    </div>
                    <div class="ms-lg-2 ms-0">
                        <button type="submit" class="btn btn-sm btn-info"><i class="bi-filter-square me-1"></i> Filter</button>
                        <a type="button" id="exportExcel" class="btn btn-sm btn-success"><i class="bi-filter-square me-1"></i> Export Excel</a>
                    </div>
                </form>
            </div>
            <hr class="my-0">
            <div class="card-body">
                @if(Session::get('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-message">{{ Session::get('message') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="datatable">
                        <thead class="bg-light">
                            <tr>
                                <th>Karyawan</th>
                                <th width="150" class="{{ Request::query('office') != '' && Request::query('office') != 0 ? 'd-none' : '' }}">Kantor</th>
                                <th width="150">Posisi Jabatan</th>
                                <th width="60">Hadir</th>
                                <th width="60">Terlambat</th>
                                <th width="60">Alpa</th>
                                <th width="60">Sakit</th>
                                <th width="60">Izin</th>
                                <th width="60">Cuti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $key=>$user)
                                @foreach($user->workhours as $workhour)
                                    <tr>
                                        <td>
                                            <span class="d-none">{{ $user->end_date != null ? 1 : 0 }}-{{ $user->name }}</span>
                                            <a href="{{ route('admin.user.detail', ['id' => $user->id]) }}">{{ $user->name }}</a>
                                            @if($user->position)
                                            <br>
                                            <small class="text-muted">{{ $user->position->name }}</small>
                                            @endif
                                            @if($user->end_date != null)
                                            <br>
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="{{ Request::query('office') != '' && Request::query('office') != 0 ? 'd-none' : '' }}">
                                            @if($user->office)
                                                <a href="{{ route('admin.office.detail', ['id' => $user->office->id]) }}">{{ $user->office->name }}</a>
                                            @endif
                                        </td>
                                        <td>{{ $workhour->name }}</td>
                                        <td align="right">
                                            <a href="{{ route('admin.summary.attendance.detail', ['id' => $user->id, 'workhour' => $workhour->id, 'category' => 1, 't1' => date('d/m/Y', strtotime($t1)), 't2' => date('d/m/Y', strtotime($t2))]) }}">{{ number_format($workhour->present,0,',',',') }}</a>
                                        </td>
                                        <td align="right">
                                            <a href="{{ route('admin.summary.attendance.detail', ['id' => $user->id, 'workhour' => $workhour->id, 'category' => 2, 't1' => date('d/m/Y', strtotime($t1)), 't2' => date('d/m/Y', strtotime($t2))]) }}">{{ number_format($workhour->late,0,',',',') }}</a>
                                        </td>
                                        <td align="right">
                                            <a href="{{ route('admin.summary.attendance.detail', ['id' => $user->id, 'category' => 6, 't1' => date('d/m/Y', strtotime($t1)), 't2' => date('d/m/Y', strtotime($t2))]) }}">{{ number_format($user->absent3,0,',',',') }}</a>
                                        </td>
                                        <td align="right">
                                            <a href="{{ route('admin.summary.attendance.detail', ['id' => $user->id, 'category' => 3, 't1' => date('d/m/Y', strtotime($t1)), 't2' => date('d/m/Y', strtotime($t2))]) }}">{{ number_format($user->absent1,0,',',',') }}</a>
                                        </td>
                                        <td align="right">
                                            <a href="{{ route('admin.summary.attendance.detail', ['id' => $user->id, 'category' => 4, 't1' => date('d/m/Y', strtotime($t1)), 't2' => date('d/m/Y', strtotime($t2))]) }}">{{ number_format($user->absent2,0,',',',') }}</a>
                                        </td>
                                        <td align="right">
                                            <a href="{{ route('admin.summary.attendance.detail', ['id' => $user->id, 'category' => 5, 't1' => date('d/m/Y', strtotime($t1)), 't2' => date('d/m/Y', strtotime($t2))]) }}">{{ number_format($user->leave,0,',',',') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')

<script type="text/javascript">
    // DataTable
    Spandiv.DataTable("#datatable", {
        pageLength: -1,
        rowsGroup: [0, 1, 5, 6, 7],
        orderAll: true
    });

    // Datepicker
    Spandiv.DatePicker("input[name=t1], input[name=t2]");

    // Change Group
    $(document).on("change", "select[name=group]", function() {
        var group = $(this).val();
        $.ajax({
            type: "get",
            url: "{{ route('api.office.index') }}",
            data: {group: group},
            success: function(result){
                var html = '<option value="0" selected>Semua Kantor</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("select[name=office]").html(html).removeAttr("disabled");
            }
        });
        $.ajax({
            type: 'get',
            url: "{{ route('api.position.index') }}",
            data: {group: group},
            success: function(result){
                var html = '<option value="0" disabled selected>--Pilih Jabatan--</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("select[name=position]").html(html);
            }
        });
        // $("#form-filter").find("button[type=submit]").attr("disabled","disabled");
    });

    // Change Date
    $(document).on("change", "input[name=t1], input[name=t2]", function(){
        var t1 = $("input[name=t1]").val();
        var t2 = $("input[name=t2]").val();
        (t1 != '' && t2 != '') ? $("#form-filter button[type=submit]").removeAttr("disabled") : $("#form-filter button[type=submit]").attr("disabled","disabled");
    });

    $('#exportExcel').click(function(){
        pos = '{{ Auth::user()->role_id }}';
        group_cek = '{{ Auth::user()->group_id }}';
        group_id = $('#group').val() != null ? $('#group').val() : null;
        position_id = $('#position').val() != null ? $('#position').val() : null;
        office_id = $('#office').val() != null ? $('#office').val() : null;
        status = $('#status').val();
        t1 = $('#t1').val();
        t2 = $('#t2').val();

        if(pos == 1){
            window.location = "{{ route('admin.summary.attendance.export') }}?position_id=" + position_id + "&office_id=" + office_id + "&status=" + status + "&group_id=" + group_id + "&t1=" + t1 + "&t2=" + t2;
        }
        else{
            window.location = "{{ route('admin.summary.attendance.export') }}?position_id=" + position_id + "&office_id=" + office_id + "&status=" + status + "&group_id=" + group_cek + "&t1=" + t1 + "&t2=" + t2;
        }
    });
</script>

@endsection

@section('css')

<style type="text/css">
    .table tbody tr td {vertical-align: top;}
</style>

@endsection