@extends('faturhelper::layouts/admin/main')

@section('title', 'Edit Cuti')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Edit Cuti</h1>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('admin.lembur.update',['id' => $lembur->id]) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $lembur->id }}">
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Perusahaan <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="group_id" class="form-select form-select-sm {{ $errors->has('group_id') ? 'border-danger' : '' }}" id="group" disabled>
                                <option value="" disabled>--Pilih--</option>
                                @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ $lembur->user->group_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('group_id'))
                            <div class="small text-danger">{{ $errors->first('group_id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Kantor <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="office_id" class="form-select form-select-sm {{ $errors->has('office_id') ? 'border-danger' : '' }}" id="office" disabled>
                                <option value="" selected>--Pilih--</option>
                                @foreach(\App\Models\Group::find($lembur->user->group_id)->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                <option value="{{ $office->id }}" {{ $lembur->user->office_id == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('office_id'))
                            <div class="small text-danger">{{ $errors->first('office_id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Karyawan <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="user_id" class="form-select form-select-sm {{ $errors->has('user_id') ? 'border-danger' : '' }}" id="member" disabled>
                                @foreach(\App\Models\Office::find($lembur->user->office_id)->users()->where('role_id','=',role('member'))->where('end_date','=',null)->orderBy('name','asc')->get() as $user)
                                <option value="{{ $user->id }}" {{ $lembur->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('user_id'))
                            <div class="small text-danger">{{ $errors->first('user_id') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Tanggal <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <div class="input-group input-group-sm">
                                <input type="text" name="date" class="form-control form-control-sm {{ $errors->has('date') ? 'border-danger' : '' }}" value="{{ date('d/m/Y', strtotime($lembur->date)) }}" autocomplete="off">
                                <span class="input-group-text"><i class="bi-calendar2"></i></span>
                            </div>
                            @if($errors->has('date'))
                            <div class="small text-danger">{{ $errors->first('date') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-12 col-sm-2 col-md-3 col-lg-2  col-form-label">Mulai<span class="text-danger">*</span></label>
                        <div class="col-12 col-sm-4 col-md-3 col-lg-4 ">
                            <div class="input-group input-group-sm">
                                <input type="time" name="start_time" class="form-control form-control-sm {{ $errors->has('start_time') ? 'border-danger' : '' }}" value="{{ $lembur->start_time }}" autocomplete="off">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                            </div>
                            @if($errors->has('start_time'))
                                <div class="small text-danger">{{ $errors->first('start_time') }}</div>
                            @endif
                        </div>
                        <label class="col-12 col-sm-2 col-md-3 col-lg-2  col-form-label">Akhir<span class="text-danger">*</span></label>
                        <div class="col-12 col-sm-4 col-md-3 col-lg-4 ">
                            <div class="input-group input-group-sm">
                                <input type="time" name="end_time" class="form-control form-control-sm {{ $errors->has('end_time') ? 'border-danger' : '' }}" value="{{ $lembur->end_time }}" autocomplete="off">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                            </div>
                            @if($errors->has('end_time'))
                                <div class="small text-danger">{{ $errors->first('end_time') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Keterangan <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <div class="input-group input-group-sm">
                                <textarea type="text" name="keterangan" 
                                        class="form-control form-control-sm {{ $errors->has('keterangan') ? 'border-danger' : '' }}"
                                        autocomplete="off">{{ $lembur->keterangan }}</textarea>
                            </div>
                            @if($errors->has('keterangan'))
                            <div class="small text-danger">{{ $errors->first('keterangan') }}</div>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-2 col-md-3"></div>
                        <div class="col-lg-10 col-md-9">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi-save me-1"></i> Submit</button>
                            <a href="{{ route('admin.lembur.index') }}" class="btn btn-sm btn-secondary"><i class="bi-arrow-left me-1"></i> Kembali</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('css')
<style type="text/css">
    input[type="time"]::-webkit-calendar-picker-indicator {
        background: transparent;
        bottom: 0;
        color: transparent;
        cursor: pointer;
        height: auto;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
        width:auto
    }
</style>
@endsection
@section('js')

<script type="text/javascript">
    // Datepicker
    Spandiv.DatePicker("input[name=date]");
</script>

@endsection