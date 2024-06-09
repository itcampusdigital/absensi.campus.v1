<?php

namespace App\Models;

use App\Models\WorkHour;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkHourTugas extends Model
{
    use HasFactory;
    protected $table = 'work_hour_tugas';
    public $timestamp = false;
    protected $fillable = ['workhour_id','tugas','wewenang'];


    public function workhour()
    {
        return $this->belongsTo(WorkHour::class, 'workhour_id', 'id');
    }
}
