<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['name', 'type'];

    /**
     * The attributes that are hidden
     * @var array
     */
    protected $hidden = ['pivot'];

    /**
     * Disable Timestamps
     */
    public $timestamps = false;
    
    /**
     * Custom attributes
     */
    private static $types = ['indoor', 'outdoor'];

   // MODEL GETTERS
    public static function get_types(){
        return self::$types;
    }

    // RELATIONS
    /**
     * Relationship with Facilities Model - Many to Many
    */
    public function villas(){
        return $this->belongsToMany('App\Models\Villa');
     }

     //MODEL METHODS
     public static function group_by_type($facilities){
        return $facilities->groupBy('type')->map(function($item, $key){
            return $item->map(function($innerItem, $innerKey){
               unset($innerItem['type']);
               return $innerItem;
            });
        });
        
     }
}
