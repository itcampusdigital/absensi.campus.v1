<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Office;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class UsersImport implements ToModel, WithStartRow
{
    // use SkipsErrors, SkipsFailures;,SkipsOnError, SkipsOnFailure

    public function startRow(): int
    {
        return 2;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

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
        
    public function model(array $row)
    {
        $office_id = Office::select('id','name')->where('name','LIKE','%'.$row[1].'%')->first();
        $position_id = Position::select('id','name')->where('name','LIKE','%'.$row[2].'%')->first();
        $password = $row[17] != null ? $row[17] : 123456;
        //generated username
        $username = $row[16] != null ? $row[16] : strstr($row[14], '@', true);
        $cek_username = User::select('username')->where('username',$username)->first();
        $cek_email = User::select('email')->where('email',$row[14])->first();
        
        // dd($username);
        
        if($cek_username != null || $cek_email != null){
            
            return abort(400,'Username atau Email pada nama "'.$row[4].'" Sudah Terdaftar');
        }
        else{
            return new User([
                'role_id' => 3,
                'group_id' => Auth::user()->group_id,
                'office_id' => $office_id->id,
                'position_id' => $position_id->id,
                'name' => $row[4],
                'username' => $row[16],
                'email' => $row[14],
                'password' => bcrypt($password),
                'remember_token' => null,
                'avatar' => null,
                'access_token' => null,
                'birthdate' => $row[5],
                'gender' => $row[6],
                'phone_number' => $row[15],
                'address' => $row[7],
                'lates_education'=>$row[8],
                'identify_number'=> $row[9],
                'start_date'=> $row[10],
                'end_date'=>$row[13],
                'status'=> 1,
                'created_at' => date('Y-m-d H:i:s'),
                'note'=>$row[18],
            ]);
        }

    }
}
