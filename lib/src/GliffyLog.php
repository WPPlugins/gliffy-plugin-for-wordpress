<?php

/** Class used for logging to the PHP error log console.
 *
 * This gives a bit more flexibility in logging levels and turning on/off certain messages
 * without bringing in an entire logging framework.
 * @package Gliffy
 */
class GliffyLog
{
    /** Logging level of errors only */
    const LOG_LEVEL_ERROR = 3;
    /** Logging level of warnings and errors */
    const LOG_LEVEL_WARN = 4;
    /** Logging level of info, warnings, and errors */
    const LOG_LEVEL_INFO = 5;
    /** Logging level of debug, info, warnings, and errors */
    const LOG_LEVEL_DEBUG = 6;

    private $_level_strings = array (
        self::LOG_LEVEL_DEBUG => "DEBUG",
        self::LOG_LEVEL_INFO => "INFO",
        self::LOG_LEVEL_WARN => "WARN",
        self::LOG_LEVEL_ERROR => "ERROR"
        );

    private $_log_level;
    private $_use_error_log;

    /** Create a GliffyLog
     * @param integer one of the constants for the logging level
     */
    public function GliffyLog($level=self::LOG_LEVEL_WARN,$use_error_log=true)
    {
        $this->_log_level = $level;
        $this->_use_error_log = $use_error_log; 
    }

    /** Gets this log's level.
     * @return integer
     */
    public function level() { return $this->_log_level; }

    /** Print a debug message.
     * @param string message
     */
    public function debug($msg) { $this->log(self::LOG_LEVEL_DEBUG,$msg); }
    /** Print a warning message.
     * @param string message
     */
    public function warn($msg) { $this->log(self::LOG_LEVEL_WARN,$msg); }
    /** Print an informational message.
     * @param string message
     */
    public function info($msg) { $this->log(self::LOG_LEVEL_INFO,$msg); }
    /** Print an error message.
     * @param string message
     */
    public function error($msg) { $this->log(self::LOG_LEVEL_ERROR,$msg); }

    /** Generic log function
     * @param integer level of the message
     * @param string the message to log
     */
    public function log($level,$msg)
    {  
     
        if ($this->_log_level >= $level)
        {
      
            $full_msg = date(DATE_RFC822) . "|" . get_class($this) . "|" . $this->_level_strings[$level] . "|" . $msg;
            if( $this->_use_error_log ) {
                error_log($full_msg);
            } else { 
                echo ( $full_msg . "\n");
            }
        }
    }

}
?>
