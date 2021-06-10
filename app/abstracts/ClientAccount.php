<?php

namespace App\Abstracts;

use Illuminate\Database\Eloquent\Model;

abstract class ClientAccount extends Model{
    
    //Available Account Types
    private const DISTRIBUTOR = 1;
    private const SUPPLIER = 2;

    /**
     * Get account type as string
     * @param  string  $value
     * @return string
     */
    public function getAccountTypeAttribute($value)
    {
        return self::stringifyType($value);
    }

    /**
     * Set account type as constant value
     * @param  string  $value
     * @return void
     */
    public function setAccountTypeAttribute($value)
    {
        if(!intval($value)){
            $this->attributes['account_type'] = self::getTypeValue($value);
        }else{
            $this->attributes['account_type'] = $value;
        }
    }

    /**
     * Returns all current class constants, i.e. account types
     * @return Array
     */
    private static function getTypes(){
        $reflector = (new \ReflectionClass(__CLASS__));
        return array_diff($reflector->getConstants(),$reflector->getParentClass()->getConstants());
    }

    /**
     * Returns only the names of all account types
     * @return Array
     */
    public static function getAccountTypes() {
        return array_keys(self::getTypes());
    }

    /**
     * Sets account type on current model instance
     * @param String $type 
     * @return void
     */
    public function setType(String $type){
        $type = strtoupper($type);
        if( !self::isValidType($type) ){
            throw new \Exception('Incorrect Account Type');
        }
        $this->account_type = constant('self::'. $type);
    }

    /**
     * Return the account type associated with current model as string
     * @return String
     */
    public function getType(){
        return ucfirst(array_search($this->account_type, self::getTypes()));
    }

    /**
     * Returns a numeric value of a contstant for specific account type
     * @param String $type
     * @return Int  
     */
    public static function getTypeValue(String $type){
        $type = strtoupper($type);
        if( !self::isValidType($type) ){
            throw new \Exception('Incorrect Account Type');
        }
        return constant('self::'. $type);
    }

    /**
     * Returns a string value of a contstant for specific account type
     * @param String $type
     * @return Int  
     */
    public static function stringifyType(Int $type){
        return ucfirst(array_search($type, self::getTypes()));
    }

    /**
     * Checks if an account type is valid
     * @param String $type
     * @return Bool
     */
    public static function isValidType(String $type){
        return array_key_exists(trim(strtoupper($type)), self::getTypes());
    }
}