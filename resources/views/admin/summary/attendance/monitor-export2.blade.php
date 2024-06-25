<table class="table table-sm table-hover table-bordered" id="datatable">
        <thead class="bg-light">
            <tr>
                <th width="10" align="center" style="border: 1px solid black;background-color:gray"><b>No</b></th>
                <th width="40" style="border: 1px solid black;background-color:gray"><b>Nama</b></th>
                @foreach ($days as $day)
                    <th align="center" style="border: 1px solid black;background-color:gray">{{ $day }}</th>
                @endforeach
                <th align="center" style="border: 1px solid black;background-color:gray"><b>Hadir</b></th>
                <th align="center" style="border: 1px solid black;background-color:gray"><b>Sakit</b></th>
                <th align="center" style="border: 1px solid black;background-color:gray"><b>Izin</b></th>
                <th align="center" style="border: 1px solid black;background-color:gray"><b>Alpa</b></th>
            </tr>
        </thead>
        @if($ceks != null)
            <tbody>
                    @for ($i=0; $i < count($ceks); $i++)
                        <tr>
                            <td align="center" style="border: 1px solid black">{{ $i+1 }}</td>
                            <td style="border: 1px solid black">{{ $ceks[$i]['name'] }}</td>

                            @for($j=0;$j<count($dates_convert);$j++)
                                @if(array_key_exists($j,$ceks[$i]['date']))
                                    <td align="center" style="border: 1px solid black;color: green"><b>{{ $ceks[$i]['date'][$j] }}</b></td>
                                @elseif(array_key_exists($j,$ceks[$i]['izin']))
                                    <td align="center" style="border: 1px solid black;color: red"><b>{{ $ceks[$i]['izin'][$j] }}</b></td>
                                @elseif(array_key_exists($j,$ceks[$i]['sakit']))
                                    <td align="center" style="border: 1px solid black;color: blue"><b>{{ $ceks[$i]['sakit'][$j] }}</b></td>
                                @elseif(array_key_exists($j,$ceks[$i]['alpa']))
                                    <td align="center" style="border: 1px solid black"><b>{{ $ceks[$i]['alpa'][$j] }}</b></td>
                                @else
                                    <td align="center" style="border: 1px solid black;background-color: gray"></td>
                                @endif
                            @endfor
                            <td align="center" style="border: 1px solid black">{{ count($ceks[$i]['date']) }}</td>
                            <td align="center" style="border: 1px solid black">{{ count($ceks[$i]['sakit']) }}</td>
                            <td align="center" style="border: 1px solid black">{{ count($ceks[$i]['izin']) }}</td>
                            <td align="center" style="border: 1px solid black">{{ count($ceks[$i]['alpa']) }}</td>

                        </tr>
                    @endfor
            </tbody>
        @else
            <tbody>
            </tbody>
        @endif

</table>