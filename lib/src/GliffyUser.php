<?php
/** A user of Gliffy in an account 
 * @package Gliffy
 * @subpackage DataContainer
 * */
class GliffyUser
{
    /** The user's username, used to refer to them 
     * @var string */
    public $username;
    /** User's id; not guaranteed to be stable 
     * @var integer */
    public $id;
    /** The user's email; this can be used to log them into the website outside of the API, but isn't
     * guranteed to be a real email address 
     * @var string */
    public $email;
    /** True if the user is an admin of the account from which this record was retrieved 
     * @var boolean */
    public $is_admin;


    public static function from_response_xml($response) { 
        $users = array();

        foreach ($response->users->user as $currentXMLUser) { 
            $newUser =  new GliffyUser( (int)    $currentXMLUser['id'],
                                        (string) $currentXMLUser->username,
                                        (string) $currentXMLUser->email,
                                        (bool)   $currentXMLUser['is-admin'] );
            
            $users[] = $newUser;   
        }

        return $users;
    } 

    public function GliffyUser($i, $u, $e, $a)
    {
        $errors = array();
        if ($i == null) $errors[] = "User ID is required";
        if ($u == null) $errors[] = "User Username is required"; 

        $this->id = intval($i);

        if ($this->id == 0) $errors[] = "User id $i was not a number";
        if (count($errors) > 0) throw new Exception(implode(",",$errors));

        $this->username = $u;
        $this->email = $e;
        $this->is_admin = $a;
    }

    /** Returns true if the email address looks like one assigned by Gliffy.
     * This allows you to hide these email addresses in a display listing
     * @return boolean 
     */
    public function isGliffyAssignedEmail()
    {
        return (preg_match("/apiuser.gliffy.com$/",$this->email));
    }
}
?>
