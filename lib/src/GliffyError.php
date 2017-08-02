<?php

/** An error message from Gliffy 
 * @package Gliffy
 * @subpackage DataContainer
 * */
class GliffyError
{
    /** The message, normalized to the empty string if null or blank.
     * @var string */
    public $message;
    /** The HTTP status code that would've been used if HTTP was supported by your client. 
     * @var integer */
    public $http_status;

    public function GliffyError($message, $http_status)
    {
        $errors = array();
        if ($http_status == null) $errors[] = "Error http-status is required";

        $this->http_status = intval($http_status);
        if ($this->http_status == 0) $errors[] = "Error http-status $http_status was not a number";

        if (count($errors) > 0) throw new Exception(implode(",",$errors));

        $this->message = $message === null ? "" : trim($message);
    }

    /** Formats the http status and message in a suitable way for log messages
     * @return string
     */
    public function __toString()
    {
        return $this->http_status . "/" . ($this->message == null ? "NO MESSAGE" : $this->message);
    }
}
?>
