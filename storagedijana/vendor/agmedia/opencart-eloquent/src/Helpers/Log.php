<?php
/**
 * User: fj.agmedia.hr
 * Date: 16/06/2017
 * Time: 14:36
 */

namespace Agmedia\Helpers;

class Log
{
    
    /**
     * @var string
     */
    public static $env = 'local';
    
    
    /**
     * @return string
     */
    public static function getEnv()
    {
        return self::$env;
    }
    
    
    /**
     * @param string $env
     */
    public static function setEnv($env)
    {
        self::$env = $env;
    }
    
    
    /**
     * @param $message
     */
    public static function error($message)
    {
        return self::log($message, 'ERROR');
    }
    
    
    /**
     * @param $message
     */
    public static function warning($message)
    {
        return self::log($message, 'WARNING');
    }
    
    
    /**
     * @param $message
     */
    public static function info($message)
    {
        return self::log($message, 'INFO');
    }
    
    
    /**
     * @param $message
     */
    public static function debug($message)
    {
        return self::log($message, 'DEBUG');
    }
    
    
    /**
     * Deprecated function.
     * Should not be used.
     *
     * @param        $message
     * @param string $filename
     *
     * @return mixed
     */
    public static function write($message, $filename = 'agmedia')
    {
        $handle = fopen(DIR_LOGS . $filename . '.log', 'a');
        fwrite($handle, self::resolveStringStart('DEBUG') . print_r($message, true) . "\n");
        fclose($handle);
    }
    
    
    /**
     * Logs the data with year/month/ folders and
     * day concat to file name.
     *
     * @param        $message
     * @param string $type
     */
    private static function log($message, $type)
    {
        $year  = date('Y');
        $month = date('m');
        $day   = date('d');
        $path  = DIR_LOGS . $year . '/';
        
        if ( ! is_dir($path . $month)) {
            mkdir($path . $month, 0755, true);
        }
        
        $filename = 'log';
        
        $handle = fopen($path . $month . '/' . $filename . '_' . $day . '.log', 'a');
        fwrite($handle, self::resolveStringStart($type) . print_r($message, true) . "\n");
        fclose($handle);
    }
    
    
    /**
     * Deprecated function.
     * Should not be used.
     *
     * @param        $message
     * @param string $filename
     *
     * @return mixed
     */
    public static function store($message, $filename = 'store')
    {
        return self::write($message, $filename);
    }
    
    
    /**
     * Used for testing and debuging tasks.
     *
     * @param        $message
     * @param string $filename
     */
    public static function test($message, $filename = 'test')
    {
        return self::write($message, $filename);
    }
    
    
    /**
     * @param $type
     *
     * @return string
     */
    private static function resolveStringStart($type)
    {
        return '[' . date('Y-m-d G:i:s') . '] ' . self::getEnv() . '.' . $type . ': ';
    }
}