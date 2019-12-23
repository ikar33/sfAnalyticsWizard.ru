<?php

class Logger
{
    public static $INFO = "info";
    public static $WARNING = "warning";
    public static $ERROR = "error";
    public static $CRITICAL_ERROR = "critical_error";

    public static $GENERAL_LOG_DIR;
    public static $EMAIL;
    private $fp;



    private static $instance = null;

    public static function getLogger()
    {
        if(is_null(Logger::$instance)){
            Logger::$instance = new Logger();
        }

        return Logger::$instance;
    }

    public function log($message, $logType = "info"){
        $log =  $logType . " [" . date('d.m.Y h:i:s') . " ]" . $message . "\n";
        error_log($log, 3, self::$GENERAL_LOG_DIR);
        /*if(self::$CRITICAL_ERROR == $logType){
            error_log($message, 1, self::$EMAIL,"Subject: Yandex Direct Quality Reports\nFrom: report error \n");
        }*/
    }


}