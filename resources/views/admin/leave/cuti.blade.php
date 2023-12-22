
@extends('faturhelper::layouts/admin/main')

@section('title', 'Rekapitulasi Cuti')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Rekapitulasi Cuti</h1>
</div>
    <div class="row">
        <div class="card">
            <div class="card-header d-sm-flex justify-content-center align-items-center">
                <form id="form-filter" class="d-lg-flex" method="get" action="">
                    {{-- <div class="mb-lg-0 mb-2">
                        <select name="year" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Periode Tahun">
                            @for($i=2023; $i>=2020; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div> --}}
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
            <div class="card-body">
                <table class="table table-responsive" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pegawai</th>
                            <th>Jatah Cuti</th>
                            <th>Jumlah Cuti diambil</th>
                            <th>Sisa Cuti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cuti as $cutis)
                        
                        <tr>
                            <td align="center">{{ $loop->iteration }}</td>
                            <td >{{ $cutis->name }}</td>
                            <td align="center"> 
                                <form action="{{ route('admin.kontrak.store') }}" 
                                    method="post" enctype="multipart/form-data" 
                                    id="#myFormID">
                                    @csrf
                                    <input style="border: none;border-color: transparent;" type="number" min="0" max="12" step="1" value="{{ $cutis->kontrak->cuti }}" id="cuti_tahunan" name="cuti_tahunan">
                                    <input type="hidden" name="user_id" value="{{ $cutis->id }}">
                                </form>
                                
                            </td>
                            <td align="center">
                                {{ $cutis->cuti_tahunan }}
                            </td>
                            <td align="center">
                                {{ $cutis->sisa_cuti}}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script type="text/javascript">

Spandiv.DataTable("#datatable");
$(document).on("change", '#cuti_tahunan', function(){
    $("#myFormID").submit();
})

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
        if(office !== null)
            $("#form-filter").find("button[type=submit]").removeAttr("disabled");
        else
            $("#form-filter").find("button[type=submit]").attr("disabled","disabled");
    });
    </script>

@endsection

