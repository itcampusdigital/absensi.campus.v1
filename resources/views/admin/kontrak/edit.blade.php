@extends('faturhelper::layouts/admin/main')

@section('title', 'Edit ' . $user_select->name)

@section('content')

    <div class="d-sm-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Edit Data {{ $user_select->name }}</h1>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="{{ route('admin.kontrak.update') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $user_select->id }}">
                        <div class="row mb-3">
                            <label class="col-lg-2 col-md-3 col-form-label">Nama <span class="text-danger">*</span></label>
                            <div class="col-lg-10 col-md-9">
                                <input type="text" name="name" disabled
                                    class="form-control form-control-sm {{ $errors->has('name') ? 'border-danger' : '' }}"
                                    value="{{ $user_select->name }}">
                                @if ($errors->has('name'))
                                    <div class="small text-danger">{{ $errors->first('name') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-2 col-md-3 col-form-label">Tanggal Bergabung <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-10 col-md-9">
                                <div class="input-group input-group-sm">
                                    <input disabled type="text" name="start_date"
                                        class="form-control form-control-sm {{ $errors->has('start_date') ? 'border-danger' : '' }}"
                                        value="{{ date('d/m/Y', strtotime($user_select->start_date)) }}" autocomplete="off">
                                    <span class="input-group-text"><i class="bi-calendar2"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-2 col-md-3 col-form-label">Tanggal Kontrak <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-10 col-md-9">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="start_date_kontrak"
                                        class="form-control form-control-sm {{ $errors->has('start_date_kontrak') ? 'border-danger' : '' }}"
                                        value="{{ date('d/m/Y', strtotime($user_select->kontrak->start_date_kontrak)) }}"
                                        autocomplete="off">
                                    <span class="input-group-text"><i class="bi-calendar2"></i></span>
                                </div>
                                @if ($errors->has('start_date_kontrak'))
                                    <div class="small text-danger">{{ $errors->first('start_date_kontrak') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-2 col-md-3 col-form-label">Masa Kontrak <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-10 col-md-9">
                                <input type="text" name="masa"
                                    class="form-control form-control-sm {{ $errors->has('masa') ? 'border-danger' : '' }}"
                                    value="{{ $user_select->kontrak->masa }}">
                                @if ($errors->has('masa'))
                                    <div class="small text-danger">{{ $errors->first('masa') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-2 col-md-3"></div>
                            <div class="col-lg-10 col-md-9">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="bi-save me-1"></i>
                                    Submit</button>
                                <a href="{{ route('admin.kontrak.index') }}" class="btn btn-sm btn-secondary"><i
                                        class="bi-arrow-left me-1"></i> Kembali</a>
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
        Spandiv.DatePicker("input[name=start_date_kontrak]");
        Spandiv.DatePicker("input[name=start_date]");
        // Spandiv.DatePicker("input[name=end_date]");

        // Select2
        Spandiv.Select2("#offices");

        // Change Group
        $(document).on("change", "#group", function() {
            var group = $(this).val();
            $.ajax({
                type: "get",
                url: "{{ route('api.office.index') }}",
                data: {
                    group: group
                },
                success: function(result) {
                    var html = '<option value="" selected>--Pilih--</option>';
                    $(result).each(function(key, value) {
                        html += '<option value="' + value.id + '">' + value.name + '</option>';
                    });
                    $("#office").html(html).removeAttr("disabled");
                }
            });
            $.ajax({
                type: "get",
                url: "{{ route('api.position.index') }}",
                data: {
                    group: group
                },
                success: function(result) {
                    var html = '<option value="" selected>--Pilih--</option>';
                    $(result).each(function(key, value) {
                        html += '<option value="' + value.id + '">' + value.name + '</option>';
                    });
                    $("#position").html(html).removeAttr("disabled");
                }
            });
        });
    </script>

@endsection
