<?php 




session_start();

# The username of the user that is adding diagrams
# We hard code this name here, but typically you'd want to use whatever unique user
# identifier you use in your system for the current user, such as an id, email, or username 
# Note that this detemines who owns newly created diagrams in the context
$testUsername = "testuser@gliffy.com"; 


# Include the Gliffy Client Library code.  Assumes we have a config.php object in place
$gliffyPath = '../../src'; 
set_include_path( get_include_path() . PATH_SEPARATOR . $gliffyPath); 
require_once("Gliffy.php"); 


# Create the gliffy context if it doesn't exists for this session
# Normall, you'll want to cache the Gliffy context for each user for performance,
# and to reduce the number of API calls
$gliffy = new Gliffy($testUsername); 

# Check to see if the 'addDiagram' button was pressed.  If it was, create a new diagram
if( isset( $_REQUEST['addDiagram'] ) &&  isset( $_REQUEST['diagramName'] )) {
    // Add diagram clicked
    $newDiagramName = $_REQUEST['diagramName']; 
    $newDiagramId = $gliffy->createDiagram($newDiagramName); 
    addDiagramToList( $newDiagramId ); 
} 

# Adds a new diagramId to the list, and store it in session
# Typically, you'll want to keep the diagram id's in your home 
# application database, since session doesn't last very long!
function addDiagramToList( $newDiagramId ) {
    if( !isset( $_SESSION['diagramArray'] ) ) {
        $_SESSION['diagramArray'] = array();
    }

    $_SESSION['diagramArray'][] = $newDiagramId; 
}

# Print out the HTML to render a diagram image, name, and edit link
function echoDiagram( $diagramId ) {
    global $gliffy;
    $diagramMetaData = $gliffy->getDiagramMetaData($diagramId);  
    $diagramImageURL = $gliffy->getDiagramAsURL($diagramId,Gliffy::MIME_TYPE_PNG); 
   
   
    echo "Diagram name: $diagramMetaData->name <br />";  
    echo "<img src=\"$diagramImageURL\" /> <br />"; 

    # It's a best priactice to wrap the editor in your own page for two reasons:
    # 1 - The OAuth security policy ensures that a given URL can never be requested more than once.  Wrapping the editor 
    #     will enable you to ensure that your users always get a valid and fresh URL
    # 2 - By wrapping the editor, your users will not be confused and think that they are leaving your site.  
    echo "<a href=\"gliffyWrapper.php?did=" .  $diagramId . "\" target=\"gliffy_" . $diagramId . "\">Edit Diagram</a>"; 
    echo "<hr />";
}


?>

<html>
<p>This is a very simple example of the Gliffy PHP Client Library.  Click 'Add diagram' to try it out.   

<br />
<br />


<form action="index.php" method="POST"> 
    Diagram Name: <input type="text" name="diagramName"/>
    <input type="submit" name="addDiagram" value="Add diagram"/>
</form> 

<hr /> 

<?php

    # Render all the diagrams we have
    if( isset( $_SESSION['diagramArray'] ) ) { 
        foreach( $_SESSION['diagramArray'] as $currentId ) { 
            echoDiagram( $currentId );
        }
    }

?>
</html>

