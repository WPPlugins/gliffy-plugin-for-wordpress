<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));


//The version of the API that this client library is designed to work with
$_GLIFFY_api_version = "1.0";


/** include */
include_once("GliffyLog.php");

/** Load Config file 

The first of the listed config files to be found will be used.

- config.php
- config.$USER.php  (where $USER is the value of the USER environment variable) 
**/

$userEnvValue = getenv("USER");
$userConfigFile = dirname(__FILE__) . "/config." . $userEnvValue . ".php";

if( file_exists(  dirname(__FILE__) . "/config.php") ) { 
    include_once("config.php");
} else if( file_exists( $userConfigFile ) ) { 
    include_once($userConfigFile);
} else {
    error_log("Could not find config.php or $userConfigFile to load\n");
    echo("Could not find config.php or $userConfigFile to load\n");
    exit(1);
}


/** include */
include_once("OAuth.php"); 
/** include */
include_once("GliffyTestAccount.php"); 
/** include */
include_once("GliffyAccount.php");
/** include */
include_once("GliffyDiagram.php");
/** include */
include_once("GliffyError.php");
/** include */
include_once("GliffyException.php");
/** include */
include_once("GliffyFolder.php"); 
/** include */
include_once("GliffyREST.php");
/** include */
include_once("GliffyResponse.php");
/** include */
include_once("GliffyResponseParser.php");
/** include */
include_once("GliffyUser.php");
/** include */
include_once("GliffyUserToken.php");

/** Primary method of interacting with Gliffy.
 *
 * This class should be created per session, and requires
 * a user to be identified with that session.    The recommedation
 * is that you create an instance of this class during a user
 * authentication and store it in {@link $_SESSION}.  Note that a session is established with
 * Gliffy via the user aotuh_token.  This class keeps track of the assigned token, and its expiration date, and
 * will attempt to re-acquire a new token if it believes the current one is out of date.  You can
 * programmatically re-acquire the token via {@link updateToken()}.  Also note that there is a 5 minute
 * tolerance, so if the token is going to expire in the next 5 minutes, this class will reacquire it.
 * This should hopefully address any drift between your server and Gliffy's.
 *
 * All of these methods throw exceptions when an error is received from Gliffy.  This allows your code
 * to be a bit more simple, without a lot of error checking, since errors from Gliffy are either programming
 * errors on your part or intermittent errors, such as network errors.  You can intercept these errors by using the callback parameter
 * to this class' constructor.  In most cases, this is actually recommended so that errors that occur don't interrupt the page
 * being served.
 * 
 *
 * @package Gliffy
 * @example simple/index.php
 * @author David Copeland, Chris Kohlhardt
 */
class Gliffy
{
    /** Mime type for JPEG images */
    const MIME_TYPE_JPEG = "image/jpeg";
    /** Mime type for PNG images */
    const MIME_TYPE_PNG = "image/png";
    /** Mime type for SVG images */
    const MIME_TYPE_SVG = "image/svg+xml";

    /** A static image you can use to indicate there is a permission problem for viewing an image of a diagram */
    const IMAGE_NO_PERMISSION = "http://www.gliffy.com/go/images/apiPermissionProblem.png";
    /** A static image you can use to indicate there is an error when viewing an image of a diagram */
    const IMAGE_ERROR = "http://www.gliffy.com/go/images/apiIntegrationProblem.png";

    const EXPIRE_TOLERANCE = 300;

    private $_binary_types = array(
        Gliffy::MIME_TYPE_JPEG  => true,
        Gliffy::MIME_TYPE_PNG  => true
        );

    /** Username for this session */
    private $_username;
    /** {@link GliffyREST} instance */
    public $_rest;
    /** string representing base URI for this account */
    private $_account_base;

    /** User's token */
    private $_token;
    private $_token_secret;

    /** Error callback */
    private $_error_callback;

    private $_logger;


    /** Create a new connection to Gliffy.
     *
     * This should be done as part of the user authentication/session creation.
     * @param string the name (or other identifier) of the user.  This user should be unique
     * to your account.  The user need not exist in Gliffy, although, if he does not, Gliffy will
     * attempt to provision him.  If your account has reached it's limit, this will fail, and a {@link GliffyException} will be thrown here.
     * @param function if present, this will called instead of an exception being thrown whenever an exception is triggered, except during
     * the execution of this constructor.
     */
    public function Gliffy($username,$error_callback = null)
    {
        global $_GLIFFY_accountID;
        global $_GLIFFY_logLevel;
        global $_GLIFFY_logTo_error_log; 

        $this->_error_callback = $error_callback;
        $this->_username = $username == null ? null : trim($username);
        if ($this->_username == "")
            $this->_username = null;
        $this->_rest = new GliffyREST(null);
        $this->_account_base = "/accounts/" . $_GLIFFY_accountID;
        $this->_logger = new GliffyLog($_GLIFFY_logLevel,$_GLIFFY_logTo_error_log);

        $this->_logger->debug("Creating Gliffy with username " . $this->_username . " and account base " . $this->_account_base);
    }

    /**
      * No-op destructor
      * 
      */
    function __destruct() { 
        $this->_logger->debug("Destroying Gliffy object for " . $this->_username ); 

        //We don't actually delete the token when the Gliffy object is destroyed.  Why?  Because it's likely that 
        //the programmer created a link using this token, and we'd rather the programmer destroy the token when they think is best. 
        
        //$this->deleteToken();
    }

    public function getToken() {
        return $this->_token;
    }

    public function getTokenSecret() {
        return $this->_token_secret;
    }

    public function setToken($token, $token_secret) {
        $this->_token = $token;
        $this->_token_secret = $token_secret;
        $this->_rest->updateToken($this->_token, $this->_token_secret);
    }

    public function getUsername() {
        return $this->_username;
    }

    /**
      * Delete the OAuth token for this user, effectively logging the user out.
      * Any image links or other request URL's created using this token will become invalid.
      */
    public function deleteToken() { 
        $this->_logger->debug("Deleting the OAuth token for " . $this->_username ); 

        $url = $this->_account_base . "/users/" . $this->_username . "/oauth_token.xml";
        $params = array( "action" => "delete" ); 
       
        $this->updateToken();
        $response = $this->_rest->post($url,$params); 
    }
        
    /** Returns true if this object has a token for the user.  This makes no call to the server and will not try to update the user's token. */
    public function hasToken()
    {
        return $this->_token != null;
    }

    /** Update the user's token, if it needs it.  This gets the user token from Gliffy, which can have one of three effects:
     * - If the user exists and has a valid token, it is returned
     * - If the user exists and has no token, or an expired one, a new one is created and returned
     * - If the user is unknown to gliffy, they are provisioned
     * Whatever the outcome, the internal state of this object is updated with the new token.
     * @param boolean if true, the token is requested from the server regardless of this object's internal state.
     * @return GliffyUserToken token, or null if there was a problem.
     */
    public function updateToken($force=false)
    {
        global $_GLIFFY_appDescription;

        if ( $force || ($this->_token == null)  )
        {
            if ($force)
                $this->_logger->debug("Forcing update of token");
            else if ($this->_token == null)
                $this->_logger->debug("No token, currently");


            if ($this->_username)
            {
                $this->_rest->updateToken(null);
                $this->_logger->debug("Getting token for " . $this->_username);

                $params = array( "action" =>  "create",
                                 "description" => "GLIFFYPHPCLIENT-" . $_GLIFFY_appDescription  );
                

                $response = $this->_rest->post($this->_account_base . "/users/" .  $this->_username  . "/oauth_token.xml",$params,array(),true); 

                if ($response->{'oauth-token-credentials'} === null)
                {
                    if ($response->error !== null)
                    {
                        $this->handleUserProvisionError($response,"Getting token for " . $this->_username);
                    }
                    else
                    {
                        $this->handleException("Major problem; expected a user token or an error message and got neither");
                    }
                    return null;
                }
                else
                {
                    $oauthTokenCredentials = $response->{'oauth-token-credentials'};
                    $this->_token = (string)$oauthTokenCredentials->{'oauth-token'};
                    $this->_token_secret = (string)$oauthTokenCredentials->{'oauth-token-secret'};
                    $this->_logger->info("User " . $this->_username . " got oauth_token " . $this->_token . " from Gliffy");
                    $this->_rest->updateToken($this->_token, $this->_token_secret);
                }
            }
            else
            {
                $this->_logger->debug("Not requesting as we have no username");
            }
        }
        else
        {
            $this->_logger->debug("Not fetching new token, as ours seems to be up to date");
        }
    }


    

    /** Creates a new diagram with the given name, possibly based on an existing diagram.
     *
     * @param string the name of this diagram
     * @param int if positive, represents a diagram id to use as the initial version of the new
     * diagram.  If omitted, a blank diagram is created.  Note that the user whose token is contained
     * in this object must have access to the diagram via this account
     * @return integer the id of the diagram that was created.  An exception is thrown if there was a problem.
     */
    public function createDiagram($diagramName, $templateDiagramId = 0) 
    {
        $this->_logger->debug("CreateDiagram()");
   
        $url = $this->_account_base . "/documents.xml"; 

        $params = array("documentName" => $diagramName,
                        "action" => "create",
                        "documentType" => "diagram"
        );

        if ($templateDiagramId > 0)
            $params["templateDiagramId"] = $templateDiagramId;

        $this->updateToken();
        $response = $this->_rest->post($url,$params);


        $theDiagrams = GliffyDiagram::from_response_xml( $response );



        return $theDiagrams[0]->id;
    }


    /** Deletes the diagram.
     * @param integer the id of the diagram to delete
     */
    public function deleteDiagram($diagramId) 
    {
        $url = $this->_account_base . "/documents/" . $diagramId . ".xml";
        $params = array( "action" => "delete" );

        $this->updateToken();
        $response = $this->_rest->post($url,$params);
    }




    /** Gets the diagram as an image, possibly saving it to a file.
     *
     * @param integer the id of the diagram to get
     * @param string the mime type.  Note that values that are not 
     * {@link MIME_TYPE_JPEG}, {@link MIME_TYPE_PNG}, or {@link MIME_TYPE_SVG} aren't guaranteed to work.
     * @param string if non-null the name of the file to write the diagram to.
     * @param string the size of the diagram.  Currently supports "T" (thumbnail), "S" (small),
     * "M" (medium), "L" (large).  This sizes are porportions based on the diagram's size.  This is
     * ignored if the mime type doesn't support it (e.g. SVG).  Null indicates to use Gliffy's default.
     * @param integer the version to get.  A value less than or equal to the "num versions" of the diagram
     * will be valid.  This is one-based.  A value of null means to get the most recent version
     * @return mixed if $file was null, returns the bytes of the diagram, otherwise, returns true
     */
    public function getDiagramAsImage($diagramId, $mime_type=Gliffy::MIME_TYPE_PNG, $file=null, $size=null, $version=null) 
    {
        $url = $this->_account_base . "/documents/" . $diagramId;
        $params = array();
        if ($size != null)
            $params["size"] = $size;
        if ($version != null)
            $params["version"] = $version;

        $this->updateToken();
        $response = $this->_rest->get($url,$mime_type,$params);
        if ( $response !== null)
        {

            if( $mime_type == Gliffy::MIME_TYPE_SVG ) {
                $response = (string) $response->asXML();
            }

            if ($file == null)
            {
                return $response;
            }
            else
            {
                $fp = null;
                $binary = array_key_exists($mime_type,$this->_binary_types);
                if ($binary)
                    $fp = fopen($file,"wb");
                else
                    $fp = fopen($file,"w");
                if ($fp)
                {
                    $bytes_written = fwrite($fp,$response);
                    if ($bytes_written)
                    {
                        $this->_logger->debug("Successfully wrote $bytes_written bytes to $file");
                        if (!fclose($fp))
                            $this->_logger->warn("Unable to close $file, although the initial open and subsequent write of $bytes_written bytes succeeded");
                    }
                    else
                    {
                        if (!fclose($fp))
                            $this->_logger->warn("Unable to close $file, although the initial open succeeded; this could be related to the write error that's about to be logged");
                        // Not sure how to get the description of the problem
                        $this->handleException("Problem writing image bytes to $file; File was opened successfully, but the write failed.  Check PHP log for possibly explanations");
                    }
                }
                else
                {
                    // Not sure how to get the description of the problem
                    $this->handleException("Problem opening $file for writing " . ($binary ? "in binary mode" : "in non-binary mode") . ".  Check PHP log for possibly causes");
                }
            }
        }
        else
        {
            $this->handleException($this->createErrorMessage("While getting diagram $diagramId as a $mime_type",$response));
        }
    }

    /** Returns a URL to the diagram that can be used in an HTML IMG tag.
     * This URL is only good as long as the user's token is good, so it should not be use for 
     * long-term reference to the diagram.  Parameters are the same as for {@link getDiagramAsImage}, save for the missing
     * file parameter.
     * @param integer id of the diagram
     * @param string mime type
     * @param string size (null means full size)
     * @param string which version (null means current)
     * @param boolean if true, put some randomness in the URL to try to keep the browser for caching it
     * @return string a URL to the requested image
     */
    public function getDiagramAsURL($diagramId, $mime_type=Gliffy::MIME_TYPE_PNG, $size=null, $version=null, $force=false) 
    {
        $extension = "";

        if( $mime_type == Gliffy::MIME_TYPE_PNG ) {
            $extension = ".png"; 
        } else if( $mime_type == Gliffy::MIME_TYPE_JPEG ) {
            $extension = ".jpg"; 
        } else if ( $mime_type == Gliffy::MIME_TYPE_SVG ) { 
            $extension = ".svg"; 
        }

        $url = $this->_account_base . "/documents/" . $diagramId . $extension;
        $params = array();
        if ($size != null)
            $params["size"] = $size;
        if ($version != null)
            $params["version"] = $version;
        if ($force)
            $params["rand"] = time() + rand();

        $this->updateToken();
        return $this->_rest->get($url,$mime_type,$params,true);
    }


    /** Gets a list of diagrams, either for the entire account, or for the given folder.
     *
     * @param string if present, returns a list of diagrams in the folder only.  This is the full path/name of the folder
     * @return array an array of {@link GlifyDiagram} objects representing the diagrams in the given context.
     * Throws an exception if there was some problem.  Shouldn't return null
     */
    public function getDiagrams($folderPath=null) 
    {
        if ($folderPath == null)
            $url = $this->_account_base . "/documents.xml";
        else
            $url = $this->_account_base . "/folders/" . $folderPath . "/documents.xml";

        $this->updateToken(); 

        $params = array( 'action' =>  'get'  );

        $response = $this->_rest->get($url,"text/xml",$params); 
   
        $theDiagrams = GliffyDiagram::from_response_xml( $response ); 
        
        $this->_logger->debug("Returning " . sizeof( $theDiagrams ) . " diagrams");

        return $theDiagrams;

    }


    /** Moves the diagram from its current folder to the new folder (if the user owning this session
     * has access to the diagram
     * @param integer the id of the diagram to move
     * @param string the path of the folder to move it to
     */
    public function moveDiagram($diagramId,$folderPath) 
    {
        $url = $this->_account_base . "/folders/" . $folderPath . "/documents/" . $diagramId . ".xml";
        $params = array( "action" => "move" );


        $this->updateToken();
        $response = $this->_rest->post($url,$params);

    }

    /** Returns meta-data about a particular diagram.
     *
     * Note that an anonymous user will get a 401/Unauthorized, if they attempt to get meta data about a non-public diagram.  To make things
     * easier, this method will return a {@link GliffyDiagram} object.  This way "null" can be viewed as a bad diagram id, an exception can
     * be viewed as an legitimate problem and a non-null return as something usable.  If the user owning this Gliffy object is anonymous, and
     * the returned diagram is not public, you will know that they recieved a 401 from the back-end.  
     * @param integer the id of the diagram
     * @return GliffyDiagram the diagram's info, or null if it wasn't found
     */
    public function getDiagramMetaData($diagramId)
    {
        $url = $this->_account_base . "/documents/" . $diagramId . "/meta-data.xml";
        $params = array (  "action" => "get" );

        $this->updateToken();

        $response = $this->_rest->get($url,"text/xml",$params); 
   
        $theDiagrams = GliffyDiagram::from_response_xml( $response ); 

        return $theDiagrams[0];          
    }

    /** Builds a returns a Gliffy editor launch link for the given diagram id  
     * 
     *
     * @param integer the id of the diagram to edit
     * @param string if present represents the URL to return the user to after they have completed their editing.  You should not urlencode this, it will be done for you
     * @param string the text that should be used in Gliffy to represent the "return to the application" button.
     * @return String this contains the complete URL to be used to edit the given diagram and behave as described.  The GliffyLaunchLink also contains
     * the diagram name, which can be used for linking.
     * Throws an exception if there was a problem
     */
    public function getEditDiagramLink($diagramId,$returnURL=null,$returnText=null) 
    {
        global $_GLIFFY_root; 

        $url = $_GLIFFY_root . "/gliffy/"; 
        $params["launchDiagramId"] = $diagramId; 

        if ($returnURL != null) $params["returnURL"] = $returnURL;
        if ($returnText != null) $params["returnButtonText"] = urlencode($returnText); 
	
	$params["returnURL"] = str_replace('/gliffy/', '', $params['returnURL']);
        $this->updateToken();
        $theLink = $this->_rest->get($url,"text/xml",$params,true); 

        return $theLink; 
    }


    /** Creates a folder at the given path.  Throws an exception if there was a problem (including the folder already existing)
     *
     * @param string the path of the folder to create; should be unique within the application
     */
    public function createFolder($folderPath) 
    {
        $url = $this->_account_base . "/folders/" . $folderPath . ".xml";

        $params = array( "action" => "create" );

        $this->updateToken();
        $response = $this->_rest->post($url,$params); 
    }


    /** Deletes the folder with the given path.  All diagrams in this folder will move to the default folder (which, incidentially, cannot be deleted).  
     *
     * This throws an exception if the folder didn't exist or there was some other problem.
     * @param string the name of the folder to delete
     */
    public function deleteFolder($folderPath) 
    {
        $url = $this->_account_base . "/folders/" . $folderPath . ".xml";
        $params = array ( "action" => "delete" );

        $this->updateToken();
        $response = $this->_rest->post($url,$params);

    }


    /** Returns all folders in this account.
     *
     * @return a ROOT GliffyFolder object which contains sub-folders as children
     */
    public function getFolders() 
    {
        $url = $this->_account_base . "/folders.xml";
        $this->updateToken();

        $params = array( "action" => "get" );

        $response = $this->_rest->get_simple($url,$params); 
         
        $theFolders = GliffyFolder::from_response_xml( $response ); 

        return $theFolders;
    }


    /** Adds the user to the given folder, so that he may have access to that folder's contents.  
     *
     * @param string the name of the folder to which the user should be added
     * @param string the user to add (they should exist)
     */
    public function addUserToFolder($folderName,$username) 
    {
        $url = $this->_account_base . "/folders/$folderName/users/$username.xml";
        $params = array( "action" => "update",
                         "read" => "true",
                         "write" => "true" );

        $this->updateToken();
        $response = $this->_rest->post($url,$params);

    }


    /** Removes the user from the given folder.
     *
     * @param string the name of the folder from which to remove the user
     * @param stinrg the name of the user to remove 
     */
    public function removeUserFromFolder($folderName,$username) 
    {

        $url = $this->_account_base . "/folders/$folderName/users/$username.xml";
        $params = array( "action" => "update",
                         "read" =>  "false",
                         "write" => "false" );

        $this->updateToken();
        $response = $this->_rest->post($url,$params); 
    }


    /** Returns all users in the account, or in the folder specified.
     *
     * @param string if not null, returns a list of users who have access to that folder.  If null, all users in the account are returned.
     * @return array an array of {@link GliffyUser} objects.
     */
    public function getUsers($folderName=null) 
    {
        $url = $this->_account_base;
        $params = array( "action" => "get" );

        if ($folderName != null)
            $url .= "/folders/$folderName"; 

        $url .= "/users.xml";

        $this->updateToken();

        $response = $this->_rest->get($url,"text/xml",$params);
  
        echo $response->asXML();

        $theUsers = GliffyUser::from_response_xml( $response ); 

        return $theUsers;
       
    }


    /** Returns the folders the given user has access to.
     *
     * @param string the name of the user to check
     * @return array an array of {@link GliffyFolder} objects.
     */
    public function getUserFolders($username) 
    {
        $url = $this->_account_base . "/users/" . $username . "/folders.xml";
        $params = array ( "action" => "get" ); 

        $this->updateToken(); 
        $response = $this->_rest->get($url,"text/xml",$params);
      
        $rootFolder = GliffyFolder::from_response_xml($response);

        return $rootFolder; 
    }


    /** Deletes the given user (from this acount), which may not be the user whose session owns this object.  
     *
     * If the user doesn't have access to other Gliffy accounts, his record is deleted.  This cannot be undone.
     * @param string the name of the user to delete
     */
    public function deleteUser($username) 
    {
        $url = $this->_account_base . "/users/" . $username . ".xml";
        $params = array( "action" => "delete" ); 

        $this->updateToken();
        $response = $this->_rest->post($url,$params);

    }


    


    /** Adds the named user to the account, if sufficient users exist.  
     *
     * An exception here indicates a problem unrelated to the account maximum.
     * @param string the name of the user to add.
     * @return boolean true if the user was added, false if the account has already reached its maximum number of users.
     */
    public function addUser($username) 
    {
        $url = $this->_account_base . "/users.xml";
        $params = array ( "action" => "create",
                          "userName" => $username );

        $this->updateToken();
        $response = $this->_rest->post($url,$params);

    }


    /** Updates the user's information.  
     *
     * This is useful for three things:
     * - manage account "admins"
     * - if you wish to store actual user emails with their gliffy accont for simplified association
     * - if you wish to allow users to access your Gliffy data via the Gliffy website.  With the email and password, they
     *   will be able to login to Gliffy outside of your application.
     * @param string the user to update
     * @param boolean if non-null, sets this user's admin status (null indicates no change should be made)
     * @param string the email address to give them.  This must be unique to all of Gliffy.  null indicates no change
     * @param string the password to give them so that they can access Gliffy via the Gliffy website.  null indicates no change
     */
    public function updateUser($username,$admin=null,$email=null,$password=null) 
    {
        if (($admin == null) && ($email == null) && ($password == null) )
            $this->handleException("You must supply at least one of admin, email, or password to updateUser()");

        $url = $this->_account_base . "/users/" . $username . ".xml";
        $params = array( "action" => "update" );
        if ($admin != null)
            $params["admin"] = $admin ? "true" : "false";
        if ($email != null)
            $params["email"] = $email;
        if ($password != null)
            $params["password"] = $password;

        $this->updateToken();
        $response = $this->_rest->post($url,$params); 
    }


    /** Returns the list of "admins" for the account.  Admins can mean whatever you want them to mean, however for Gliffy,
     * an admin is simply someone who has access to all Folders of an account.
     * @return array an array of {@link GliffyUser} objects
     */
    public function getAdmins() 
    { 
        $url = $this->_account_base . "/admins";
        $params = array( "action" => "get" ); 

        $this->updateToken(); 
        $response = $this->_rest->get($url,"text/xml",$params); 
        echo $response->asXML();
        $theAdmins = GliffyUser::from_response_xml( $response ); 

        return $theAdmins; 
    }

    /** Gets all diagrams that the user can access 
     * @param string if null, the user whose session this is will be used
     * @return array an array of {@link GliffyDiagram} objects, sorted by name, that 
     * represent all diagrams in all folders to which the given user has access
     */
    public function getUserDiagrams($username=null)
    {
        if ($username == null)
            $username = $this->_username;

        // TODO: Get public diagrams in the account?
        if ($username == null)
            return array();

        $this->_logger->debug("Getting user diagrams for $username");
        $folders = $this->getUserFolders($username);
        $diagrams = $this->getUserDiagramsInFolders($folders);
        $diagrams_to_sort = array();
        foreach ($diagrams as $d)
        {
            $diagrams_to_sort[$d->name . $d->id] = $d;
        }
        ksort($diagrams_to_sort);
        return $diagrams_to_sort;
    }

    public function updateDiagramContent($diagramId,$content)
    { 

        $url = $this->_account_base . "/documents/" . $diagramId . ".xml";

        $params = array (  "action" => "update",
                           "content" => $content );

        $this->updateToken(); 
   
        $response = $this->_rest->post($url,$params);

        $theDiagrams = GliffyDiagram::from_response_xml( $response ); 

        return $theDiagrams[0];          
    }


    private function getDiagram($diagramId,$format) { 
        $this->_logger->debug("getDiagram as" . $format); 
       
        $this->updateToken();

        $url = $this->_account_base . "/documents/" . $diagramId . "." . $format;
        $params = array (  "action" => "get" );

        $this->updateToken();

        //need to cast the xml object to string
        $content =  $this->_rest->get($url,"text/xml",$params); 


        return $content->asXML(); 
    }
 

    public function getDiagramAsXML($diagramId) { 
        return $this->getDiagram($diagramId,"xml");
    } 

    function getAccountInfo() { 
        $this->_logger->debug("Getting account info"); 
       
        $this->updateToken();
        $url = $this->_account_base . ".xml"; 
        $params = array( 'action' =>  'create'  );
        $response = $this->_rest->get($url,"text/xml",$params); 

        $theAccount = GliffyAccount::from_response_xml( $response );

        return $theAccount; 
    }

    private function getUserDiagramsInFolders($folders)
    {
        $diagrams_to_return = array();
        foreach ($folders as $f)
        {
            $this->_logger->debug("Getting diagrams from folder " . $f->name);
            $diagrams = $this->getDiagrams($f->path);
            if ($diagrams)
            {
                foreach ($diagrams as $d)
                {
                    $this->_logger->debug("Adding diagram " . $d->name);
                    $diagrams_to_return[] = $d;
                }
            }
            $child_diagrams = $this->getUserDiagramsInFolders($f->children);
            foreach ($child_diagrams as $d)
            {
                $diagrams_to_return[] = $d;
            }
        }
        return $diagrams_to_return;
    }

    /** Given a {@link GliffyResponse}, examines it to determine the error message.
     *
     * This handles when the response has no error set
     *
     * @param string a caller-specified message, hopefully indicating the operation that was being performed
     * @param GliffyResponse the response received from {@link GliffyREST}.
     */
    private function createErrorMessage($message,$response)
    {
        if ($response->error == null)
            return $message . ". Didn't recieve expected results, nor did we receive a Gliffy Error";
        else
            return $message . ". " . ($response->error->message == "" ? "no message" : $response->error->message) . " (" . $response->error->http_status . ")";
    }

    /** Handles dealing with the error messages from calls that provision users */
    private function handleUserProvisionError($response, $activity)
    {
        if ($response->error->http_status == 401)
            throw new GliffyException("Error $activity; " . ($response->error->message == "" ? "Check your API Key and shared secret" : $response->error->message) );
        # Error that can't be handled
        if ($response->error->message == "")
            $this->handleException ("Error $activity; it's possible your account's maximum users has been reached");
        else
            $this->handleException ($response->error->message);
    }

    private function handleException($msg)
    {
        if ($this->_error_callback != null)
        {
            $this->_logger->info("Using callback named " . $this->_error_callback);
            call_user_func($this->_error_callback,$msg);
        }
        else
        {
            $this->_logger->info("Not Using callback");
            throw new Exception($msg);
        }
    }
}
?>
