@extends('faturhelper::layouts/admin/main')

@section('title', 'rekap DAP')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Rekap Daily Activity Progress</h1>
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
                        <select name="divisi" id="divisi" class="divisi form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Divisi">
                            <option value="0">--Pilih Jabatan--</option>
                            @foreach ($divisis as $divisi)
                                <option value="{{ $divisi->id }}">{{ $divisi->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select disabled name="pegawai" id="pegawai" class="pegawai form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Pegawai">
                            <option value="0">--Pilih Pegawai--</option>
                            
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select disabled name="bulan" id="bulan" class="bulan form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Bulan">
                            <option value="0">--Pilih Bulan--</option>
                            @foreach (hitungan_bulan() as $key=>$bulan)
                                <option value="{{ $key }}">{{ $bulan }}</option>
                                    
                            @endforeach
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select disabled name="tahun" id="tahun" class="tahun form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Bulan">
                            <option value="0">--Pilih Tahun--</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>

                        </select>
                    </div>

    
                    <div class="ms-lg-2 ms-0">
                        <button type="submit" class="btn btn-sm btn-info" disabled><i class="bi-filter-square me-1"></i> Filter</button>
                    </div>
                </form>
            </div>
            <hr class="my-0">
            <div class="card-body">
                {{-- <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered" id="datatable">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">No</th>
                                <th width="300">Nama</th>
                                <th width="250">Tanggal</th>
                                <th>Note</th>
                                <th width="200"></th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div> --}}
            </div>
        </div>
    </div>
</div>

@include('admin.report.komponen.modal')
{{-- <form class="form-delete d-none" method="post" action="{{ route('admin.attendance.delete') }}">
    @csrf
    <input type="hidden" name="id">
</form> --}}

@endsection

@section('js')

<script type="text/javascript">    

    function cek_tanggal(month,year){
        const oneDay = 24 * 3600 * 1000; // hours*minutes*seconds*milliseconds
        months = Number(month)
        month_start = (months - 1) == 0 ? 12 : months - 1
        year_end = month_start == 12 ? year+1 : year
        const firstDate = "24/"+month_start+"/"+year
        const secondDate = "23/"+(months)+"/"+year_end

        const diffDays = Math.abs((firstDate - secondDate) / oneDay);

        return year_end

    }
    // DataTable
    Spandiv.DataTable("#datatable");

    // Datepicker
    Spandiv.DatePicker("input[name=t1], input[name=t2]");
    
    // Button Delete
    Spandiv.ButtonDelete(".btn-delete", ".form-delete");

    
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
        $("select[name=position]").html(html).removeAttr("disabled");
    });

    $('#divisi').change(function(){
        var divisi = $(this).val();
        $.ajax({
            type: 'get',
            url: "/api/userJobAll/"+divisi,
            success: function(result){
                
                var html = '<option value="0" disabled selected>--Pilih Pegawai--</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("select[name=pegawai]").html(html);
            }
        })
        $("#pegawai").removeAttr("disabled")
    })

    $('#pegawai').change(function(){
        var pegawai = $('#pegawai').val()
        if(pegawai != null){
            $("#bulan").removeAttr("disabled")
            $("#tahun").removeAttr("disabled")

            $('#tahun').change(function(pegawai){
                var bulan = $("#bulan").val()
                var tahun = $("#tahun").val()
                $.ajax({
                    type: 'get',
                    url: "/api/userJob/report/"+pegawai,
                    success: function(result){
                        cek = cek_tanggal(bulan,tahun)
                        console.log(cek)
                    }
                })
            })
        }
        else{
            $("#bulan").attr("disabled","disabled")
        }

    })

    // Change Date
    $("#divisi, #bulan").change(function(){
        var t1 = $("#bulan").val();
        var t2 = $("#divisi").val();
    
        (t1 != '' && t2 != '') ? $("#form-filter button[type=submit]").removeAttr("disabled") : $("#form-filter button[type=submit]").attr("disabled","disabled");
    });


</script>

@endsection

@section('css')

<style type="text/css">
    .table tbody tr td {vertical-align: top;}    
    .modal{
        display: none;
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }
</style>

@endsection