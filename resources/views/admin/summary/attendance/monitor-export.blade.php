<table>
    <thead style="background-color: green; color: skyblue; border: 3px solid #ee00ee">
    <tr>
        <th style="text-align:center"><b>No</b></th>
        <th><b>Tanggal</b></th>
        <th><b>Nama</b></th>
        <th><b>Posisi</b></th>
        <th><b>Jam Masuk</b></th>
        <th><b>Keterlambatan</b></th>
    </tr>
    </thead>
    <tbody>
        @foreach ($monitoring as $monitorings)
            <tr>
                <td width="8" style="text-align:center">{{ $loop->iteration }}</td>
                <td>{{ $monitorings->date }}</td>
                <td>{{ $monitorings->user->name }}</td>
                <td>{{ $monitorings->workhour->name }}</td>
                <td>{{ date('H:i:s', strtotime($monitorings->entry_at)) }}</td>
                @if($monitorings->late_time == 0)
                    <td>-</td>
                @else
                    <td>{{ $monitorings->late_time }} Menit</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>