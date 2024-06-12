<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithStartRow;


class UsersImport implements WithStartRow
{

    public function startRow(): int
    {
        return 2;
    }

    // row[0] = nomor                   // row[10] = mulai bergabung
    // row[1] = kantor                  // row[11] = mulai kontrak           
    // row[2] = jabatan/position        // row[12] = masa kontrak          
    // row[3] = divisi                  // row[13] = akhir bekerja
    // row[4] = nama                    // row[14] = email         
    // row[5] = tanggal lahir           // row[15] = nomor_hp
    // row[6] = jenis kelamin           // row[16] = username    
    // row[7] = alamat                  // row[17] = password
    // row[8] = Pendidikan terakhir     // row[18] = catatan
    // row[9] = NIk
        
}
