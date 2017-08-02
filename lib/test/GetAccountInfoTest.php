<?php 

$gliffyPath = '../src'; 
set_include_path( get_include_path() . PATH_SEPARATOR . $gliffyPath); 
require_once("Gliffy.php");


class GetAccountInfoTest extends GliffyTest { 

    function testCode() {
        $gliffy = new Gliffy("testuser@gliffy.com"); 
        $account = $gliffy->getAccountInfo(); 
    
        $this->assertTrue( is_string( $account->name ) );
        $this->assertTrue( is_string( $account->type ) );
        $this->assertTrue( is_int( $account->max_users ) );
        $this->assertTrue( is_string( $account->terms ) );
        $this->assertTrue( is_int( $account->expiration_date ) ); 
    }

   

}

?>
