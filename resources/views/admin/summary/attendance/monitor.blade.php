@extends('faturhelper::layouts/admin/main')

@section('title', 'Rekapitulasi Monitoring Absensi')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Rekapitulasi Monitoring Absensi</h1>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-sm-flex justify-content-center align-items-center">
                <form id="form-filter" class="d-lg-flex" method="get" action="">
                    <div class="mb-lg-0 mb-2">
                        <select name="month" id="month" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Periode Bulan">
                            @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ \Ajifatur\Helpers\DateTimeExt::month($i) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="year" id="year" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Periode Tahun">
                            @for($i=date("Y"); $i>=2020; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    @if(Auth::user()->role_id == role('super-admin'))
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="group" id="group" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Perusahaan">
                            <option value="0">--Pilih Perusahaan--</option>
                            @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ Request::query('group') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="office" id="office" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Kantor">
                            <option value="0" disabled selected>--Pilih Kantor--</option>
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
                            <option value="0" disabled selected>--Pilih Jabatan--</option>
                            @if(Auth::user()->role_id == role('super-admin'))
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
                    <div class="ms-lg-2 ms-0">
                        <button type="submit" class="btn btn-sm btn-info" {{ Request::query('office') != null ? '' : 'disabled' }}><i class="bi-filter-square me-1"></i> Filter</button>
                        {{-- <a type="button" id="exportExcel" class="btn btn-sm btn-success"><i class="bi-filter-square me-1"></i> Export Excel</a> --}}
                    </div>
                </form>
                    <form action="{{ route('admin.summary.attendance.export.dataUser') }}" enctype="multipart/form-data" method="get">
                        @csrf
                        <div class="ms-lg-2 ms-0">
                            <input type="hidden" name="data" value="{{ encrypt(json_encode($ceks)) }}">
                            <input type="hidden" name="date_array" value="{{ encrypt(json_encode($date_array['day'])) }}">
                            <input type="hidden" name="dates_convert_array" value="{{ encrypt(json_encode($dates_convert)) }}">
                            <button {{ request('office') == 19 ? '' : 'disabled' }} id="buttonExcel" type="submit" class="btn btn-sm btn-success"><i class="bi-filter-square me-1"></i> Export Excel</button>
                        </div>
                    </form>
                
            </div>
            <hr class="my-0">
            @if(Request::query('office') != null && count($work_hours) > 0)
            <div class="card-body">
                @if(Session::get('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-message">{{ Session::get('message') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered" id="datatable">
                        @if(request('office') == 19)
                            <thead class="bg-light">
                                <tr>
                                    <th width="10">No</th>
                                    <th width="200">Nama</th>
                                    @foreach ($date_array['day'] as $days)
                                        <th>{{ $days }}</th>
                                    @endforeach
                                    <th>Hadir</th>
                                    <th>Sakit</th>
                                    <th>Izin</th>
                                    <th>Alpa</th>
                                </tr>
                            </thead>
                            @if($ceks != null)
                                <tbody>
                                        @for ($i=0; $i < count($ceks); $i++)
                                            <tr>
                                                <td align="center">{{ $i+1 }}</td>
                                                <td>{{ $ceks[$i]['name'] }}</td>

                                                @for($j=0;$j<count($dates_convert);$j++)
                                                    @if(array_key_exists($j,$ceks[$i]['date']))
                                                        <td align="center" style="color: green"><b>{{ $ceks[$i]['date'][$j] }}</b></td>
                                                    @elseif(array_key_exists($j,$ceks[$i]['izin']))
                                                        <td align="center" style="color: rgb(255, 0, 170)"><b>{{ $ceks[$i]['izin'][$j] }}</b></td>
                                                    @elseif(array_key_exists($j,$ceks[$i]['sakit']))
                                                        <td align="center" style="color: blue"><b>{{ $ceks[$i]['sakit'][$j] }}</b></td>
                                                    @elseif(array_key_exists($j,$ceks[$i]['alpa']))
                                                        <td align="center"><b>{{ $ceks[$i]['alpa'][$j] }}</b></td>
                                                    @else
                                                        <td style="background-color: rgb(247, 78, 78)"></td>
                                                    @endif
                                                @endfor
                                                <td align="center">{{ count($ceks[$i]['date']) }}</td>
                                                <td align="center">{{ count($ceks[$i]['sakit']) }}</td>
                                                <td align="center">{{ count($ceks[$i]['izin']) }}</td>
                                                <td align="center">{{ count($ceks[$i]['alpa']) }}</td>

                                            </tr>
                                        @endfor
                                </tbody>
                            @else
                                <tbody>
                                </tbody>
                            @endif
                        @else
                            <thead class="bg-light">
                                <tr>
                                    <th rowspan="2" width="20"></th>
                                    <th rowspan="2" width="80">Tanggal</th>
                                    @if(count($work_hours) > 0)
                                    <th colspan="{{ count($work_hours) }}">Jam Kerja</th>
                                    @endif
                                </tr>
                                @if(Request::query('office') != null && Request::query('position') != null && count($work_hours) > 0)
                                    <tr>
                                        @foreach($work_hours as $work_hour)
                                        <th>{{ $work_hour->name }}</th>
                                        @endforeach
                                    </tr>
                                @endif
                            </thead>
                            <tbody>
                                    @foreach($dates as $key=>$date)
                                        <tr>
                                            <td align="center">{{ ($key+1) }}</td>
                                            <td>
                                                <span class="d-none">{{ \Ajifatur\Helpers\DateTimeExt::change($date) }}</span>
                                                {{ $date }}
                                            </td>
                                            @if(count($work_hours) > 0)
                                                @foreach($work_hours as $work_hour)
                                                    @php
                                                        $attendances = \App\Models\Attendance::has('user')->where('workhour_id','=',$work_hour->id)->where('date','=',\Ajifatur\Helpers\DateTimeExt::change($date))->get();
                                                    @endphp
                                                    <td>
                                                        @if(count($attendances) > 0)
                                                            @foreach($attendances as $key=>$attendance)
                                                                <a href="{{ route('admin.attendance.edit', ['id' => $attendance->id]) }}" class="{{ count($attendances) > $work_hour->quota ? 'text-danger' : '' }}">{{ $attendance->user->name }}</a>
                                                                @if($key < count($attendances) - 1)
                                                                <hr class="my-1">
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                @endforeach
                                            @endif
                                        </tr>
                                    @endforeach
                            </tbody>
                        @endif
                    </table>
                </div>
            </div>
            @elseif(Request::query('office') != null && Request::query('position') != null && count($work_hours) <= 0)
            <div class="card-body">
                <div class="alert alert-danger show mb-0" role="alert">
                    <div class="alert-message">Jabatan tidak ada di dalam kantor.</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('js')

<script type="text/javascript">
    // DataTable
    Spandiv.DataTable("#datatable", {
        pageLength: -1,
        orderAll: true
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
        $("#form-filter").find("button[type=submit]").attr("disabled","disabled");
    });

    // Change the Office and Position
    $(document).on("change", "select[name=office]", function() {
        var office = $("select[name=office]").val();
        var position = $("select[name=position]").val();
        if(office != null)
            $("#form-filter").find("button[type=submit]").removeAttr("disabled");
        else
            $("#form-filter").find("button[type=submit]").attr("disabled","disabled");
    });

    $('#exportExcel').click(function(){
        pos = '{{ Auth::user()->role_id }}';
        group_cek = '{{ Auth::user()->group_id }}';
        group_id = $('#group').val() != null ? $('#group').val() : null;
        position_id = $('#position').val() != null ? $('#position').val() : null;
        office_id = $('#office').val() != null ? $('#office').val() : null;
        year = $('#year').val();
        month = $('#month').val();

        if(pos == 1){
            window.location = "{{ route('admin.summary.attendance.monitor.export') }}?position_id=" + position_id + "&office_id=" + office_id + "&group_id=" + group_id + "&year=" + year + "&month=" + month;
        }
        else{
            window.location = "{{ route('admin.summary.attendance.monitor.export') }}?position_id=" + position_id + "&office_id=" + office_id + "&group_id=" + group_id + "&year=" + year + "&month=" + month;
        }
    });
</script>

@endsection

@section('css')

<style type="text/css">
    .table tbody tr td {vertical-align: top;}
</style>

@endsection