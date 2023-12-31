@extends('faturhelper::layouts/admin/main')

@section('title', 'Rekapitulasi Penggajian Kantor')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Rekapitulasi Penggajian Kantor</h1>
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
                            <option value="0">Semua Perusahaan</option>
                            @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ Request::query('group') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="ms-lg-2 ms-0">
                        <button type="submit" class="btn btn-sm btn-info"><i class="bi-filter-square me-1"></i> Filter</button>
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
                                <th width="20"></th>
                                <th>Kantor</th>
                                @if(Auth::user()->role_id == role('super-admin'))
                                <th width="150">Perusahaan</th>
                                @endif
                                <th width="80">Total Gaji Kotor</th>
                                <th width="80">Total Keterlambatan</th>
                                <th width="80">Total Kasbon</th>
                                <th width="80">Total Gaji Bersih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offices as $key=>$office)
                            <tr>
                                <td align="center">{{ ($key+1) }}</td>
                                <td><a href="{{ route('admin.office.detail', ['id' => $office->id]) }}">{{ $office->name }}</a></td>
                                @if(Auth::user()->role_id == role('super-admin'))
                                <td>
                                    @if($office->group)
                                        <a href="{{ route('admin.group.detail', ['id' => $office->group->id]) }}">{{ $office->group->name }}</a>
                                    @endif
                                </td>
                                @endif
                                <td align="right">{{ number_format($office->grossSalary,0,',',',') }}</td>
                                <td align="right">{{ number_format($office->late_fund,0,',',',') }}</td>
                                <td align="right">{{ number_format($office->debt_fund,0,',',',') }}</td>
                                <td align="right">{{ number_format($office->total,0,',',',') }}</td>
                            </tr>
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
        orderAll: true
    });
</script>

@endsection

@section('css')

<style type="text/css">
    .table tbody tr td {vertical-align: top;}    
</style>

@endsection