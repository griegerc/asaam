<?php

/**
 * Custom exception handler
 *
 * @param Exception $exception
 * @return void
 */
function exceptionHandler($exception)
{
    $traceline = "#%s %s(%s): %s(%s)";
    $msg = "PHP Fatal error:  Uncaught exception '%s' with message '%s' in %s:%s\nStack trace:\n%s\n  thrown in %s on line %s";

    $trace = $exception->getTrace();
    $key = 0;
    foreach ($trace as $key => $stackPoint) {
        // converting arguments to their type
        // (prevents passwords from ever getting logged as anything other than 'string')
        $trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
    }

    $result = array();
    foreach ($trace as $key => $stackPoint) {

        $file = '???';
        if (isset($stackPoint['file'])) {
            $file = $stackPoint['file'];
        }
        $line = '???';
        if (isset($stackPoint['line'])) {
            $line = $stackPoint['line'];
        }
        $function = '???';
        if (isset($stackPoint['function'])) {
            $function = $stackPoint['function'];
        }

        $result[] = sprintf( $traceline, $key, $file, $line, $function, implode(', ', $stackPoint['args']));
    }

    // trace always ends with {main}
    $result[] = '#' . ++$key . ' {main}';

    // write tracelines into main template
    $msg = sprintf(
        $msg,
        get_class($exception),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        implode("\n", $result),
        $exception->getFile(),
        $exception->getLine());

    // log and echo in dev mode
    Gpf_Logger::error($msg);
    if (Gpf_Config::get('ENVIRONMENT') == 'development') {
        print '<pre>'.$msg.'</pre>';
    }
}
set_exception_handler('exceptionHandler');


/**
 * Sets the environment and error reporting level
 */
if (Gpf_Config::get('ENVIRONMENT') == 'development') {
    error_reporting(E_ALL | E_STRICT | E_NOTICE | E_DEPRECATED | E_USER_DEPRECATED);
} else {
    error_reporting(0);
}


$env = Gpf_Core::processRequest();
$controllerClassName = 'Controllers_'.Gpf_Core::getCamelCased($env[Gpf_Core::PARAM_CONTROLLER]);
$actionMethodName = Gpf_Core::getCamelCased($env[Gpf_Core::PARAM_ACTION]).'Action';

/* @var $controller Controllers_Abstract */
$controller = new $controllerClassName($env);
$controller->$actionMethodName();

print json_encode($controller->output);