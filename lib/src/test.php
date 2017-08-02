<?php
//date_default_timezone_set("America/New_York");
//error_reporting(E_STRICT);

// Stolen from phpdocumentor
$a = explode('/', str_replace('\\', '/', dirname(realpath(__FILE__))));
array_pop($a);
$GLOBALS['_Gliffy_install_dir'] = join('/', $a);
// add my directory to the include path, and make it first, should fix any errors
if (substr(PHP_OS, 0, 3) == 'WIN')
    ini_set('include_path', $GLOBALS['_Gliffy_install_dir'].';'.ini_get('include_path'));
else
    ini_set('include_path', $GLOBALS['_Gliffy_install_dir'].':'.ini_get('include_path'));
require_once("Gliffy.php");

$rest = new GliffyREST("http://localhost:8080/gliffy/rest","BurnsODyneAPI","BurnsODyneSecret","montytoken",false,true,GliffyLog::LOG_LEVEL_DEBUG);
$response = $rest->get("/accounts/BurnsODyne/diagrams/100000398","text/xml",null);
if ($response->diagram_xml == null)
{
    echo "Expected diagram XML\n";
    echo $response->error . "\n";
}
else
{
    echo "Got Diagram XML:\n";
    echo $response->diagram_xml;
    echo "\nDONE\n";
}
$response = $rest->get("/accounts/BurnsODyne/folders","text/xml",null);
if ($response->folders == null)
{
    echo "Expected diagram XML\n";
    echo $response->error . "\n";
}
else
{
    echo "Got Folders:\n";
    foreach ($response->folders as $folder)
    {
        echo $folder->name . "\n";
    }
}
?>
