<table>
    <thead style="background-color: green; color: skyblue; border: 3px solid #ee00ee">
    <tr>
        <th>No</th>
        <th>Karyawan</th>
        <th  class="{{ Request::query('office') != '' && Request::query('office') != 0 ? 'd-none' : '' }}">Kantor</th>
        <th >Posisi Jabatan</th>
        <th >Hadir</th>
        <th >Terlambat</th>
        <th >Sakit</th>
        <th >Izin</th>
        <th >Cuti</th>

    </tr>
    </thead>
    <tbody>
        @foreach($users as $key=>$user)
            @foreach($user->workhours as $workhour)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ $user->name }}
                        @if($user->position)
                        <br>
                        <small class="text-muted">{{ $user->position->name }}</small>
                        @endif

                    </td>
                    <td class="{{ Request::query('office') != '' && Request::query('office') != 0 ? 'd-none' : '' }}">
                        @if($user->office)
                            {{ $user->office->name }}
                        @endif
                    </td>
                    <td>{{ $workhour->name }}</td>
                    <td align="right">
                        {{ number_format($workhour->present,0,',',',') }}
                    </td>
                    <td align="right">
                        {{ number_format($workhour->late,0,',',',') }}
                    </td>
                    <td align="right">
                        {{ number_format($user->absent1,0,',',',') }}
                    </td>
                    <td align="right">
                        {{ number_format($user->absent2,0,',',',') }}
                    </td>
                    <td align="right">
                        {{ number_format($user->leave,0,',',',') }}
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>