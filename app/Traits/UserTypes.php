<?php
namespace App\Traits;

trait UserTypes
{
    /**
     * Get user type as string
     * @param  string  $value
     * @return string
     */
    public function getTypeAttribute($value)
    {
        return self::typeToString($value);
    }

    /**
     * Set user type as constant value
     * @param  string  $value
     * @return void
     */
    public function setTypeAttribute($value)
    {
        if(!intval($value)){
            $this->attributes['type'] = self::typeToValue($value);
        }else{
            $this->attributes['type'] = $value;
        }
    }

     /**
     * Returns all current class constants, i.e. user types
     * @return Array
     */
    private static function userTypes(){
        $reflector = (new \ReflectionClass(__CLASS__));
        return array_diff($reflector->getConstants(),$reflector->getParentClass()->getConstants());
    }
    /**
     * Returns only the names of all user types
     * @return Array
     */
    public static function getUserTypes() {
        return array_keys(self::userTypes());
    }

    /**
     * Sets user type on current model instance
     * @param String $type 
     * @return void
     */
    public function setType(String $type){
        $type = strtoupper($type);
        if(!in_array($type, self::getUserTypes())){
            throw new \Exception('Incorrect User Type');
        }
        $this->type = constant('self::'. $type);
    }

    /**
     * Return the user type associated with current model as string
     * @return String
     */
    public function getType(){
        return ucfirst(array_search($this->type, self::userTypes()));
    }

     /**
     * Returns a numeric value of a constant for specific user type
     * @param String $type
     * @return Int  
     */
    public static function typeToValue(String $type){
        $type = strtoupper($type);
        if(!in_array($type, self::getUserTypes())){
            throw new \Exception('Incorrect User Type');
        }
        return constant('self::'. $type);
    }

    /**
     * Returns a string value of a contstant for specific user type
     * @param String $type
     * @return Int  
     */
    public static function typeToString(Int $type){
        return ucfirst(array_search($type, self::userTypes()));
    }


    /**
     * Checks if a user type is valid
     * @param String $type
     * @return Bool
     */
    public static function isValidType(String $type){
        return array_key_exists(strtoupper($type), self::userTypes());
    }   
   

}