@extends('faturhelper::layouts/admin/main')

@section('title', 'Edit Jabatan: ')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Edit Divisi</h1>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('admin.divisi.update',['id' => $wh_tugas->id]) }}" enctype="multipart/form-data">
                    @csrf
                
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Nama <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <input type="text" name="name" class="form-control form-control-sm {{ $errors->has('name') ? 'border-danger' : '' }}" value="{{ $wh_tugas->name }}" autofocus>
                            @if($errors->has('name'))
                            <div class="small text-danger">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                    </div>
                    @if(Auth::user()->role_id == role('super-admin'))
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Perusahaan <span class="text-danger">*</span></label>
                        <div class="col-lg-10 col-md-9">
                            <select name="group_id" class="form-select form-select-sm {{ $errors->has('group_id') ? 'border-danger' : '' }}">
                                <option value="" disabled selected>--Pilih--</option>
                                @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('group_id'))
                            <div class="small text-danger">{{ $errors->first('group_id') }}</div>
                            @endif
                        </div>
                    </div>
                    @endif
                    <hr>
                    <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Tugas</label>
                        <div class="col-lg-10 col-md-9">
                            <table class="table table-sm table-bordered table-responsive" id="table-dr">
                                <tbody>
                                    @for($key=0; $key < $count_tugas; $key++)
                                    <tr data-id="{{ $key }}">
                                        <td>
                                            <textarea placeholder="Tugas" name="dr_names[]" class="form-control form-control-sm" rows="2" cols="140">{{ $tugas != null ? $tugas->tugas[$key] : '' }}</textarea>
                                        </td>
                                        <td>
                                            @if(empty($tugas->tipe[$key]))
                                                <select name="tipe[]" class="form-select form-select-sm" id="tipe">
                                                    <option value="1">Harian</option>
                                                    <option value="2">Mingguan</option>
                                                    <option value="3">Bulanan</option>
                                                </select>    
                                            @else
                                                <select name="tipe[]" class="form-select form-select-sm" id="tipe">
                                                    <option value="1" {{ $tugas->tipe[$key] == 1 ? 'selected' : '' }}>Harian</option>
                                                    <option value="2" {{ $tugas->tipe[$key] == 2 ? 'selected' : '' }}>Mingguan</option>
                                                    <option value="3" {{ $tugas->tipe[$key] == 3 ? 'selected' : '' }}>Bulanan</option>
                                                </select>
                                            @endif
                                            <input size="5" placeholder="Target" type="text" name="target[]" value="{{ $tugas != null ? $tugas->target[$key] : null }}" class="form-control form-control-sm" >
                                        </td>
                                        <td width="80" align="center">
                                            {{-- <input type="hidden" name="dr_ids[]" value="#"> --}}
                                            <div class="btn-group">
                                                <a href="#" class="btn btn-success btn-sm btn-add-row" data-id="{{ $key }}" data-bs-toggle="tooltip" title="Tambah"><i class="bi-plus"></i></a>
                                                <a href="#" class="btn btn-danger btn-sm btn-delete-row" data-id="{{ $key }}" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- <div class="row mb-3">
                        <label class="col-lg-2 col-md-3 col-form-label">Wewenang</label>
                        <div class="col-lg-10 col-md-9">
                            <table class="table table-sm table-bordered" id="table-a">
                                <tbody>
                                    @for($key=0;$key<$count_wewenang;$key++)
                                    <tr data-id="{{ $key }}">
                                        <td>
                                            <textarea name="a_names[]" class="form-control form-control-sm" rows="2">{{ $wewenang != null ? $wewenang[$key] : null }}</textarea>
                                        </td>
                                        <td width="80" align="center">
                                            
                                            <div class="btn-group">
                                                <a href="#" class="btn btn-success btn-sm btn-add-row" data-id="{{ $key }}" data-bs-toggle="tooltip" title="Tambah"><i class="bi-plus"></i></a>
                                                <a href="#" class="btn btn-danger btn-sm btn-delete-row" data-id="{{ $key }}" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div> --}}
                    <hr>
                    <div class="row">
                        <div class="col-lg-2 col-md-3"></div>
                        <div class="col-lg-10 col-md-9">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi-save me-1"></i> Submit</button>
                            <a href="{{ route('admin.divisi.index') }}" class="btn btn-sm btn-secondary"><i class="bi-arrow-left me-1"></i> Kembali</a>
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
    // Load
    $(window).on("load", function() {
        var length = $("#table-dr tbody tr").length;
        if(length == 0) $("#table-dr tbody").append(starter_html());
        var length_a = $("#table-a tbody tr").length;
        if(length_a == 0) $("#table-a tbody").append(starter_html_a());
        Spandiv.Tooltip();
    })

    // Button Add Row
    $(document).on("click", ".btn-add-row", function(e) {
        e.preventDefault();
        var id = $(this).parents(".table").attr("id");
        if(id == "table-dr") $(this).parents(".table").find("tbody").append(starter_html());
        else if(id == "table-a") $(this).parents(".table").find("tbody").append(starter_html_a());
        recreate();
    });

    // Button Delete Row
    $(document).on("click", ".btn-delete-row", function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var length = $(this).parents(".table").find("tbody tr").length;
        var ask = confirm("Anda yakin ingin menghapus data ini?");
        if(ask) {
            if(length > 1) $(this).parents(".table").find("tbody tr[data-id="+id+"]").remove();
            else {
                $(this).parents(".table").find("tbody tr[data-id="+id+"]").find("input, textarea").val(null);
            }
            recreate();
        }
    });

    // Starter HTML
    function starter_html() {
        var html = '';
        html += '<tr data-id="0">';
        html += '<td>';
        html += '<textarea name="dr_names[]" class="form-control form-control-sm" rows="2" cols="140"></textarea>';
        html += '</td>';
        html += '<td>';
        html += '<select name="tipe[]" class="form-select form-select-sm" id="tipe">';
        html += '<option value="1">Harian</option>';
        html += '<option value="2">Mingguan</option>';
        html += '<option value="3">Bulanan</option>';
        html += '</select>';
        html += '</td>';
        html += '<td>';
        html += '<input size="5" placeholder="Target" type="text" name="target[]"  class="form-control form-control-sm">';
        html += '</td>';
        html += '<td width="80" align="center">';
        html += '<input type="hidden" name="dr_ids[]">';
        html += '<div class="btn-group">';
        html += '<a href="#" class="btn btn-success btn-sm btn-add-row" data-id="0" data-bs-toggle="tooltip" title="Tambah"><i class="bi-plus"></i></a>';
        html += '<a href="#" class="btn btn-danger btn-sm btn-delete-row" data-id="0" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';
        return html;
    }
    function starter_html_a() {
        var html = '';
        html += '<tr data-id="0">';
        html += '<td>';
        html += '<textarea name="a_names[]" class="form-control form-control-sm" rows="2"></textarea>';
        html += '</td>';
        html += '<td width="80" align="center">';
        html += '<div class="btn-group">';
        html += '<a href="#" class="btn btn-success btn-sm btn-add-row" data-id="0" data-bs-toggle="tooltip" title="Tambah"><i class="bi-plus"></i></a>';
        html += '<a href="#" class="btn btn-danger btn-sm btn-delete-row" data-id="0" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';
        return html;
    }

    // Recreate data-id
    function recreate() {
        $(".table tbody tr").each(function(key,elem) {
            $(elem).attr("data-id",key);
            $(elem).find(".btn-add-row").attr("data-id",key);
            $(elem).find(".btn-delete-row").attr("data-id",key);
        });
        Spandiv.Tooltip();
    }
</script>

@endsection