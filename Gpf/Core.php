<?php

class Gpf_Core
{
    const PARAM_CONTROLLER = 'controller';
    const PARAM_ACTION     = 'action';

    const MAJOR_VERSION = '1';
    const MINOR_VERSION = '0';
    const BUILD_VERSION = '20181207';

    /**
     * @return string
     */
    public static function getDefaultController()
    {
        return (string)Gpf_Config::get('DEFAULT_CONTROLLER');
    }

    /**
     * @return string
     */
    public static function getDefaultAuthController()
    {
        return (string)Gpf_Config::get('DEFAULT_AUTH_CONTROLLER');
    }

    /**
     * Returns a camel cased string
     * @param string $inputString
     * @return string
     */
    public static function getCamelCased($inputString)
    {
        $pieces = explode('_', $inputString);
        $output = '';

        $i = 0;
        foreach ($pieces as &$piece) {
            if ($i == 0) {
                $output .= $piece;
            } else {
                $output .= ucfirst($piece);
            }
            $i++;
        }
        return $output;
    }

    /**
     * Parse/split the "q" parameter into controller/action params
     * @return array
     */
    private static function _parseRequest()
    {
        $env = array_merge($_GET, $_POST);

        foreach($env as &$param) {
            if (is_string($param)) {
                $param = urldecode($param);
            }
        }

        if (isset($env['q'])) {
            $params = explode('/', $env['q']);
            $path = '';

            if (isset($params[1])) {
                $env[Gpf_Core::PARAM_CONTROLLER] = $params[1];
                $path .= $params[1].'/';
            }
            if (isset($params[2])) {
                $env[Gpf_Core::PARAM_ACTION] = $params[2];
                $path .= $params[2];
            }

            if (isset($params[3])) {
                for($i=3; $i<count($params); $i+=2) {
                    $val = NULL;
                    if (isset($params[$i+1])) {
                        $val = $params[$i+1];
                    }
                    $env[$params[$i]] = $val;
                }
            }
        }
        return $env;
    }

    /**
     * Prepares and stores all the REQUEST-Data and redirects if neccessary.
     * @return array
     */
    public static function processRequest()
    {
        $env = self::_parseRequest();
        $env[self::PARAM_CONTROLLER] = (isset($env[self::PARAM_CONTROLLER]))?$env[self::PARAM_CONTROLLER]:'';
        $env[self::PARAM_ACTION]     = (isset($env[self::PARAM_ACTION]))?$env[self::PARAM_ACTION]:'index';

		switch ($env[self::PARAM_CONTROLLER]) {
			case '':
				$env[self::PARAM_CONTROLLER] = self::getDefaultController();
				break;
			case 'api':
				break;
			default:
				// redirect to the default page, except he clicked on a public area.
				self::redirect(self::getDefaultController());
				break;
		}

		return $env;
    }

    /**
     * Assembles an URL from an given set of parameters.
     * @param string $controller
     * @param bool|false|string $action
     * @param array|bool|false $params
     * @return string
     */
    public static function getURL($controller, $action = false, $params = false)
    {
        $url = '/'.$controller;
        if ($action !== false) {
            $url .= '/'.$action;
        }
        if (is_array($params) && count($params)>0) {
            foreach ($params as $key => $value) {
                $url .= '/'.$key.'/'.$value;
            }
        }
        return $url;
    }

    /**
     * Performs an redirect within the framework
     * @param string $controller
     * @param bool|false|string $action
     * @param array|bool|false $params
     * @return void
     */
    public static function redirect($controller, $action = false, $params = false)
    {
        $url = self::getURL($controller, $action, $params);

        if (isset($_SERVER['SERVER_NAME'])) {
            $serverName = 'http://'. $_SERVER['SERVER_NAME'];
        } else {
            $serverName = '';
        }

        header('Location: '.$serverName.$url);
        exit(0);
    }

	/**
	 * @param string $string
	 * @return mixed
	 */
	public static function removeBadUnicodeChars ($string) {
		// Removes
		// 0000 - 001F / 0080 - 0009F	-> Controls
		// 00A0							-> No-Break Space
		// 00AD							-> Soft Hyphen
		// 034F							-> Combining Grapheme Joiner
		return preg_replace('/[\x{0000}-\x{001F}\x{0080}-\x{009F}\x{00A0}\x{00AD}\x{034F}\x{200C}-\x{200F}\x{2060}-\x{206F}]/u', '', $string);
	}

	/**
	 * @param string $string
	 * @return mixed
	 */
	public static function trimAndCleanString($string) {
		$string = self::removeBadUnicodeChars($string);
		return preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u','',$string);
	}
}
