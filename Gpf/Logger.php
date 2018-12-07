<?php

class Gpf_Logger
{
    /**
     * Gpf_Logger constructor
     */
    private function __construct() {}

    /**
     * Writes a message to the log destination.
     * @param string|array|Exception $message
     * @param string $level
     * @param string $prefix
     * @return void
     */
    private function _log($message, $level, $prefix = '')
    {
        if ($message instanceof Exception) {
            $message = 'ErrorCode='.$message->getCode().': '.$message->getMessage();
        } elseif (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }

        if ((int)Gpf_Config::get('LOG_ENABLED') !== 1) {
            return;
        }

        $logHandle = fopen(Gpf_Config::get('LOG_FILE'), 'a');
        if ($logHandle === false) {
            exit('Error: cannot write logfile "'.Gpf_Config::get('LOG_FILE').'"');
        }

        $prefix = trim($prefix);
        if ($prefix == '') {
            $prefix = ' - [DEFAULT] - ';
        } else {
            $prefix = ' - ['.$prefix.'] - ';
        }

        $logTime = date('d.m.Y H:i:s', time()).' - ';
        $message = $logTime.'['.$level.']'.$prefix.$message;

        fwrite($logHandle, $message.PHP_EOL);
        fclose($logHandle);
    }

    /**
     * Writes a debug log message
     * @param mixed $message
     * @param string $prefix
     */
    public static function debug($message, $prefix = '')
    {
        $log = new self();
        $log->_log($message, 'DEBUG', $prefix);
    }

    /**
     * Writes a debug log message
     * @param string $message
     * @param string $prefix
     */
    public static function info($message, $prefix = '')
    {
        $log = new self();
        $log->_log($message, 'INFO', $prefix);
    }

    /**
     * Writes a debug log message
     * @param string $message
     * @param string $prefix
     */
    public static function warn($message, $prefix = '')
    {
        $log = new self();
        $log->_log($message, 'WARN', $prefix);
    }

    /**
     * Writes a debug log message
     * @param string $message
     * @param string $prefix
     */
    public static function error($message, $prefix = '')
    {
        $log = new self();
        $log->_log($message, 'ERROR', $prefix);
    }
}
