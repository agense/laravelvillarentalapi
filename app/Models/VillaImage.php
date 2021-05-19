<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class VillaImage extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['image', 'villa_id'];
   
    /**
     * Disable Timestamps
     */
    public $timestamps = false;
    
    /**
     * Custom Attributes
     */
    private static $UPLOAD_PATH = 'images/villas/';

    // MODEL METHODS
    public static function get_upload_path(){
        return self::$UPLOAD_PATH;
    }
}
