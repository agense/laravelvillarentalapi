<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class City extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['name', 'slug', 'region_id'];

    /**
     * The relationships that should always be loaded.
     * @var array
     */
    protected $with = ['region'];

    protected $withCount = ['villas'];

    /**
     * Disable Timestamps
     */
    public $timestamps = false;

    //SETTERS
    /**
     * Set the slug from name.
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst($value);
        $this->attributes['slug'] = Str::slug($value, '-');
    }

    // RELATIONS
    /**
     * Relationship with Region Model
    */
    public function region(){
        return $this->belongsTo('App\Models\Region');
    }
    /**
     * Relationship with Villa Model
    */
    public function villas(){
        return $this->hasMany('App\Models\Villa');
    }

    // MODEL METHODS
    /**
     * Return cities ordered by name
     */
    public static function list(){
        return self::orderBy('name')->get();
    } 
}

