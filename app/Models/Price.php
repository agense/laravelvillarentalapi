<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['villa_id', 'date', 'price'];

    /**
     * The attributes that are hidden
     * @var array
     */
    protected $hidden = ['id', 'villa_id'];

    /**
     * Disable Timestamps
     */
    public $timestamps = false;

    //RELATIONSHIPS
    /**
     * Relationship with Villa Model
     */
    public function villa()
    {
        return $this->belongsTo('App\Models\Villa');
    }
}
