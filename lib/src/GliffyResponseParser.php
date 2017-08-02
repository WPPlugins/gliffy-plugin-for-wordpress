<?php
/** Parses a Gliffy response.  
 *
 * This can have three basic results:
 * - The response was well-formed and represents a normal respons.  See GliffyResponse.
 * - The response represents an error you made in your request on one on the Gliffy side.  The GliffyResponse has the error information
 * - The response was not a proper Gliffy response.
 * Also note that this uses PHP's DOM parser.  This means that the error reporting you get back will be pretty bad.  Hopefully, this will
 * not  be a problem, since Gliffy response are relatively short and simple.  Further, Gliffy should never send you an unparsable
 * response.
 * @package Gliffy
 * @example Example3.php Basic Usage
 */
class GliffyResponseParser
{
    const account_xml_name = "account";
    const account_type_xml_name = "account-type";
    const accounts_xml_name = "accounts";
    const diagram_xml_name = "diagram";
    const diagrams_xml_name = "diagrams";
    const error_xml_name = "error";
    const folder_xml_name = "folder";
    const folders_xml_name = "folders";
    const launch_link_xml_name = "launch-link";
    const response_xml_name = "response";
    const user_xml_name = "user";
    const user_token_xml_name = "user-token";
    const users_xml_name = "users";
    const oauth_token_xml_name = "oauth-token";

    private $_root;
    private $_error_message;
    private $_warnings;
    private $_logger;

    /** Create a GliffyResponseParser for some received XML.
     *
     * @param mixed the response that you received.  If this is in Gliffy Response XML, it will be parsed.  Otherwise,
     * this will do the best it can to determine what to do with it.
     * @param integer one of the constants of this class to represent the level of logging.
     * @param boolean if true, the raw content is saved in the returned response.  Useful for braindead testing of Gliffy
     * @param boolean if true, indicates we think this is binary data
     * Messages are logged via error_log
     */
    public function GliffyResponseParser($data,$log_level=GliffyLog::LOG_LEVEL_WARN,$save_content=false, $binary=false)
    { 
        global $_GLIFFY_logTo_error_log;

        $this->_logger = new GliffyLog($log_level,$_GLIFFY_logTo_error_log);
        $warnings = array();
        try
        {
            $document = new DOMDocument();
            $document->preserveWhitespace = FALSE;
            try
            {
                $this->_logger->debug("starting parsing");
                if ( $binary || ($data == "") || !$document->loadXML($data,LIBXML_NOWARNING) )
                {
                    $this->_logger->debug("Got false back while parsing data.  Assuming it's binary image data");
                    $this->_root = new GliffyResponse();
                    $this->_root->image_data = $data;
                    if ($save_content) $this->_root->content = $data;
                    return;
                }
            }
            catch (Exception $ex)
            {
                $this->_logger->warn("While parsing data (" . $ex->getMessage() . ")");
                $this->_root = new GliffyResponse();
                $this->image_data = $data;
                if ($save_content) $this->_root->content = $data;
                return;
            }
            $response = $document->getElementsByTagName(self::response_xml_name);
            if ($response->length == 0) 
            {
                // OK, this means we got XML, but not in Gliffy Response format
                $svg = $document->getElementsByTagName("svg");
                if ($svg->length > 0)
                {
                    $this->_root = new GliffyResponse();
                    $this->_root->image_data = $data;
                    if ($save_content) $this->_root->content = $data;
                    return;
                }
                else
                {
                    $gliffy = $document->getElementsByTagName("stage");
                    if ($gliffy->length > 0)
                    {
                        $this->_root = new GliffyResponse();
                        $this->_root->image_data = $data;
                        if ($save_content) $this->_root->content = $data;
                        return;
                    }
                }
                throw new Exception("Unknown XML format");
            }

            $root_node = $response->item(0);

            $node_map = $root_node->attributes;
            $success = $this->get_node_value($node_map,"success");
            $not_modified = $this->get_node_value($node_map,"not-modified");

            $children = $root_node->childNodes;
            if ($children == null) throw new Exception("Empty response cannot be parsed");

            foreach ($children as $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    $this->_root = new GliffyResponse();
                    if ($child->nodeName == self::accounts_xml_name)
                    {
                        $this->_root->accounts = $this->read_accounts($child);
                        if ($save_content) $this->_root->content = $data;
                    }
                    else if ($child->nodeName == self::users_xml_name)
                    {
                        $this->_root->users = $this->read_users($child);
                        if ($save_content) $this->_root->content = $data;
                    }
                    else if ($child->nodeName == self::diagrams_xml_name)
                    {
                        $diagrams = $this->read_diagrams($child);
                        if ($diagrams == null)
                        {
                            $this->_logger->debug("Got null diagrams, making empty array");
                            $diagrams = array();
                        }
                        $this->_root->diagrams = $diagrams;
                        if ($save_content) $this->_root->content = $data;
                    }
                    else if ($child->nodeName == self::oauth_token_xml_name)
                    {
                        $this->_root->oauth_token = $this->read_oauth_token($child);
                        if ($save_content) $this->_root->content = $data;
                    }
                    else if ($child->nodeName == self::error_xml_name)
                    {
                        $this->_root->error = $this->read_error($child);
                        if ($save_content) $this->_root->content = $data;
                    }
                    else if ($child->nodeName == self::folders_xml_name)
                    {
                        $this->_root->folders = $this->read_folders($child);
                        if ($save_content) $this->_root->content = $data;
                    }
                    else if ($child->nodeName == self::launch_link_xml_name)
                    {
                        $this->_root->launch_link = $this->read_launch_link($child);
                        if ($save_content) $this->_root->content = $data;
                    }
                    else
                    {
                        throw new Exception("Don't know how to handle '$child->nodeName'");
                        $this->_root = null;
                    }
                    break; // only expecting one child
                }
            }

            if (!$this->_root)
            {
                $this->_root = new GliffyResponse();
                if ($save_content) $this->_root->content = $data;
            }
            $this->_root->success = $success;
            $this->_root->not_modified = $not_modified;
        }
        catch (Exception $e)
        {
            $this->_logger->error($e->getMessage());
            $this->_error_message = $e->getMessage();
        }
    }

    /** Gets the response, as a GliffyResponse, parsed in the constructor.  
     *
     * May return null.  If so, that represents a parsing error.  
     *
     * @see error_message()
     *
     * @return GliffyResponse the response that was parsed, or null if there was a problem
     */
    public function response() { return $this->_root; }

    /** If {@link response()} returns null, this provides a probably explanation as to the problem.
     *
     * This is more for debugging your code, and less for informing the user
     * @return string the error message, if there was a problem
     */
    public function error_message() { return $this->_error_message; }

    /** Get the warnings generated by parsing.  
     *
     * This is useful if you want easier access to warnings than via the logging mechanism
     * @return array strings representing all the warnings
     */
    public function warnings() { return $this->_warnings; }

    private function read_accounts(DOMNode $node)
    {
        $this->_logger->debug("Reading accounts");
        $accounts = array();
        foreach ($node->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->nodeName == self::account_xml_name)
                {
                    $accounts[] = $this->read_account($child);
                }
                else
                {
                    $this->_logger->warn("[read_accounts] Unexpected node '$child->nodeName' in accounts list");
                }
            }
        }
        return $accounts;
    }

    private function read_users(DOMNode $node)
    {
        $this->_logger->debug("Reading users");
        $users = array();
        foreach ($node->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->nodeName == self::user_xml_name)
                {
                    $users[] = $this->read_user($child);
                }
                else
                {
                    $this->_logger->warn("[read_users] Unexpected node '$child->nodeName' in users list");
                }
            }
        }
        return $users;
    }

    private function read_user(DOMNode $node)
    {
        $this->_logger->debug("Reading a user");
        $node_map = $node->attributes;
        $id_node = $node_map->getNamedItem("id");
        $admin_node = $node_map->getNamedItem("is-admin");

        if ($id_node != null)
            $id = $id_node->nodeValue;
        $is_admin = false;
        if ($admin_node != null)
        {
            $this->_logger->debug("is-admin node value == '" . $admin_node->nodeValue . "'");
            $is_admin = $admin_node->nodeValue == "true";
        }

        foreach ($node->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->nodeName == "username")
                {
                    $username = $child->nodeValue;
                }
                else if ($child->nodeName == "email")
                {
                    $email = $child->nodeValue;
                }
                else
                {
                    $this->_logger->warn("Unknown element '$child->nodeName' in user");
                }
            }
        }

        $this->_logger->debug("$id:$username:$email:$is_admin");
        return new GliffyUser($id,$username,$email,$is_admin);
    }

    private function read_account(DOMNode $node)
    {
        $this->_logger->debug("Reading an account");
        $node_map = $node->attributes;

        $id = $this->get_node_value($node_map,"id");
        $type = $this->get_node_value($node_map,self::account_type_xml_name);
        $max_users = $this->get_node_value($node_map,"max-users");
        $terms = $this->get_node_value($node_map,"terms");
        $terms = ( ($terms != null) && $terms == "true");

        foreach ($node->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->nodeName == "name")
                {
                    $name = $child->nodeValue;
                }
                else if ($child->nodeName == "expiration-date")
                {
                    $expiration_str = $child->nodeValue;
                    $expiration = intval($expiration_str);
                    if ($expiration == 0) 
                        $expiration = null;
                }
                else if ($child->nodeName == self::users_xml_name)
                {
                    $users = $this->read_users($child);
                }
                else
                {
                    $this->_logger->warn("[read_account] Unexpected node '$child->nodeName' in account");
                }
            }
        }
        $this->_logger->debug("$name:$type:$id:$max_users:$terms:$expiration:" . count($users));

        return new GliffyAccount($name,$type,$id,$max_users,$terms,$expiration,$users);
    }

    private function read_diagrams(DOMNode $node)
    {
        $this->_logger->debug("Reading diagrams");
        $diagrams = array();
        foreach ($node->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->nodeName == self::diagram_xml_name)
                {
                    $diagrams[] = $this->read_diagram($child);
                }
                else
                {
                    $this->_logger->warn("[read_diagrams] Unexpected node '$child->nodeName' in diagrams list");
                }
            }
        }
        return $diagrams;
    }

    private function read_diagram(DOMNode $node)
    {
        $this->_logger->debug("Reading a Diagram");
        $node_map = $node->attributes;
        $id = $this->get_node_value($node_map,"id");
        $ispublic = $this->get_node_value($node_map,"is-public");
        $this->_logger->debug("**** $ispublic");
        $public = isset($ispublic);
//        $private = $this->get_node_value($node_map,"is-private");
        $num_versions = $this->get_node_value($node_map,"num-versions");

        foreach ($node->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->nodeName == "name")
                {
                    $name = $child->nodeValue;
                }
                else
                {
                    $this->_logger->warn("[read_diagram] Unexpected node '$child->nodeName' in account");
                }
            }
        }
        $this->_logger->debug("$id:$num_versions:$name");
        return new GliffyDiagram($id, $num_versions, $name, $public, !$public);
    }

    private function read_oauth_token(DOMNode $node)
    {
        $this->_logger->debug("Reading an ouath token");
        $value = null;
        foreach ($node->childNodes as $child)
        {
            if ($child->nodeType == XML_TEXT_NODE)
                $value = $child->nodeValue;
            else
                $this->_logger->warn("Ignoring $child->nodeType/$child->nodeName");
        }
        $node_map = $node->attributes;
        $date_str = $this->get_node_value($node_map,"expiration");
        if ($date_str != null)
        {
            $date = intval($date_str);
            if ($date == 0)
                $date = null;
        }

        $this->_logger->debug("$value:$date");
        return new GliffyOAuthToken($value,$date);
    }



    private function read_user_token(DOMNode $node)
    {
        $this->_logger->debug("Reading a user token");
        $value = null;
        foreach ($node->childNodes as $child)
        {
            if ($child->nodeType == XML_TEXT_NODE)
                $value = $child->nodeValue;
            else
                $this->_logger->warn("Ignoring $child->nodeType/$child->nodeName");
        }
        $node_map = $node->attributes;
        $date_str = $this->get_node_value($node_map,"expiration");
        if ($date_str != null)
        {
            $date = intval($date_str);
            if ($date == 0)
                $date = null;
        }

        $this->_logger->debug("$value:$date");
        return new GliffyUserToken($value,$date);
    }

    private function read_error(DOMNode $node)
    {
        $this->_logger->debug("Reading a gliffy error");
        $node_map = $node->attributes;
        $status = $this->get_node_value($node_map,"http-status");
        $value = null;
        foreach ($node->childNodes as $child)
        {
            if ($child->nodeType == XML_TEXT_NODE)
                $value = $child->nodeValue;
            else
                $this->_logger->warn("Ignoring $child->nodeType/$child->nodeName");
        }

        if ( ($status == "200") || ($status == "201") )
        {
            $this->_logger->debug("error represents success.  Returning null");
            return null;
        }

        $this->_logger->debug("$value:$status");
        return new GliffyError($value,$status);
    }

    private function read_folders(DOMNode $node)
    {
        $folders = array();
        $this->_logger->debug("Reading folders");
        foreach ($node->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->nodeName == self::folder_xml_name)
                {
                    $folders[] = $this->read_folder($child);
                }
                else
                {
                    $this->_logger->warn("[read_folders] Unexpected node '$child->nodeName' in folders list");
                }
            }
        }
        return $folders;
    }

    private function read_folder(DOMNode $node)
    {
        $this->_logger->debug("Reading a folder");
        $node_map = $node->attributes;
        $default = $this->get_node_value($node_map,"is-default");
        $default = ($default != null) && ($default == "true");
        $name = null;
        $path = null;
        $children = array();
        foreach ($node->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->nodeName == "name")
                    $name = $child->nodeValue;
                else if ($child->nodeName == "path")
                    $path = $child->nodeValue;
                else if ($child->nodeName == self::folder_xml_name)
                    $children[] = $this->read_folder($child);
                else
                    $this->_logger->warn("[read_folder] Unexpected node '$child->nodeName' in folder");
            }
        }

        $this->_logger->debug("$name:$default");
        if ($path == null)
            $this->_logger->warn("Didn't get a path for folder named $name");
        $folder = new GliffyFolder($name,$default,$path);
        $folder->children = $children;
        return $folder;
    }

    private function read_launch_link(DOMNode $node)
    {
        $this->_logger->debug("Reading a launch link");
        $node_map = $node->attributes;
        $name = $this->get_node_value($node_map,"diagram-name");
        $link = null;
        foreach ($node->childNodes as $child)
        {
            if ($child->nodeType == XML_TEXT_NODE)
            {
                $link = $child->nodeValue;
            }
        }

        $this->_logger->debug("$name:$link");
        return new GliffyLaunchLink($name,$link);
    }

    private function get_node_value($node_map,$name)
    {
        $this->_logger->debug("Getting attribute node named $name");
        $node = $node_map->getNamedItem($name);
        return $node == null ? null : $node->nodeValue;
    }
}
?>
