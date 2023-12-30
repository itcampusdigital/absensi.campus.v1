<table>
    <thead style="background-color: green; color: skyblue; border: 3px solid #ee00ee">
    <tr>
        <th><b>No</b></th>
        <th><b>NIK</b></th>
        <th><b>Nama</b></th>
        <th><b>Email</b></th>
        <th><b>Nomor Hp</b></th>
        <th><b>Mulai Bergabung</b></th>
        <th><b>Kantor</b></th>
        <th><b>Jabatan</b></th>

    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $user->identity_number }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->phone_number }}</td>
            <td>{{ $user->start_date }}</td>
            <td>{{ $user->office ? $user->office->name : '-'  }}</td>
            <td>{{ $user->position ? $user->position->name : '-' }}</td>

        </tr>
    @endforeach
    </tbody>
</table>