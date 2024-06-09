<?php

namespace App\Models;

use App\Models\Group;
use App\Models\Office;
use App\Models\Position;
use App\Models\WorkHour;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Divisi extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'divisis';
    public $timestamps = false;
    /**
     * Fill the model with an array of attributes.
     *
     * @param  array
     */
    protected $fillable = ['name', 'position_id', 'tugas','wewenang'];
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
