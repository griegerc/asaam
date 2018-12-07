<?php

define('GPF_BASEPATH', dirname(dirname(__FILE__)));
ignore_user_abort(true);

/**
* Encoding settings
*/
mb_regex_encoding('UTF-8');
mb_internal_encoding('UTF-8');
iconv_set_encoding('internal_encoding', 'UTF-8');

/** Setting the default date/time zone and language */
date_default_timezone_set(Gpf_Config::get('TIMEZONE'));

/**
* AutoLoader for system classes.
* @param string $fullClassName
* @return void
*/
function __autoload($fullClassName)
{
    $classPath = GPF_BASEPATH . '/';
    if (strpos($fullClassName, '_') == false) {
        $className = ucfirst($fullClassName);
    } else {
        $classPath .= substr($fullClassName, 0, strrpos($fullClassName, '_'));
        $classPath = ucfirst(str_replace('_', '/', $classPath)) . '/';
        $className = ucfirst(substr($fullClassName, strrpos($fullClassName, '_') + 1));
    }

    if (file_exists($classPath . $className . '.php')) {
        require_once $classPath . $className . '.php';
    }
}

/**
* Customized error handler
* @param $errno
* @param $errstr
* @param $errfile
* @param $errline
* @return bool
* @throws ErrorException
*/
function errorHandler($errno, $errstr, $errfile, $errline)
{
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler('errorHandler');
