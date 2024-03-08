<table>
    <thead style="background-color: green; color: skyblue; border: 3px solid #ee00ee">
    <tr>
        <th><b>No</b></th>
        <th>Tanggal</th>
        <th>Karyawan</th>
        <th >Jam Kerja</th>
        <th>Absen Masuk</th>
        <th>Absen Keluar</th>

    </tr>
    </thead>
    <tbody>
    @foreach($attendances as $attendance)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <span class="d-none">{{ date('Y-m-d', strtotime($attendance->entry_at)).' '.$attendance->start_at }}</span>
                {{ date('d/m/Y', strtotime($attendance->date)) }}
            </td>
            <td>
                {{ $attendance->user->name }}
                <br>
                <small>( {{ $attendance->user->group->name }} )</small> 
            </td>
            <td>
                {{ $attendance->workhour ? $attendance->workhour->name : '-' }}
                <br>
                <small class="text-muted">{{ date('H:i', strtotime($attendance->start_at)) }} - {{ date('H:i', strtotime($attendance->end_at)) }}</small>
            </td>
            <td>
                @php $date = $attendance->start_at <= $attendance->end_at ? $attendance->date : date('Y-m-d', strtotime('-1 day', strtotime($attendance->date))); @endphp
                <i class="bi-alarm me-1"></i> {{ date('H:i', strtotime($attendance->entry_at)) }} WIB
                <br>
                <span class="text-muted"><i class="bi-calendar2 me-1"></i> {{ date('d/m/Y', strtotime($attendance->entry_at)) }}</span>
                @if(strtotime($attendance->entry_at) < strtotime($date.' '.$attendance->start_at) + 60)
                    <br>
                    <span class="text-success"><i class="bi-check-square me-1"></i> Masuk sesuai dengan waktunya.</span>
                @else
                    <br>
                    <span class="text-danger"><i class="bi-exclamation-triangle me-1"></i> Terlambat {{ time_to_string(abs(strtotime($date.' '.$attendance->start_at) - strtotime($attendance->entry_at))) }}.</span>
                @endif
                @if($attendance->late != '')
                <br>
                <span class="text-danger"><i class="bi-pencil me-1"></i> Terlambat karena {{ $attendance->late }}.</span>
                @endif
            </td>
            <td>
                @if($attendance->exit_at != null)
                    <i class="bi-alarm me-1"></i> {{ date('H:i', strtotime($attendance->exit_at)) }} WIB
                    <br>
                    <span class="text-muted"><i class="bi-calendar2 me-1"></i> {{ date('d/m/Y', strtotime($attendance->exit_at)) }}</span>
                    @php $attendance->end_at = $attendance->end_at == '00:00:00' ? '23:59:59' : $attendance->end_at @endphp
                    @if(strtotime($attendance->exit_at) > strtotime($attendance->date.' '.$attendance->end_at))
                        <br>
                        <span class="text-success"><i class="bi-check-square me-1"></i> Keluar sesuai dengan waktunya.</span>
                    @else
                        <br>
                        <span class="text-danger"><i class="bi-exclamation-triangle me-1"></i> Keluar lebih awal {{ time_to_string(abs(strtotime($attendance->exit_at) - strtotime($attendance->date.' '.$attendance->end_at))) }}.</span>
                    @endif
                @else
                    <span class="text-info"><i class="bi-question-circle me-1"></i> Belum melakukan absen keluar.</span>
                @endif
            </td>


        </tr>
    @endforeach
    </tbody>
</table>