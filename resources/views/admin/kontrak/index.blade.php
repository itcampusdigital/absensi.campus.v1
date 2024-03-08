@extends('faturhelper::layouts/admin/main')

@section('title', 'Kelola Masa Kontrak')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-sm-flex justify-content-center align-items-center">
                    <form id="form-filter" class="d-lg-flex" method="get" action="">

                    @if (Auth::user()->role_id == role('super-admin'))
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select id="group" name="group" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Perusahaan">
                            <option value="0">--Pilih Perusahaan--</option>
                            @foreach ($groups as $group)
                            <option value="{{ $group->id }}" {{ Request::query('group') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select id="office" name="office" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Kantor">
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
                    {{-- <div class="ms-lg-2 ms-0 mb-lg-0 mb-2">
                        <select id="hari" name="hari" class="form-select form-select-sm" data-bs-toggle="tooltip" title="Pilih Hari">
                            <option value="0" disabled selected>--Pilih Hari--</option>
                            <option value="1">Dibawah 20 hari</option>
                            <option value="2">21-30 Hari</option>
                            <option value="3">Diatas 30 hari</option>
                        </select>
                    </div> --}}
                    <div class="ms-lg-2 ms-0">
                        <button type="submit" class="btn btn-sm btn-info" {{ Request::query('office') != null ? '' : 'disabled' }}><i class="bi-filter-square me-1"></i> Filter</button>
                    </div>
                </form>
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
                                    {{-- <th width="20"><input type="checkbox" class="form-check-input checkbox-all"></th> --}}
                                    <th>Nama</th>
                                    <th width="200">Kantor</th>
                                    <th width="150">Tanggal Bergabung</th>
                                    <th width="150">Tanggal Kontrak</th>
                                    <th width="150">Masa Kontrak</th>
                                    <th width="150">Akhir Kontrak</th>
                                    <th width="150">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- @endif --}}
            </div>
        </div>
    </div>
    <form class="form-delete d-none" method="post" action="{{ route('admin.kontrak.destroy') }}">
        @csrf
        <input type="hidden" name="id">
    </form>

@endsection

@section('js')
    <script type="text/javascript">
    // DataTable
    // Spandiv.DataTable("#datatable");
    $(document).ready(function(){
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                'url' : '{{ route('admin.kontrak.getKontrak') }}',
                'data' : function(d){
                    d.office_select = $('#office').val();
                    d.hari_select = $('#hari').val();
                }

            },
            // order: [6,'asc'],
            columns: [
                // {data: 'checkbox', name: 'checkbox', className: 'text-center', orderable: false},
                {data: 'user.name', name: 'user.name'},
                {data: 'user.office_id', name: 'user.office_id'},
                {data: 'user.start_date', className: 'text-center', name: 'user.start_date'},
                {data: 'start_date_kontrak', className: 'text-center', name: 'start_date_kontrak'},
                {data: 'masa', name: 'masa'},
                {data: 'end_date_kontrak', className: 'text-center', name: 'end_date_kontrak'},
                {data: 'action', name: 'action', className: 'text-center', orderable: false},
            ]
        });

        $('#office').change(function(){
            reloadTable('#datatable');
        });
    })

    
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
