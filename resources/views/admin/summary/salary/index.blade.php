@extends('faturhelper::layouts/admin/main')

@section('title', 'Rekapitulasi Penggajian')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Rekapitulasi Penggajian</h1>
    <div class="btn-group">
        <a href="{{ route('admin.summary.office.index') }}" class="btn btn-sm btn-primary"><i class="bi-briefcase me-1"></i> Penggajian Kantor</a>
        @if(count(Request::query()) > 0)
        <a href="{{ route('admin.summary.salary.export', Request::query()) }}" class="btn btn-sm btn-success"><i class="bi-file-excel me-1"></i> Export ke Excel</a>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-sm-flex justify-content-center align-items-center">
                <form id="form-filter" class="d-lg-flex" method="get" action="">
                    <div class="mb-lg-0 mb-2">
                        <select name="month" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Periode Bulan">
                            @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ \Ajifatur\Helpers\DateTimeExt::month($i) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="year" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Periode Tahun">
                            @for($i=2023; $i>=2020; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
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
                            @elseif(Auth::user()->role_id == role('admin') || Auth::user()->role_id == role('manager'))
                                @foreach(\App\Models\Group::find(Auth::user()->group_id)->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                <option value="{{ $office->id }}" {{ Request::query('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="position" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Jabatan">
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
                        <button type="submit" class="btn btn-sm btn-info" {{ Request::query('office') != null && Request::query('position') != null ? '' : 'disabled' }}><i class="bi-filter-square me-1"></i> Filter</button>
                    </div>
                </form>
            </div>
            <hr class="my-0">
            @if(Request::query('office') != null && Request::query('position') != null)
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
                                <th rowspan="{{ count($categories) > 0 ? 2 : 1 }}">Karyawan</th>
                                <th rowspan="{{ count($categories) > 0 ? 2 : 1 }}" width="80">Tanggal Kontrak</th>
                                <th rowspan="{{ count($categories) > 0 ? 2 : 1 }}" width="80">Kehadiran dan Cuti</th>
                                @if(count($categories) > 0)
                                <th colspan="{{ count($categories) }}">Rincian Gaji Kotor</th>
                                <th rowspan="{{ count($categories) > 0 ? 2 : 1 }}" width="80">Total Gaji Kotor</th>
                                @endif
                                <th colspan="2">Rincian Potongan</th>
                                <th rowspan="{{ count($categories) > 0 ? 2 : 1 }}" width="80">Total Gaji Bersih</th>
                            </tr>
                            @if(Request::query('office') != null && Request::query('position') != null && count($categories) > 0)
                                <tr>
                                    @foreach($categories as $category)
                                    <th width="80">{{ $category->name }}</th>
                                    @endforeach
                                    <th width="100">Keterlambatan</th>
                                    <th width="100">Kasbon</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td><a href="{{ route('admin.user.detail', ['id' => $user->id]) }}">{{ $user->name }}</a></td>
                                    <td>
                                        <span class="d-none">{{ $user->end_date == null ? 1 : 0 }} {{ $user->start_date }}</span>
                                        @if($user->end_date == null)
                                            {{ date('d/m/Y', strtotime($user->start_date)) }}
                                        @else
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td align="left">
                                        @if(is_int($user->attendances))
                                            <p class="mb-0">Kehadiran:<br><span class="text-success">{{ number_format($user->attendances,0,',',',') }}x</span></p>
                                            <hr class="my-1">
                                            <p class="mb-0">Cuti<span class="text-danger">*</span>: <br><span class="text-success">{{ number_format($user->leaves,0,',',',') }}x</span></p>
                                        @elseif(is_array($user->attendances))
                                            @foreach($user->attendances as $key=>$attendance)
                                                <p class="mb-0">{{ $attendance['name'] }}:<br><span class="text-success">{{ number_format($attendance['count'],0,',',',') }}x</span></p>
                                                <hr class="my-1">
                                            @endforeach
                                            <p class="mb-0">Cuti<span class="text-danger">*</span>: <br><span class="text-success">{{ number_format($user->leaves,0,',',',') }}x</span></p>
                                        @endif
                                    </td>
                                    @if(count($user->salary) > 0)
                                        @foreach($user->salary as $salary)
                                        <td align="right">
                                            <span class="amount-indicator" data-user="{{ $user->id }}" data-category="{{ $salary['category']->id }}">{{ number_format($salary['amount'],0,',',',') }}</span>
                                            <hr class="my-1">
                                            @if($salary['category']->type_id == 1)
												@if($salary['category']->indicators()->count() > 1)
                                                <p class="text-start text-muted mb-0">Nilai:</p>
                                                <input type="number" class="form-control form-control-sm user-indicator" data-user="{{ $user->id }}" data-category="{{ $salary['category']->id }}" data-month="{{ $month }}" data-year="{{ $year }}" value="{{ $salary['value'] }}">
												<p class="small text-start text-decoration-underline text-info mb-0">
													<a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" title="Keterangan" data-bs-content="
													@foreach($salary['category']->indicators as $indicator)
														@if($indicator->upper_range != null)
															<p>Jika nilai diisi <strong>{{ $indicator->lower_range }}</strong> - <strong>{{ $indicator->upper_range }}</strong>, maka {{ $salary['category']->name }} adalah <strong>Rp {{ number_format($indicator->amount,0,',',',') }}</strong>.</p>
														@else
															<p class='mb-0'>Jika nilai diisi >= <strong>{{ $indicator->lower_range }}</strong>, maka {{ $salary['category']->name }} adalah <strong>Rp {{ number_format($indicator->amount,0,',',',') }}</strong>.</p>
														@endif
													@endforeach
													">Keterangan</a>
												</p>
												@endif
                                            @elseif($salary['category']->type_id == 2)
                                                <p class="text-start text-muted mb-0">Masa Kerja:</p>
                                                <p class="text-start text-success mb-0">{{ number_format($salary['value'],1,'.',',') }} bulan</p>
                                            @elseif($salary['category']->type_id == 3)
                                                <p class="text-start text-muted mb-0">Sertifikasi:</p>
                                                <p class="text-start text-success mb-0">{{ number_format($salary['value'],0,'.',',') }}x</p>
                                            @endif
                                        </td>
                                        @endforeach
                                        <td align="right">
                                            <span class="subtotal-salary" data-user="{{ $user->id }}">{{ number_format($user->subtotalSalary,0,',',',') }}</span>
                                        </td>
                                        <td align="right">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control user-late-fund" data-user="{{ $user->id }}" data-month="{{ $month }}" data-year="{{ $year }}" value="{{ number_format(late_fund($user->id, $month, $year),0,',',',') }}">
                                            </div>
                                            <hr class="my-1">
                                            <p class="text-start text-muted mb-0">Terlambat:</p>
                                            <p class="text-start text-success mb-0">{{ number_format(late($user->id),0,',',',') }}x</p>
                                        </td>
                                        <td align="right">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control user-debt-fund" data-user="{{ $user->id }}" data-month="{{ $month }}" data-year="{{ $year }}" value="{{ number_format(debt_fund($user->id, $month, $year),0,',',',') }}">
                                            </div>
                                        </td>
                                        <td align="right">
                                            <span class="total-salary" data-user="{{ $user->id }}">{{ number_format($user->totalSalary,0,',',',') }}</span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td colspan="{{ count($categories) > 0 ? count($categories) + 6 : 5 }}">Total Gaji Karyawan</td>
                                <td align="right"><span class="overall-salary">{{ number_format($overall,0,',',',') }}</span></td>
                            </tr>
                        </tfoot>
                    </table>
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
        orderAll: true
    });
	
	// Popover
    Spandiv.Popover();

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
    $(document).on("change", "select[name=office], select[name=position]", function() {
        var office = $("select[name=office]").val();
        var position = $("select[name=position]").val();
        if(office !== null && position !== null)
            $("#form-filter").find("button[type=submit]").removeAttr("disabled");
        else
            $("#form-filter").find("button[type=submit]").attr("disabled","disabled");
    });

    // Change the User Indicator
    $(document).on("change", ".user-indicator", function() {
        var user = $(this).data("user");
        var category = $(this).data("category");
        var month = $(this).data("month");
        var year = $(this).data("year");
        var value = $(this).val();
        if(value < 0) {
            $(this).val(0);
            return;
        }
        $.ajax({
            type: "post",
            url: "{{ route('admin.summary.salary.update.indicator') }}",
            data: {_token: "{{ csrf_token() }}", user: user, category: category, month: month, year: year, value: value},
            success: function(response) {
                $(".amount-indicator[data-user=" + user + "][data-category=" + category + "]").text(response.amount);
                $(".subtotal-salary[data-user=" + user + "]").text(response.subtotal);
                $(".total-salary[data-user=" + user + "]").text(response.total);
                $(".overall-salary").text(response.overall);
            }
        });
    });

    // Change the User Late Fund
    $(document).on("keyup", ".user-late-fund", function() {
        var user = $(this).data("user");
        var month = $(this).data("month");
        var year = $(this).data("year");
        var value = $(this).val().replace(",","").toString();
        $.ajax({
            type: "post",
            url: "{{ route('admin.summary.salary.update.late-fund') }}",
            data: {_token: "{{ csrf_token() }}", user: user, month: month, year: year, amount: value},
            success: function(response) {
                $(".total-salary[data-user=" + user + "]").text(response.total);
                $(".overall-salary").text(response.overall);
            }
        });
        $(this).val(Spandiv.NumberFormat(value));
    });

    // Change the User Debt Fund
    $(document).on("keyup", ".user-debt-fund", function() {
        var user = $(this).data("user");
        var month = $(this).data("month");
        var year = $(this).data("year");
        var value = $(this).val().replace(",","").toString();
        $.ajax({
            type: "post",
            url: "{{ route('admin.summary.salary.update.debt-fund') }}",
            data: {_token: "{{ csrf_token() }}", user: user, month: month, year: year, amount: value},
            success: function(response) {
                $(".total-salary[data-user=" + user + "]").text(response.total);
                $(".overall-salary").text(response.overall);
            }
        });
        $(this).val(Spandiv.NumberFormat(value));
    });
</script>

@endsection

@section('css')

<style type="text/css">
    .table tbody tr td {vertical-align: top;}    
</style>

@endsection