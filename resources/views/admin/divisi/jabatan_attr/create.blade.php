@extends('faturhelper::layouts/admin/main')

@section('title', 'Tambah Cuti')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Tambah Karyawan</h1>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('admin.jabatan.divisi.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Perusahaan <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="group_id" class="form-select form-select-sm {{ $errors->has('group_id') ? 'border-danger' : '' }}" id="group" {{ Auth::user()->role_id == role('super-admin') ? '' : 'disabled' }}>
                                <option value="" disabled selected>--Pilih--</option>
                                @if(Auth::user()->role_id == role('super-admin'))
                                    @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                    @endforeach
                                @else
                                    @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ Auth::user()->group_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('group_id'))
                            <div class="small text-danger">{{ $errors->first('group_id') }}</div>
                            @endif
                        </div>
                    </div>
                    @php
                        $disabled_selected = '';
                        if(Auth::user()->role_id == role('super-admin') && old('group_id') == null)
                            $disabled_selected = 'disabled';
                    @endphp
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Kantor <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="office_id" class="form-select form-select-sm {{ $errors->has('office_id') ? 'border-danger' : '' }}" id="office" {{ Auth::user()->role_id == role('super-admin') && old('group_id') == null ? 'disabled' : '' }}>
                                <option value="" disabled selected>--Pilih--</option>
                                @if(Auth::user()->role_id == role('super-admin') && old('group_id') != null)
                                    @foreach(\App\Models\Group::find(old('group_id'))->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                        <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                    @endforeach
                                @elseif(Auth::user()->role_id == role('admin'))
                                    @foreach(\App\Models\Group::find(Auth::user()->group_id)->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                    <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                    @endforeach
                                @elseif(Auth::user()->role_id == role('manager'))
                                    @foreach(Auth::user()->managed_offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                    <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('office_id'))
                            <div class="small text-danger">{{ $errors->first('office_id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Jabatan <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select disabled name="position" class="form-select form-select-sm" id="position">
                                <option class="form-select form-select-sm" >--Pilih--</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Nama Karyawan <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select disabled name="user_id" class="form-select form-select-sm" id="user_id">
                                <option class="form-select form-select-sm user_id_list" >--Pilih--</option>
                            </select>
                            <div id="badge-users">
                                <span id="list_user[]" class="badge badge-sm badge-success bg-success">Success</span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-2 col-md-3"></div>
                        <div class="col-lg-10 col-md-9">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi-save me-1"></i> Submit</button>
                            <a href="{{ route('admin.jabatan.divisi.index',['id_divisi'=> $id_divisi]) }}" class="btn btn-sm btn-secondary"><i class="bi-arrow-left me-1"></i> Kembali</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')

<script type="text/javascript">
    // Datepicker
    Spandiv.DatePicker("input[name=date]");

    //position
    $(document).on("change", "#office", function() {
        var group = $('#group').val();
        var office = $(this).val();

        $.ajax({
            type: "get",
            url: "{{ route('api.position.index') }}",
            data: {group: group},
            success: function(result){
                var html = '<option value="" disabled>--Pilih--</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("#position").html(html).removeAttr("disabled");
            }
        });
        
    });

    $(document).on('change',"#position", function(){
        var group = $('#group').val();
        var office = $('#office').val();
        var position = $(this).val();
        $.ajax({
            type: "get",
            url: "{{ route('api.user.index') }}",
            data: {group: group, office: office, position: position},
            success: function(result){
                var html = '<option class="user_id_list" value="" disabled>--Pilih--</option>';
                $(result).each(function(key,value){
                    html += '<option class="user_id_list" value="' + value.id + '">' + value.name + '</option>';
                });
                $("#user_id").html(html).removeAttr("disabled");
            }
        })
    });

    $(document).on('click',"#user_id", function(){
        var user_id = $(this).val();
        var user_id_name = $(this).text();
        $("#badge-users").append('<span id="list_user[]" class="badge badge-sm badge-success bg-success">'+user_id+'</span>');
    });

    // Change Group
    $(document).on("change", "#group", function() {
        var group = $(this).val();
        $.ajax({
            type: "get",
            url: "{{ route('api.office.index') }}",
            data: {group: group},
            success: function(result){
                var html = '<option value="" selected>--Pilih--</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("#office").html(html).removeAttr("disabled");
            }
        });
    });

    // Change Office
    $(document).on("change", "#office", function() {
        var office = $(this).val();
        var group = $("#group").val();
        $.ajax({
            type: "get",
            url: "{{ route('api.user.index') }}",
            data: {group: group, office: office},
            success: function(result){
                var html = '<option value="" selected>--Pilih--</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("#member").html(html).removeAttr("disabled");
            }
        });
    });
</script>

@endsection