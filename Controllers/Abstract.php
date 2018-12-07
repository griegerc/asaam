<?php

abstract class Controllers_Abstract extends App_Base
{
    /** @var array */
    public $env;

    /** @var array */
    public $output;

    /**
     * Constructor
     * @param array $env
     */
    public function __construct($env)
    {
        $this->env = $env;
        $this->output = array();

		header_remove('X-Powered-By');

        header('Cache-control: no-cache');
        header('Expires: '.GMDate('D, d M Y H:i:s').' GMT');
        header('Expires: now');
        header('Server: ASAAM');
        header('Pragma: no-cache');
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json; charset=utf-8');

        if (Gpf_Config::get('ALLOWED_CLIENTS') != '*') {
            $clientHasAccess = false;
            $allowedClients = explode(',', Gpf_Config::get('ALLOWED_CLIENTS'));
            foreach ($allowedClients as $client) {
                if (trim($client) == $_SERVER['REMOTE_ADDR']) {
                    $clientHasAccess = true;
                    break;
                }
            }
            if (!$clientHasAccess) {
                Gpf_Logger::warn('Unauthorized access - Restricted client IP:'.$_SERVER['REMOTE_ADDR']);
                header('HTTP/1.1 403 Forbidden');
                exit('"Access forbidden"');
            }
        }

		if (!$this->_isAuth()) {
			Gpf_Logger::warn('Unauthorized access - ENV:'.print_r($env, true).' HEADER:'.print_r(getallheaders(), true));
			header('HTTP/1.1 403 Forbidden');
			exit('"Access forbidden"');
		}
    }

    /**
     * Fetches the content of a env param
     * @param string $paramKey
     * @param mixed $defaultValue
     * @return mixed
     */
    protected function _getParam($paramKey, $defaultValue = NULL)
    {
        if (isset($this->env[$paramKey])) {
            return $this->env[$paramKey];
        } else {
            return $defaultValue;
        }
    }

	/**
	 * @param $string
	 * @return string
	 */
    protected function _cleanString($string)
	{
		return Gpf_Core::trimAndCleanString($string);
	}

    /**
     * Default action
     * @return void
     */
    public function indexAction()
    {
    }

	/**
	 * Generates a signature from a given token
	 * @param string $token
	 * @param int $timestamp
	 * @return string
	 */
	protected function _getSignature($token, $timestamp)
	{
		return md5($token.Gpf_Config::get('AUTH_SECRET').$timestamp);
	}

	/**
	 * Formats an error output message
	 * @param string $errorMessage
	 * @param int $errorCode
	 */
	protected function _setError($errorMessage, $errorCode)
	{
		$this->output['errorMessage'] = $errorMessage;
		$this->output['errorCode'] = $errorCode;
	}

	/**
	 * @return bool
	 */
	protected function _isAuth()
	{
		if ((int)Gpf_Config::get('AUTH_ENABLED') == 0) {
			return true;
		}

		$requestHeaders = getallheaders();

		$token = '';
		if (isset($requestHeaders['X-Auth-Token'])) {
			$token = Gpf_Core::trimAndCleanString($requestHeaders['X-Auth-Token']);
		}
		$signature = '';
		if (isset($requestHeaders['X-Auth-Signature'])) {
			$signature = Gpf_Core::trimAndCleanString($requestHeaders['X-Auth-Signature']);
		}
		$timestamp = 0;
		if (isset($requestHeaders['X-Auth-Timestamp'])) {
			$timestamp = (int)$requestHeaders['X-Auth-Timestamp'];
		}

		if ($timestamp + (int)Gpf_Config::get('AUTH_TIME_OFFSET') < App_Base::now()) {
			return false;
		}
		if ($this->_getSignature($token, $timestamp) != $signature) {
			return false;
		}

		return true;
	}
}