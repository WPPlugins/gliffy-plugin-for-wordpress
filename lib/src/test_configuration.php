<?php
/** This script validates your configuration to ensure that your config.php is
 * set up correctly.  See {@link config_example.php} for how to create that file.
 * <p>
 * The simplest way to use this script is via the command line:
 * <pre>
 *    php test_configuration.php <i>username</i>
 * </pre>
 * where <i>username</i> was the username you chose when creating your account.  For
 * individual accounts, this is the name part of your email address.
 * </p>
 * <p>
 * If you cannot execute PHP via the command line, you can access it via:
 * <pre>
 *   http://www.yourdomain.com/location/you/extracted/gliffy/test_configuration.php?username=<i>username</i>
 * </pre>
 * </p>
 * @package Gliffy
 */
error_reporting(E_ALL);

require_once("Gliffy.php");

$created_a_gliffy = false;

$error_start = "<font color='red'><pre>\n";
$error_end = "</pre></font>\n";
$list_start = "<ul>\n";
$list_end = "</ul>\n";
$list_item_start = "<li> ";
$list_item_end = "</li>\n";
$support_email = "<a href='mailto:support@gliffy.com'>support@gliffy.com</a>";
try
{
    $username = null;
    if (isset($argv))
    {
        $username = $argv[1];
    }
    if ($username)
    {
        // Assume command line usage means no HTML
        $error_start = "ERROR\n";
        $error_end = "\n";
        $list_start = "\n";
        $list_end = "\n";
        $list_item_start = "";
        $list_item_end = "\n";
        $support_email = "support@gliffy.com";
    }
    else if (!$username)
    {
        if (isset($_GET) && isset($_GET['username']) )
        {
            $username = $_GET['username'];
        }
    }
    if (!$username)
    {
        error_log("Can't determine username for connecting to Gliffy");
        echo $error_start;
        echo "Cannot find username as a command line argument, in the session under 'username', or as a GET request parameter value for 'username'\n";
        echo $error_end;
    }
    else
    {
        echo "Your configuration:\n";
        echo $list_start;
        echo $list_item_start .  "_GLIFFY_root = " .  $_GLIFFY_root . $list_item_end;
        echo $list_item_start .  "_GLIFFY_restRoot = " .  $_GLIFFY_restRoot . $list_item_end;
        echo $list_item_start .  "_GLIFFY_appDescription = " .  $_GLIFFY_appDescription . $list_item_end;
        echo $list_item_start .  "_GLIFFY_accountID = " .  $_GLIFFY_accountID . $list_item_end;
        echo $list_item_start .  "_GLIFFY_oauth_consumer_key = " .  $_GLIFFY_oauth_consumer_key . $list_item_end;
        echo $list_item_start .  "_GLIFFY_oauth_consumer_secret = " .  $_GLIFFY_oauth_consumer_secret . $list_item_end; 
        echo $list_item_start .  "_GLIFFY_strictREST = " .  ($_GLIFFY_strictREST ? "true" : "false"). $list_item_end; 
        echo $list_item_start .  "_GLIFFY_logLevel = " .  $_GLIFFY_logLevel. $list_item_end;
        echo $list_item_start .  "_GLIFFY_restRootUsername = " .  $_GLIFFY_restRootUsername . $list_item_end;
        echo $list_item_start .  "_GLIFFY_restRootPassword = " .  $_GLIFFY_restRootPassword . $list_item_end;
        echo $list_end;

        if ( !preg_match("/^http:\/\//",$_GLIFFY_root) &&
            !preg_match("/^https:\/\//",$_GLIFFY_root) )
            throw new Exception('$_GLIFFY_root must start with http:// or https://');

        $gliffy = new Gliffy($username);
        $created_a_gliffy = true;
        $rootFolder = $gliffy->getFolders();
        if (  !is_null( $rootFolder ) )
        {
            echo "Top Level folders are:";
            echo $list_start;
            foreach ($rootFolder->children as $folder)
            { 
                echo $list_item_start;
                echo $folder->name;
                if ($folder->default)
                {
                    echo " (default folder)";
                }
                echo $list_item_end;
            }
            echo $list_end;
            echo "Your configuration appears to be correct\n";
        }
        else
        {
            echo $error_start;
            echo "Didn't get any folders back, but also didn't get an exception.  Contact ";
            echo $support_email;
            echo "\n";
            echo "Include your account name, username, and API Key\n";
            echo $error_end;
        }
    }
}
catch (Exception $e)
{
    echo $error_start;
    echo "There is a problem with your configuration\n";
    if ($created_a_gliffy)
    {
        echo "Able to connect to Gliffy however, the list of folders couldn't be retrieved\n";
    }
    else
    {
        echo "Unable to make an initial connection to Gliffy\n";
    }
    echo "In " . $e->getFile() . ", on line #" . $e->getLine() . "\n";
    echo $e->getMessage() . "\n";
    echo "\n";
    echo $error_end;
}
?>
