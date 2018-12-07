<?php

class Gpf_Env
{
    /** @var array */
    private $_params;

    /**
     * Initializes the environment
     */
    public function __construct()
    {
        $this->_params = array_merge($_GET, $_POST);
    }

    /**
     * Fetches a key from the params
     * @param string $key
     * @param NULL|string $default
     * @return mixed
     */
    public function get($key, $default=NULL)
    {
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        } else {
            return $default;
        }
    }

    /**
     * Sets the key with a specifc value
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->_params[$key] = $value;
    }
}