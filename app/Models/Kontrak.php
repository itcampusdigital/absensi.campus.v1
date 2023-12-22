<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontrak extends Model
{
    use HasFactory;

    protected $table = 'kontrak';
    protected $fillable = ['user_id', 'start_date_kontrak','masa','cuti'];

    public $timestamps = false;
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
