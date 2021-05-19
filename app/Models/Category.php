<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['name', 'slug'];

    /**
     * The attributes that are hidden
     * @var array
     */
    protected $hidden = ['pivot'];

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
     * Relationship with Villas Model - Many to Many
    */
    public function villas(){
        return $this->belongsToMany('App\Models\Villa');
     }
}
