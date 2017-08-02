<?php
/** The token a user gets for using Gliffy 
 * @package Gliffy
 * @subpackage DataContainer
 * */
class GliffyUserToken
{
    /** The value of the token 
     * @var string */
    public $value;
    /** The unix timestamp when the token expires 
     * @var integer */
    public $expiration_date;

    public function GliffyUserToken($value, $expiration_date)
    {
        $errors = array();
        if ($value == null) $errors[] = "Token value is required";
        if ($expiration_date == null) $errors[] = "Token expiration_date is required";
        $this->value = $value;
        $this->expiration_date = intval($expiration_date);
        if ($this->expiration_date == 0) $errors[] = "Token expiration_date $expiration_date was not a number";
        if (count($errors) > 0) throw new Exception(implode(",",$errors));
    }
}
?>
