<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Region extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['name', 'slug'];

    /**
     * The relationships that should always be loaded.
     * @var array
     */
    protected $withCount = ['cities', 'villas'];

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
     * Relationship with City Model
    */
    public function cities(){
        return $this->hasMany('App\Models\City');
    }
    /**
     * Relationship with Villa Model
    */
    public function villas(){
        return $this->hasManyThrough('App\Models\Villa', 'App\Models\City');
    }

    // MODEL METHODS
    /**
     * Return regions ordered by name
     */
    public static function list(){
        return self::orderBy('name')->get();
    } 
}
