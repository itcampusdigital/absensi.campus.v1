@extends('faturhelper::layouts/admin/main')

@section('title', 'Tambah '.role(role(Request::query('role'))))

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Tambah {{ role(role(Request::query('role'))) }}</h1>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('admin.user.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="role_id" value="{{ role(Request::query('role')) }}">
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
                    @if(Request::query('role') == 'manager')
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Kantor <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="offices[]" class="form-select form-select-sm {{ $errors->has('offices') ? 'border-danger' : '' }}" id="offices" multiple="multiple" {{ Auth::user()->role_id == role('super-admin') && old('group_id') == null ? 'disabled' : '' }}>
                                <option value="" disabled>--Pilih--</option>
                                @if(Auth::user()->role_id == role('super-admin'))
                                    @if(old('offices') != null || old('group_id') != null)
                                        @foreach(\App\Models\Group::find(old('group_id'))->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                            <option value="{{ $office->id }}" {{ is_array(old('offices')) && in_array($office->id, old('offices')) ? 'selected' : '' }}>{{ $office->name }}</option>
                                        @endforeach
                                    @endif
                                @else
                                    @foreach(\App\Models\Group::find(Auth::user()->group_id)->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                    <option value="{{ $office->id }}" {{ is_array(old('offices')) && in_array($office->id, old('offices')) ? 'selected' : '' }}>{{ $office->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('offices'))
                            <div class="small text-danger">{{ $errors->first('offices') }}</div>
                            @endif
                        </div>
                    </div>
                    @endif
                    @if(Request::query('role') == 'member')
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Kantor <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="office_id" class="form-select form-select-sm {{ $errors->has('office_id') ? 'border-danger' : '' }}" id="office" {{ Auth::user()->role_id == role('super-admin') && old('group_id') == null ? 'disabled' : '' }}>
                                @if(Auth::user()->role_id == role('super-admin'))
                                    @if(old('office_id') != null || old('group_id') != null)
                                        <option value="" selected>--Pilih--</option>
                                        @foreach(\App\Models\Group::find(old('group_id'))->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
                                            <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="" selected>--Pilih--</option>
                                    @endif
                                @else
                                    <option value="" selected>--Pilih--</option>
                                    @foreach(\App\Models\Group::find(Auth::user()->group_id)->offices()->orderBy('is_main','desc')->orderBy('name','asc')->get() as $office)
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
                            <select name="position_id" class="form-select form-select-sm {{ $errors->has('position_id') ? 'border-danger' : '' }}" id="position" {{ Auth::user()->role_id == role('super-admin') && old('group_id') == null ? 'disabled' : '' }}>
                                @if(Auth::user()->role_id == role('super-admin'))
                                    @if(old('position_id') != null || old('group_id') != null)
                                        <option value="" selected>--Pilih--</option>
                                        @foreach(\App\Models\Group::find(old('group_id'))->positions()->orderBy('name','asc')->get() as $position)
                                            <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="" selected>--Pilih--</option>
                                    @endif
                                @else
                                    <option value="" selected>--Pilih--</option>
                                    @foreach(\App\Models\Group::find(Auth::user()->group_id)->positions()->orderBy('name','asc')->get() as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('position_id'))
                            <div class="small text-danger">{{ $errors->first('position_id') }}</div>
                            @endif
                        </div>
                    </div>
                    @endif
                    <hr>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Nama <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <input type="text" name="name" class="form-control form-control-sm {{ $errors->has('name') ? 'border-danger' : '' }}" value="{{ old('name') }}" autofocus>
                            @if($errors->has('name'))
                            <div class="small text-danger">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>
                    @if(Request::query('role') == 'member')
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <div class="input-group input-group-sm">
                                <input type="text" name="birthdate" class="form-control form-control-sm {{ $errors->has('birthdate') ? 'border-danger' : '' }}" value="{{ old('birthdate') }}" autocomplete="off">
                                <span class="input-group-text"><i class="bi-calendar2"></i></span>
                            </div>
                            @if($errors->has('birthdate'))
                            <div class="small text-danger">{{ $errors->first('birthdate') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            @foreach(gender() as $gender)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="gender-{{ $gender['key'] }}" value="{{ $gender['key'] }}" {{ old('gender') == $gender['key'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="gender-{{ $gender['key'] }}">
                                    {{ $gender['name'] }}
                                </label>
                            </div>
                            @endforeach
                            @if($errors->has('gender'))
                            <div class="small text-danger">{{ $errors->first('gender') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Alamat <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <textarea name="address" class="form-control form-control-sm {{ $errors->has('address') ? 'border-danger' : '' }}" rows="3">{{ old('address') }}</textarea>
                            @if($errors->has('address'))
                            <div class="small text-danger">{{ ucfirst($errors->first('address')) }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Pendidikan Terakhir</label>
                        <div class="col-lg-10 col-md-9">
                            <textarea name="latest_education" class="form-control form-control-sm {{ $errors->has('latest_education') ? 'border-danger' : '' }}" rows="3">{{ old('latest_education') }}</textarea>
                            @if($errors->has('latest_education'))
                            <div class="small text-danger">{{ ucfirst($errors->first('latest_education')) }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">NIK</label>
                        <div class="col-lg-10 col-md-9">
                            <input type="text" name="identity_number" class="form-control form-control-sm {{ $errors->has('identity_number') ? 'border-danger' : '' }}" value="{{ old('identity_number') }}">
                            @if($errors->has('identity_number'))
                            <div class="small text-danger">{{ $errors->first('identity_number') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Tanggal Bergabung <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <div class="input-group input-group-sm">
                                <input type="text" name="start_date" class="form-control form-control-sm {{ $errors->has('start_date') ? 'border-danger' : '' }}" value="{{ old('start_date') }}" autocomplete="off">
                                <span class="input-group-text"><i class="bi-calendar2"></i></span>
                            </div>
                            @if($errors->has('start_date'))
                            <div class="small text-danger">{{ $errors->first('start_date') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Mulai Kontrak</label>
                        <div class="col-lg-10 col-md-9">
                            <div class="input-group input-group-sm">
                                <input placeholder="Tanggal Mulai Kontrak Kerja" type="text" name="start_date_kontrak" class="form-control form-control-sm {{ $errors->has('start_date_kontrak') ? 'border-danger' : '' }}" value="{{ old('start_date_kontrak') }}" autocomplete="off">
                                <span class="input-group-text"><i class="bi-calendar2"></i></span>
                            </div>
                            @if($errors->has('start_date_kontrak'))
                            <div class="small text-danger">{{ $errors->first('start_date_kontrak') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Masa Kontrak</label>
                        <div class="col-lg-10 col-md-9">
                            <div class="input-group input-group-sm">
                                <input placeholder="Lama masa kontrak" type="number" name="masa" class="form-control form-control-sm {{ $errors->has('masa') ? 'border-danger' : '' }}" value="{{ old('masa') }}" autocomplete="off">
                            </div>
                            @if($errors->has('masa'))
                            <div class="small text-danger">{{ $errors->first('masa') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Akhir Bekerja</label>
                        <div class="col-lg-10 col-md-9">
                            <div class="input-group input-group-sm">
                                <input type="text" name="end_date" class="form-control form-control-sm {{ $errors->has('end_date') ? 'border-danger' : '' }}" value="{{ old('end_date') }}" autocomplete="off">
                                <span class="input-group-text"><i class="bi-calendar2"></i></span>
                            </div>
                            <div class="small text-muted">Kosongi saja jika masih aktif bekerja.</div>
                            @if($errors->has('end_date'))
                            <div class="small text-danger">{{ $errors->first('end_date') }}</div>
                            @endif
                        </div>
                    </div>
                    <hr>
                    @endif
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Email <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <input type="email" name="email" class="form-control form-control-sm {{ $errors->has('email') ? 'border-danger' : '' }}" value="{{ old('email') }}">
                            @if($errors->has('email'))
                            <div class="small text-danger">{{ $errors->first('email') }}</div>
                            @endif
                        </div>
                    </div>
                    @if(Request::query('role') == 'member')
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">No. HP <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <input type="text" name="phone_number" class="form-control form-control-sm {{ $errors->has('phone_number') ? 'border-danger' : '' }}" value="{{ old('phone_number') }}">
                            @if($errors->has('phone_number'))
                            <div class="small text-danger">{{ $errors->first('phone_number') }}</div>
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Username <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <input type="text" name="username" class="form-control form-control-sm {{ $errors->has('username') ? 'border-danger' : '' }}" value="{{ old('username') }}">
                            @if($errors->has('username'))
                            <div class="small text-danger">{{ $errors->first('username') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Password <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <div class="input-group">
                                <input type="password" name="password" class="form-control form-control-sm {{ $errors->has('password') ? 'border-danger' : '' }}">
                                <button type="button" class="btn btn-sm {{ $errors->has('password') ? 'btn-outline-danger' : 'btn-outline-secondary' }} btn-toggle-password"><i class="bi-eye"></i></button>
                            </div>
                            @if($errors->has('password'))
                            <div class="small text-danger">{{ $errors->first('password') }}</div>
                            @endif
                        </div>
                    </div>
                    <hr>
					<div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Catatan</label>
                        <div class="col-lg-10 col-md-9">
                            <textarea name="note" class="form-control form-control-sm {{ $errors->has('note') ? 'border-danger' : '' }}" rows="3">{{ old('note') }}</textarea>
                            @if($errors->has('note'))
                            <div class="small text-danger">{{ $errors->first('note') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                    <div class="row">
                        <div class="col-lg-2 col-md-3"></div>
                        <div class="col-lg-10 col-md-9">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi-save me-1"></i> Submit</button>
                            <a href="{{ route('admin.user.index', ['role' => Request::query('role')]) }}" class="btn btn-sm btn-secondary"><i class="bi-arrow-left me-1"></i> Kembali</a>
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
    Spandiv.DatePicker("input[name=birthdate]");
    Spandiv.DatePicker("input[name=start_date]");
    Spandiv.DatePicker("input[name=start_date_kontrak]");
    Spandiv.DatePicker("input[name=end_date]");

    // Select2
    Spandiv.Select2("#offices");

        $(document).on("change", "#position" ,function() {
            position = $('#position').val();
            office = $('#office').val();
            group = $('#group').val();
            $.ajax({
                type: "get",
                url: "{{ route('api.work-hour.indexApi') }}",
                data : {position: position, office: office, group: group},
                success: function(result){
                    var html = '<option value="" disabled>--Pilih--</option>';
                    $(result).each(function(key,value){
                        html += '<option value="' + value.id + '">' + value.name + '</option>';
                    });
                    if(position == 4){
                        var htmlm = '<option value="" disabled>--Pilih--</option>';
                        $("#wh_tugas").html(htmlm).attr("disabled","disabled");
                    }else{
                        $("#wh_tugas").html(html).removeAttr("disabled");
                    }
                }
            });
        });

    // Change Group
    $(document).on("change", "#group", function() {
        var group = $(this).val();
        $.ajax({
            type: "get",
            url: "{{ route('api.office.index') }}",
            data: {group: group},
            success: function(result){
                var html = '<option value="" disabled>--Pilih--</option>';
                $(result).each(function(key,value){
                    html += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $("#office").html(html).removeAttr("disabled");
                $("#offices").html(html).removeAttr("disabled");
            }
        });
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
</script>

@endsection