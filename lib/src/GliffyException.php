<?php

/** An exception from Gliffy that you might be able to handle.
 * When interacting with Gliffy, things should generally go smoothly.  Problems that accur
 * will most likely be network related or other problems that you simply won't be able to 
 * recover from.  For problems that do not fall into that category (such as provisioning
 * a user, but your account has used up all its users), this exception will be thrown.
 * @package Gliffy
 * */
class GliffyException extends Exception
{
    /** Create a GliffyException with the given message 
     * @param string the message
     */
    public function __construct($message) { parent::__construct($message); }
}
?>
