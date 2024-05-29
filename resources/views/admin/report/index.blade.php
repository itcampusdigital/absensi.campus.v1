@extends('faturhelper::layouts/admin/main')

@section('title', 'report')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Daily Activity Progress report</h1>
    {{-- <a href="{{ route('admin.attendance.create') }}" class="btn btn-sm btn-primary"><i class="bi-plus me-1"></i> Tambah Absensi</a> --}}
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
                        <input type="text" id="t1" name="t1" class="form-control form-control-sm input-tanggal" value="{{ Request::query('t1') != null ? Request::query('t1') : date('d/m/Y') }}" autocomplete="off" data-bs-toggle="tooltip" title="Dari Tanggal">
                    </div>
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <input type="text" id="t2" name="t2" class="form-control form-control-sm input-tanggal" value="{{ Request::query('t2') != null ? Request::query('t2') : date('d/m/Y') }}" autocomplete="off" data-bs-toggle="tooltip" title="Sampai Tanggal">
                    </div>
                    <div class="ms-lg-2 ms-0">
                        <button type="submit" class="btn btn-sm btn-info"><i class="bi-filter-square me-1"></i> Filter</button>
                    </div>
                </form>
            </div>
            <hr class="my-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered" id="datatable">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">No</th>
                                <th width="300">Nama</th>
                                <th width="250">Tanggal</th>
                                <th>Note</th>
                                <th>Report</th>
                                <th width="200"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dailies as $key=>$daily)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="data_names" id="data_names{{ $daily->id }}">{{ $daily->user->name }}</td>
                                <td class="data_dates" id="data_dates{{ $daily->id }}">{{ $daily->date }}</td>
                                <td class="data_notes" id="data_notes{{ $daily->id }}">{{ $daily->note }}</td>
                                <td > 
                                    @foreach ($daily->report as $key=>$report)
                                        <li class="reports{{ $daily->id }}" id="report{{ $daily->id }}">{{ $report->report }} <span class="scores{{ $daily->id }}" id="score{{ $daily->id }}">({{ $report->score }})</span>-</li>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info"><i class="bi-pencil-square me-1"></i> Edit</a>
                                    <button data-toggle="modal" type="button" data-id="{{ $daily->id }}" id="btnModal" class="btnModals btn btn-sm btn-primary"><i class="bi bi-eye-fill"></i> View</button>
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

@include('admin.report.komponen.modal')
{{-- <form class="form-delete d-none" method="post" action="{{ route('admin.attendance.delete') }}">
    @csrf
    <input type="hidden" name="id">
</form> --}}

@endsection

@section('js')

<script type="text/javascript">

    //modal
    var modal = document.getElementById("modal")
    var span = document.getElementsByClassName("close")[0]
    

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none"
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    //send data to modal
    $('.btnModals').click(function(){
        modal.style.display = "block"
        var id = $(this).data('id');
        var name = $('#data_names'+id).text()
        var date = $('#data_dates'+id).text()
        var note = $('#data_notes'+id).text()

        var report = $('.reports'+id).text()
        var score = $('.scores'+id).text()

        var cek = report.split('-')

        var report_panjang = $('.reports'+id).length
        var score_panjang = $('.score'+id).length

        $('.get_data_name').text(name)
        $('.get_data_date').text(date)
        $('.get_data_note').text(note)
        // $('.data_report').text(report)
        // $('.data_score').text(score)
        

        
        $('.get_data_report').empty().append(cek)
        // for(var i = 0; i < report_panjang; i++){
        //     $('.get_data_report').append('cek')
        // }

        
    }) 

    
    
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

    // Change Date
    $(document).on("change", "input[name=t1], input[name=t2]", function(){
        var t1 = $("input[name=t1]").val();
        var t2 = $("input[name=t2]").val();
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