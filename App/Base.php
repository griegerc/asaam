<?php

class App_Base
{
    /**
     * Prevents dynamic properties in classes
     * @param string $name
     * @param mixed $value
     * @throws Exception
     */
    public function __set($name, $value)
    {
        throw new Exception('Cannot set property "'.$name.'" with value='.$value);
    }

    /**
     * Retrieves the current unix timestamp
     * @return int
     */
    public static function now()
    {
        return Gpf_Config::get('NOW');
    }

    /**
     * Retrieves the database connection
     * @return Gpf_Database
     */
    protected function _db()
    {
        return Gpf_Database::getInstance();
    }

    /**
     * Converts a integer value (0 or 1) to boolean
     * @param int $intValue
     * @return bool
     */
    public static function intToBool($intValue)
    {
        if ((int)$intValue === 0) {
            return false;
        }
        return true;
    }
}