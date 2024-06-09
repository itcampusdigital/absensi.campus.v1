<?php

namespace App\Models;

use App\Models\WorkHourTugas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkHour extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work_hours';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'category', 'quota', 'start_at', 'end_at'];
    
    public function wokhourtugas()
    {
        return $this->hasMany(WorkHourTugas::class, 'work_hour_id');
    }
    /**
     * Get the group that owns the work hour.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Get the office that owns the work hour.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    /**
     * Get the position that owns the work hour.
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'workhour_id');
    }
}
