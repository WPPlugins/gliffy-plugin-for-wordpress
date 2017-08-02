<?php
/** A response from Gliffy.  
 *
 * This is a union of all possible responses, including those formatted to the Gliffy Response XSD, as well
 * as binary/image responses.
 *
 * Exactly one of the public fields will have a value.  Since you will know which one
 * to expect based upon your request, you can simply access it.  If it is null, {@link $error} should have a value
 * explaining why your expected field was null.
 * Note that errors from Gliffy are either because you've made a bad request, or something bad happened on the 
 * Gliffy side.  In both cases, you will want to provide the user with a better error message than the
 * one you get from {@link $error}.  That is useful for your own debugging or reporting problems to Gliffy
 *
 * @package Gliffy
 * @subpackage DataContainer
 */
class GliffyResponse
{
    /** Array of {@link GliffyAccount} objects.
     * @var array */
    public $accounts;
    /** Array of {@link GliffyUser} objects in response to a request for users or admins.
     * @var array */
    public $users;
    /** Array of {@link GliffyDiagram} objects 
     * @var array */
    public $diagrams;
    /** Array of {@link GliffyFolder} objects 
     * @var array */
    public $folders;


    public $oauth_token; 

    /** A {@link GliffyUserToken} object 
     * @var GliffyUserToken */
    public $user_token;

    /** A {@link GliffyLaunchLink} object 
     * @var GliffyLaunchLink */
    public $launch_link;
    /** A {@link GliffyError} object 
     * @var GliffyError */
    public $error;

    /** True if the call was successful, though you can just interrogate the item you expect.
     * @var boolean */
    public $success;

    /** True if the call was a conditional get and the resource was not modified.  This is not set in strict REST mode.
     * @var boolean */
    public $not_modified;

    /** If the request was for an image, this contains the image's bytes.  Note that the format of
     * this depends on the requested mime type.  Gliffy should not send you back a mime type different than
     * what you requested, so you can reliably assume that if this is set, it will be the type requested.
     * @var mixed
     * */
    public $image_data;

    /** The unparsed response (this is only filled in if you specifically requested 
     * that {@link GliffyResponseParser} save it).
     * @var mixed
     * */
    public $content;
}

?>
