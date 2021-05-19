<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;
    /**
     * Disable Timestamps
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['villa_id', 'date', 'availability'];

    /**
     * The attributes that are hidden
     * @var array
     */
    protected $hidden = ['id', 'villa_id'];

    //RELATIONSHIPS
    /**
     * Relationship with Villa Model
     */
    public function villa()
    {
        return $this->belongsTo('App\Models\Villa');
    }

}
