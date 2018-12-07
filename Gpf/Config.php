<?php

class Gpf_Config
{
    /**
     * Variable config values stored in the db-table "config"
     */
    const DB_VERSION = 'DB_VERSION';

    /** @var Gpf_Config */
    private static $_instance = NULL;

    /** @var array */
    private $_settings = array();

    /**
     * Instance of the config
     * @return Gpf_Config
     */
    public static function getInstance()
    {
        if (NULL === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Instance
     */
    private function __construct()
    {
        $this->_settings = parse_ini_file(GPF_BASEPATH.'/config.ini');
    }

    /**
     * @return void
     */
    private function __clone() {}

    /**
     * Retrieves a specific config setting value
     * @param string $key
     * @return string|int
     */
    public static function get($key)
    {
        $config = self::getInstance();
        if (!isset($config->_settings[$key])) {
            if ($key == 'NOW') {
                $db = Gpf_Database::getInstance();
                $dbRes = $db->query('SELECT UNIX_TIMESTAMP() as ts;', true);
                $now = (int)$dbRes['ts'];
                $config->_settings['NOW'] = $now;
            } else {
				Gpf_Logger::error('Config key "'.$key.'" not found', 'CONFIG');
				return NULL;
            }
        }
        return $config->_settings[$key];
    }

    /**
     * Retrieves a special key stored in the db-table "config"
     * @param string $key
     * @return NULL|string|int
     */
    public static function getVar($key)
    {
        $db = Gpf_Database::getInstance();
        $dbRes = $db->inquiryOne('getConfigValue', $key);

        if (isset($dbRes['value'])) {
            return $dbRes['value'];
        } else {
            return NULL;
        }
    }

    /**
     * Sets a special key stored in the db-table "config"
     * @param string $key
     * @param $value
     * @return void
     */
    public static function setVar($key, $value)
    {
        $db = Gpf_Database::getInstance();
        $db->inquiry('setConfigValue', array($key, $value));
    }
}
