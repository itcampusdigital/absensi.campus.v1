<?php

namespace App\Models;

use App\Models\Divisi;
use App\Models\Position;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JabatanAttribute extends Model
{
    use HasFactory;

    protected $table = 'jabatan_attributes';
    protected $guarded = ['id'];

    public function divisi(){
        return $this->belongsTo(Divisi::class, 'division_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
