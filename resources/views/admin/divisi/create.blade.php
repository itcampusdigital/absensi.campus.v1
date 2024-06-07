@extends('faturhelper::layouts/admin/main')

@section('title', 'Tambah Cuti')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Tambah Cuti</h1>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('admin.jabatan.store') }}" enctype="multipart/form-data">
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
                        <label class="col-lg-2 col-md-3 col-form-label">Nama jabatan <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <div class="input-group input-group-sm">
                                <input type="text" name="name" class="form-control form-control-sm {{ $errors->has('name') ? 'border-danger' : '' }}" value="{{ old('name') }}" autocomplete="off">
                                <span class="input-group-text"><i class="bi-calendar2"></i></span>
                            </div>
                            @if($errors->has('name'))
                            <div class="small text-danger">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-2 col-md-3"></div>
                        <div class="col-lg-10 col-md-9">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi-save me-1"></i> Submit</button>
                            <a href="{{ route('admin.leave.index') }}" class="btn btn-sm btn-secondary"><i class="bi-arrow-left me-1"></i> Kembali</a>
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