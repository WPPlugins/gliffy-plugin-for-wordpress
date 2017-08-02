<?php
/** Example Configuration file for Gliffy PHP Client.  Copy this to "config.php" and edit this based on your account's information.
 * At a minimum, you'll need to update the values for the following:
 * <ul>
 *    <li> $_GLIFFY_appDescription
 *    <li> $_GLIFFY_accountID
 *    <li> $_GLIFFY_oauth_consumer_key
 *    <li> $_GLIFFY_oauth_consumer_secret 
 * </ul>
 * @package Gliffy
 * @subpackage Config
 */

/** This is the root of the Gliffy web app.  The value included in this distribution
 * is probably correct, however check the Gliffy Developer Site for more details.  
 * This should start with either "http://" or "https://" and should not include a trailing slash.
 * Note that https may not be supported for your account type.  
 * @global string $_GLIFFY_root 
 */
$_GLIFFY_root = "http://www.gliffy.com";



/** This is the root of the Gliffy REST API.  The value included in this distribution
 * is probably correct, however check the Gliffy Developer Site for more details.  This should not
 * include a trailing slash
 * @global string  $_GLIFFY_restRoot 
 */
$_GLIFFY_restRoot = $_GLIFFY_root . "/api/1.0";



/** This is a String description of the application you are building. 
 *  Helps Gliffy track which integrations are popular.  This is required.  
 * @global string $_GLIFFY_appDescription 
 */ 
$_GLIFFY_appDescription = "Wordpress Plugin"; 



/** The id of your account, as provided by Gliffy.
 * If you don't have your API credentials, visit http://www.gliffy.com/developer/ to look them up
 * @global string $_GLIFFY_accountID 
 */
$_GLIFFY_accountID = get_option('gliffy_account_id');



/** The oauth consumer key assigned to your account by Gliffy.  
 * If you don't have your API credentials, visit http://www.gliffy.com/developer/ to look them up 
 * @global string $_GLIFFY_oauth_consumer_key
 */
$_GLIFFY_oauth_consumer_key = get_option('gliffy_oauth_consumer_key');



/** The oauth consumer secret assiged to your account by Gliffy.  
 *  WARNING: You should never share your oauth consumer secret as this will allow others to access documents in your account 
 * @global string $_GLIFFY_oauth_consumer_secret
 */
$_GLIFFY_oauth_consumer_secret = get_option('gliffy_oauth_consumer_secret');



/** Setting this to true instructs Gliffy to operate in a strict HTTP/REST
 * mode, where errors that occur result in HTTP status codes and not <go-error> XML responses.  You probably 
 * want to keep this false.  
 * @global boolean $_GLIFFY_strictREST
 */
$_GLIFFY_strictREST = false;



/** If using SSL, this instructs the underlying HTTP code to accept Untrusted SSL certificates. 
 * You should probably never need to set this to true 
 * @global boolean $_GLIFFY_verifySSLCert
 * */
$_GLIFFY_verifySSLCert = false;



/** The level of logging the Gliffy classes shoudl perform.
 * Setting this to LOG_LEVEL_DEBUG can help diagnose issues on the client side.  
 * @global string $_GLIFFY_logLevel
 */
$_GLIFFY_logLevel=GliffyLog::LOG_LEVEL_DEBUG;



/** This is a username to access the REST Root via HTTP Basic Auth.  This is
 * not needed for normal usages of Gliffy, and is here for testing purposes 
 * @global string $_GLIFFY_restRootUsername
 */
$_GLIFFY_restRootUsername = null;



/** This is a password to access the REST Root via HTTP Basic Auth.  This is
 * not needed for normal usages of Gliffy, and is here for testing purposes 
 * @global string $_GLIFFY_restRootPassword
 */
$_GLIFFY_restRootPassword = null;



/** This is the maximum time, in seconds, that the Client Library will wait for HTTP Requests to come back. 
 *  In most cases, this probably doesn't need to be modified.  
 * $global number $_GLIFFY_requestTimeout
 */ 
$_GLIFFY_requestTimeout = 30;



/** This is for debugging and testing.  If false, logging output will be sent using 'echo' instead of sent to the error_log.  
 *  You probably want this to be true so that logging goes to the php error_log.

 * @global boolean $_GLIFFY_logTo_error_log
 */ 
$_GLIFFY_logTo_error_log = true; 



/** This is for debugging and testing.  If true, the raw unparsed content will be available in the {@link GliffyResponse} objects.
 * @ignore
 * @global boolean $_GLIFFY_keepRawContent
 */
$_GLIFFY_keepRawContent = false;



?>
