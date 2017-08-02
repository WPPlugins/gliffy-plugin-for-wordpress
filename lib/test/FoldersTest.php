<?php 

$gliffyPath = '../src'; 
set_include_path( get_include_path() . PATH_SEPARATOR . $gliffyPath); 
require_once("Gliffy.php");


class FoldersTest extends GliffyTest { 

    function testCode() {
        $gliffy = new Gliffy("testuser@gliffy.com"); 
        $account = $gliffy->getAccountInfo(); 
        
        //create two folders
        $firstFolderPath = "ROOT/My first folder";
        $secondFolderPath = "ROOT/My second folder"; 
        $gliffy->createFolder( $firstFolderPath );
        $gliffy->createFolder( $secondFolderPath );

        // verify the folders were created  
        $foundFirstFolder = false;
        $foundSecondFolder = false;

        $rootFolder = $gliffy->getFolders(); 

        foreach ($rootFolder->children as $f) {
            echo "\n\nName:$f->name: ";
            echo "\nFirstFolderName:$firstFolderPath: ";

            if( strcmp( $f->path,$firstFolderPath ) == 0 ) { 
                $foundFirstFolder = true; 
            } else if( strcmp( $f->path, $secondFolderPath ) == 0 ) { 
                $foundSecondFolder = true; 
            }       
        } 

        //verify that we found both new folders
        $this->assertTrue( $foundFirstFolder );
        $this->assertTrue( $foundSecondFolder ); 

        //create two diagrams
        $firstDiagramName ="Super Duper Flow Chart";
        $secondDiagramName = "Best Floorplan evar!";
        $firstDiagramId = $gliffy->createDiagram($firstDiagramName);
        $secondDiagramId = $gliffy->createDiagram($secondDiagramName); 

        //place both diagrams in the first folder, verify that the diagrams are in the folder 
        $gliffy->moveDiagram( $firstDiagramId, $firstFolderPath );  
        $gliffy->moveDiagram( $secondDiagramId, $firstFolderPath );  

        // Verify that both diagrams are in the first folder, and nothing else is 
        $diagramsInFirstFolder = $gliffy->getDiagrams( $firstFolderPath );      
        $foundFirstDiagram = false;
        $foundSecondDiagram = false; 
        $foundUnknownDiagram = false; 


        foreach ($diagramsInFirstFolder as $d) {
            echo "\n\ndname: $d->name\nfirstDiagramname:$firstDiagramName";


            if( strcmp( $d->name, $firstDiagramName ) == 0 ) {
                $foundFirstDiagram = true;
            } else if( strcmp( $d->name, $secondDiagramName ) == 0 ) {
                $foundSecondDiagram = true;
            } else { 
                $foundUnknownDiagram = true; 
            }
        } 

        $this->assertTrue( $foundFirstDiagram );
        $this->assertTrue( $foundSecondDiagram );
        $this->assertTrue( !$foundUnknownDiagram ); 

        $diagramsInSecondFolder = $gliffy->getDiagrams( $secondFolderPath );      
        $this->assertTrue( sizeof( $diagramsInSecondFolder ) == 0 ); 

        //move one diagram to the second folder 
        $gliffy->moveDiagram( $secondDiagramId, $secondFolderPath );  

        $diagramsInFirstFolder = $gliffy->getDiagrams( $firstFolderPath );      
        $foundFirstDiagram = false;
        $foundSecondDiagram = false; 
        $foundUnknownDiagram = false; 

        foreach ($diagramsInFirstFolder as $d) {
            if( strcmp( $d->name, $firstDiagramName ) == 0 ) {
                $foundFirstDiagram = true;
            } else if( strcmp( $d->name, $secondDiagramName ) == 0 ) {
                $foundSecondDiagram = true;
            } else { 
                $foundUnknownDiagram = true; 
            }
        } 

        //Verify that only the first diagram is in the first folder
        $this->assertTrue( $foundFirstDiagram );
        $this->assertTrue( !$foundSecondDiagram );
        $this->assertTrue( !$foundUnknownDiagram ); 
        $this->assertTrue( sizeof( $diagramsInFirstFolder ) == 1 ); 

        $diagramsInSecondFolder = $gliffy->getDiagrams( $secondFolderPath );      
        $foundFirstDiagram = false;
        $foundSecondDiagram = false; 
        $foundUnknownDiagram = false; 

        foreach ($diagramsInSecondFolder as $d) {
            if( strcmp( $d->name, $firstDiagramName ) == 0 ) {
                $foundFirstDiagram = true;
            } else if( strcmp( $d->name, $secondDiagramName ) == 0 ) {
                $foundSecondDiagram = true;
            } else { 
                $foundUnknownDiagram = true; 
            }
        } 

        // Verify that the second diagam is the only diagram in the second folder
        $this->assertTrue( !$foundFirstDiagram );
        $this->assertTrue( $foundSecondDiagram );
        $this->assertTrue( !$foundUnknownDiagram ); 
        $this->assertTrue( sizeof( $diagramsInSecondFolder ) == 1 ); 

        //move the first diagram to the root folder
        $gliffy->moveDiagram( $firstDiagramId, 'ROOT' );  

        //delete the first folder
        $gliffy->deleteFolder( $firstFolderPath ); 
       
        $foundFirstFolder = false;
        $foundSecondFolder = false;

        $rootFolder = $gliffy->getFolders(); 

        foreach ($rootFolder->children as $f) { 

            echo "\n\nfpath: $f->path\nfirstFolderPath:$firstFolderPath";
            if( strcmp( $f->path, $firstFolderPath ) == 0 ) {
                $foundFirstFolder = true; 
            } else if( strcmp( $f->path, $secondFolderPath ) == 0 ) {
                $foundSecondFolder = true; 
            }       
        } 

        //verify that the first folder was deleted
        $this->assertTrue( !$foundFirstFolder );
        $this->assertTrue( $foundSecondFolder ); 


        // get the diagrams that exist at ROOT folder
        $diagramsInRoot = $gliffy->getDiagrams('ROOT'); 
        $diagramsInSecondFolder = $gliffy->getDiagrams($secondFolderPath); 

        $foundFirstDiagram = false;
        $foundSecondDiagram = false; 

        foreach ($diagramsInRoot as $d) {
            if( strcmp( $d->name, $firstDiagramName ) == 0 ) {
                $foundFirstDiagram = true;
            } else if( strcmp( $d->name, $secondDiagramName ) == 0 ) {
                $foundSecondDiagram = true;
            } 
        } 

        //verify that the first diagram was moved to root
        $this->assertTrue( $foundFirstDiagram ); 

        $foundFirstDiagram = false;
        $foundSecondDiagram = false; 

        foreach ($diagramsInSecondFolder as $d) { 
             echo "\n\ndname: $d->name\nsecondDiagramName:$secondDiagramName";

             if( strcmp( $d->name, $secondDiagramName ) == 0 ) {
                $foundSecondDiagram = true;
            } 
        } 

        //verify that the second diagram didn't move
        $this->assertTrue( $foundSecondDiagram ); 

        //delete the user token
        $gliffy->deleteToken(); 
    } 


}

?>
