<?php

namespace App\Models;

use App\Models\Group;
use App\Models\Office;
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
    protected $fillable = ['name', 'group_id', 'office_id'];
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
}
