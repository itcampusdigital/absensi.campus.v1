<?php

namespace App\Models;

use App\Models\User;
use App\Models\Divisi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Position extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'positions';

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array
     */
    protected $fillable = ['name', 'work_hours'];

    /**
     * Get the users for the position.
     */
    public function divisi()
    {
        return $this->hasMany(Divisi::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the job duties & responsibilities for the position.
     */
    public function duties_and_responsibilities()
    {
        return $this->hasMany(\App\Models\JobDutyResponsibility::class);
    }

    /**
     * Get the job authorities for the position.
     */
    public function authorities()
    {
        return $this->hasMany(\App\Models\JobAuthority::class);
    }
    
    /**
     * Get the group that owns the office.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
