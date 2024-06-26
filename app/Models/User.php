<?php

namespace App\Models;

use App\Models\Leave;
use App\Models\Divisi;
use App\Models\Lembur;
use App\Models\Office;
use App\Models\ReportDaily;
use App\Models\JabatanAttribute;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends \Ajifatur\FaturHelper\Models\User
{
    // use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'role_id', 'group_id', 'office_id', 'position_id', 'username', 'email', 'password', 'birthdate', 'gender', 'phone_number', 'address', 'latest_education', 'identity_number', 'start_date', 'end_date', 'status', 'last_visit',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function jabatanAttribute()
    {
        return $this->hasMany(JabatanAttribute::class);
    }
    
    public function reportDaily()
    {
        return $this->hasMany(ReportDaily::class);
    }

    public function kontrak()
    {
        return $this->hasOne(Kontrak::class);
    }

    public function lembur()
    {
        return $this->hasMany(Lembur::class,'user_id');
    }
    public function attendance()
    {
        return $this->hasMany(Attendance::class,'user_id');
    }
    /**
     * Get the group that owns the user.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
    
    public function leave(){
        return $this->hasMany(Leave::class, 'user_id');
    }
    /**
     * Get the office that owns the user.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
    
    /**
     * Get the position that owns the user.
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Get the indicators for the user.
     */
    public function indicators()
    {
        return $this->hasMany(\App\Models\UserIndicator::class);
    }

    /**
     * Get the late funds for the user.
     */
    public function late_funds()
    {
        return $this->hasMany(\App\Models\UserLateFund::class);
    }

    /**
     * Get the debt funds for the user.
     */
    public function debt_funds()
    {
        return $this->hasMany(\App\Models\UserDebtFund::class);
    }

    /**
     * Get the certifications for the user.
     */
    public function certifications()
    {
        return $this->hasMany(\App\Models\UserCertification::class);
    }

    /**
     * The offices that belong to the manager.
     */
    public function managed_offices()
    {
        return $this->belongsToMany(Office::class, 'user__office', 'user_id', 'office_id');
    }
}
