<table style="border: 1px solid;">
    <thead style="background-color: green; color: skyblue; border: 3px solid #ee00ee">
        <tr>
            <th rowspan="2" style="text-align:center"><b>No</b></th>
            <th rowspan="2" style="text-align:center"><b>Tanggal</b></th>
            @if($monitoring[0]->count_wh > 0)
            <th style="text-align:center" width="30" colspan="{{ $monitoring[0]->count_wh }}"><b>Jam Kerja</b></th>
            @endif
 
        </tr>
        @if(count($workhours) > 0)
        <tr>
            @foreach($workhours as $wh_name)
                <th style="text-align:center"><b>{{ $wh_name }}</b></th>
            @endforeach
        </tr>
        @endif
    </thead>
    <tbody>

        @foreach ($monitoring as $datess)
        <tr>
                <td style="text-align:center">{{ $loop->iteration }}</td>
                <td style="text-align:center">{{ $datess->date }}</td>
                {{-- @foreach ($workhours as $wh) --}}
                    <td style="text-align:center">{{ $datess->workhour->name == 'Shift 1' ? $datess->user->name : '-' }}</td>         
                    <td style="text-align:center">{{ $datess->workhour->name == 'Shift 2' ? $datess->user->name : '-' }}</td>
                    <td style="text-align:center">{{ $datess->workhour->name == 'Shift 3' ? $datess->user->name : '-' }}</td>
                    <td style="text-align:center">{{ $datess->workhour->name == 'Sisipan 3' ? $datess->user->name : '-' }}</td>
                {{-- @endforeach --}}
        </tr>

        @endforeach
        {{-- @foreach ($monitoring as $datess)
        <tr>
            <td style="text-align:center">{{ $loop->iteration }}</td>
            <td style="text-align:center">{{ $datess->date }}</td>
            
            @foreach ($shift1 as $s1)
                @if ($s1->date == $datess->date)
                <td style="text-align:center">{{ $s1->user->name }}</td>
                @else
                <td>-</td>
                @endif
            @endforeach


        </tr>
    @endforeach --}}
    </tbody>
</table>
