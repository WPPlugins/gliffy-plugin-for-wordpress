<?php 

$gliffyPath = '../src'; 
set_include_path( get_include_path() . PATH_SEPARATOR . $gliffyPath); 
require_once("Gliffy.php");


class UsersTest extends GliffyTest { 

    function testCode() { 
        global $_GLIFFY_PHPTestUser; 


        //create admin user gliffy context
        $adminUsername = $_GLIFFY_PHPTestUser;
        $adminGliffy = new Gliffy($adminUsername); 

        //create two users by obtaining gliffy context for them. 
        $userNameOne = "testuser1";
        $userNameTwo = "testuser2";
        $userOneGliffy = new Gliffy($userNameOne);
        $userTwoGliffy = new Gliffy($userNameTwo); 

        //create two folders
        $firstFolderPath = "ROOT/My first user folder";
        $secondFolderPath = "ROOT/My second user folder"; 
        $adminGliffy->createFolder( $firstFolderPath );
        $adminGliffy->createFolder( $secondFolderPath ); 

        //update data on user 1
 
        $userEmailOne = "testuser" . rand(1,99999) . "@gliffy.com";
        $adminGliffy->updateUser( $userNameOne, false,$userEmailOne , '123456'); 

        //get the user 1 data via the API
        $allUsers = $adminGliffy->getUsers();
        $userOneData = null;
        foreach( $allUsers as $user ) {
            echo "Username:$user->username  userNameOne:$userNameOne";
            if( strcmp( $user->username, $userNameOne ) == 0 ) {
                $userOneData = $user;
            }
        }

        //verify that the user 1 email is set as expected
        $this->assertStringsEqual( $userEmailOne, $userOneData->email );

        //verify that the user 1 is NOT an admin
        $this->assertFalse( $userOneData->is_admin );

        //set permissions on folder 1 such that only user 1 may access it
        $adminGliffy->addUserToFolder($firstFolderPath,$userNameOne); 
        $adminGliffy->removeUserFromFolder($firstFolderPath,$userNameTwo); 

        //create a new diagram and add it to folder 1
        $diagramOneId = $adminGliffy->createDiagram( "My Happy Flowchart");   
        $adminGliffy->moveDiagram( $diagramOneId,$firstFolderPath ); 

        //as user 1, attempt to get diagram 1 (should be ok)
        $data = $userOneGliffy->getDiagramAsXML( $diagramOneId );
        $this->assertNotNull( $data );

        //as user 2, attempt to get diagram 1 (should be ok because this is a basic account, and all diagrams are public) 
        $data = $userTwoGliffy->getDiagramAsXML( $diagramOneId ); 
        $this->assertNotNull( $data );

        //update user 1 so they are an admin on the account 
        $adminGliffy->updateUser( $userNameOne,true); 

        //get admins
        $admins = $adminGliffy->getAdmins(); 

        //get admins on the account
        $foundUserOne = false;
        $foundAdmin = false;
        $foundUnexpectedUser = false;

        foreach( $admins as $user ) {
            if( strcmp( $user->username, $userNameOne ) == 0 ) {
                $foundUserOne = true;
            } else if (strcmp( $user->username, $adminUsername ) == 0 ) {
                $foundAdmin = true;
            } else {
                $foundUnexpectedUser = true;
            } 
        }

        //verify admins are as expected
        $this->assertTrue( $foundUserOne );
        $this->assertTrue( $foundAdmin );
        $this->assertFalse( $foundUnexpectedUser ); 

        //delete user 1
        $adminGliffy->deleteUser( $userNameOne );

        //Get the list of users 
        $allUsers = $adminGliffy->getUsers();
        $foundUserOne = false;
        foreach( $allUsers as $user ) {
         
            if( strcmp( $user->username, $userNameOne ) == 0 ) {
                $userOneFound = true;
            }
        }
        
        //verify user 1 is deleted
        $this->assertFalse( $foundUserOne ); 


        //create user 3 
        $userNameThree = "testuser3";
        $adminGliffy->addUser($userNameThree);





        //verify user 3 is created
        //Get the list of users 
        $allUsers = $adminGliffy->getUsers();
        $foundUserThree = false;
        foreach( $allUsers as $user ) { 
            if( strcmp( $user->username, $userNameThree ) == 0 ) {
                $foundUserThree = true;
            }
        }
        
        //verify user 3 is created
        $this->assertTrue( $foundUserThree ); 


        //get folders that user three has access to
        $userThreeFolders = $adminGliffy->getUserFolders( $userNameThree );

        $foundChildFolder = false;
        //the root folder is always returned at the top level 
        foreach ( $userThreeFolders->children as $folder ) { 
            echo "\nUserThreeFolder: $folder->name";
            $foundChildFolder = true;
        }

        //user three should not have access to any folders, since we didn't give this user permission to any yet
        $this->assertFalse( $foundChildFolder ); 

        //Destroy the user tokens to log the users out 
        $adminGliffy->deleteToken();
      
        $userTwoGliffy->deleteToken();
    } 


}

?>
