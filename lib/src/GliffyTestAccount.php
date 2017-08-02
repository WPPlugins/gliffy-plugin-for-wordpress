<?php

/** Represents information about a Gliffy accont.
 * @package Gliffy
 * @subpackage DataContainer
 */
class GliffyTestAccount
{
    /** The name of the account 
     * @var string */
    public $name;
    /** The type of the account. XSD has possible values 
     * @var string */
    public $type;
    /** The id of the account, not guaranteed to be stable 
     * @var integer */
    public $id;
    /** The maximum number of users allowed in this account 
     * @var integer */
    public $max_users;
    /** True if the account has agreed to the terms of service 
     * @var boolean */
    public $terms;
    /** Expiration date of the account, as a UNIX timestamp 
     * @var integer */
    public $expiration_date;
    /** The list of users in the account as @link GliffyUser objects (this may be null if the request didn't ask for them) 
     * @var array */
    public $users;



    /** An OAuthConsumerKey to be used for testing purposes
      * @var string */ 
    public $oAuthConsumerKey;

    /** An OAuthConsumerSecret to be used for testing purposes 
      * @var string */ 
    public $oAuthConsumerSecret;
   

    public static function from_response_xml( $response  ) { 

        $accountXML = $response->testAccount;

        $newAccount = new GliffyTestAccount( (string)    $accountXML->name,
                                         (string)    $accountXML['account-type'],
                                                     $accountXML['id'],
                                         (int)    $accountXML['max-users'],
                                         (string)    $accountXML['terms'],
                                         (int)    $accountXML->{'expiration-date'},
                                          null, 
                                         (string)    $accountXML->{'oauth-consumer-key'},
                                         (string)    $accountXML->{'oauth-consumer-secret'}


                                    );


        return $newAccount;

    }

    function __construct(
        $name,
        $type,
        $id,
        $max_users,
        $terms,
        $expiration_date,
        $users,
        $oAuthConsumerKey,
        $oAuthConsumerSecret)
    {
        $errors = array();

        if ($oAuthConsumerKey == null) $errors[] = "oAuthConsumerKey is required";
        if ($oAuthConsumerSecret == null) $errors[] = "oAuthConsumerSecret is required"; 
        if ($name == null) $errors[] = "Account Name is required";
        if ($type == null) $errors[] = "Account Type is required";
        if ($id == null) $errors[] = "Account ID is required";

        $this->id = intval($id);
        $this->max_users = intval($max_users);
        $this->expiration_date = intval($expiration_date);
        

        if ($this->id == 0) $errors[] = "Account ID $id was not a number";
        if ($max_users != null)
            if ($this->max_users == 0) $errors[] = "Account max-users $max_users was not a number";
        if ($expiration_date != null)
            if ($this->expiration_date == 0) $errors[] = "Account expiration-date $expiration_date was not a number";

        $this->name = $name;
        $this->type = $type;
        $this->terms = $terms;
        $this->users = $users;
        $this->oAuthConsumerKey = $oAuthConsumerKey;
        $this->oAuthConsumerSecret = $oAuthConsumerSecret;

        if (count($errors) > 0) throw new Exception(implode(",",$errors));
    }
}
?>
