@extends('faturhelper::layouts/admin/main')

@section('title', 'Kelola Cuti')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-sm-flex justify-content-center align-items-center">
                    {{-- <form id="form-filter" class="d-lg-flex" method="get" action="">
                    <div class="mb-lg-0 mb-2">
                        <select name="year" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Periode Tahun">
                            @for ($i = 2023; $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    @if (Auth::user()->role_id == role('super-admin'))
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="group" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Perusahaan">
                            <option value="0">--Pilih Perusahaan--</option>
                            @foreach ($groups as $group)
                            <option value="{{ $group->id }}" {{ Request::query('group') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select name="office" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Kantor">
                            <option value="0" disabled selected>--Pilih Kantor--</option>
                            @if (Auth::user()->role_id == role('super-admin'))
                                @if (Request::query('group') != 0)
                                    @foreach (\App\Models\Group::find(Request::query('group'))->offices()->orderBy('is_main', 'desc')->orderBy('name', 'asc')->get() as $office)
                                    <option value="{{ $office->id }}" {{ Request::query('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                    @endforeach
                                @endif
                            @elseif(Auth::user()->role_id == role('admin'))
                                @foreach (\App\Models\Group::find(Auth::user()->group_id)->offices()->orderBy('is_main', 'desc')->orderBy('name', 'asc')->get() as $office)
                                <option value="{{ $office->id }}" {{ Request::query('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @elseif(Auth::user()->role_id == role('manager'))
                                @foreach (Auth::user()->managed_offices()->orderBy('is_main', 'desc')->orderBy('name', 'asc')->get() as $office)
                                <option value="{{ $office->id }}" {{ Request::query('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="ms-lg-2 ms-0">
                        <button type="submit" class="btn btn-sm btn-info" {{ Request::query('office') != null ? '' : 'disabled' }}><i class="bi-filter-square me-1"></i> Filter</button>
                    </div>
                </form> --}}
                </div>
                <hr class="my-0">
                {{-- @if (Request::query('office') != null) --}}
                <div class="card-body">
                    @if (Session::get('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="alert-message">{{ Session::get('message') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="datatable">
                            <thead class="bg-light">
                                <tr>
                                    <th width="20"><input type="checkbox" class="form-check-input checkbox-all"></th>
                                    <th width="250">NIK</th>
                                    <th>Nama</th>
                                    <th width="150">Tanggal Bergabung</th>
                                    <th width="150">Tanggal Kontrak</th>
                                    <th width="150">Masa Kontrak</th>
                                    <th width="150">Akhir Kontrak</th>
                                    <th width="150">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user_kontrak)
                                    <tr >
                                        <td><input type="checkbox" class="form-check-input"></td>
                                        <td>{{ $user_kontrak->user->identity_number }}</td>
                                        <td>{{ $user_kontrak->user->name }}</td>
                                        <td style="text-align: center">{{ date('d/m/Y', strtotime($user_kontrak->user->start_date)) }}</td>
                                        @if($user_kontrak != null)
                                            <td style="text-align: center">{{ date('d/m/Y', strtotime($user_kontrak->start_date_kontrak)) }}</td>
                                        @else
                                            <td> </td>
                                        @endif
                                        <td style="text-align: center">{{ $user_kontrak->masa }}</td>
                                        @if($user_kontrak->end_date_kontrak != null)
                                            <td style="text-align: center">{{ date('d/m/Y', strtotime($user_kontrak->end_date_kontrak)) }}</td>
                                        @else
                                            <td> </td>
                                        @endif
                                        
                                        <td style="text-align: center">
                                            <a href="{{ route('admin.kontrak.edit', $user_kontrak->user_id) }}" type="button" class="btn btn-sm btn-warning"><i
                                                    class="bi-pencil"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- @endif --}}
            </div>
        </div>
    </div>
    <form class="form-delete d-none" method="post" action="#">
        @csrf
        <input type="hidden" name="id">
    </form>

@endsection

@section('js')
    <script type="text/javascript">
    // DataTable
    Spandiv.DataTable("#datatable");
    
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
