<?php

$gliffyPath = '../src'; 
set_include_path( get_include_path() . PATH_SEPARATOR . $gliffyPath); 
require_once("Gliffy.php");
require_once("UnitTestClasses.php");


/**
 * Creating test accounts is intended for Client Library developers only!
 * This function will create a test account which is attached to the email address used when initializing the 'Gliffy' object.
 *   
 * Don't invoke this with your regular account, otherwise you'll get tons of 
 * accounts attached to your email address.  Create a development-only account with a development only email address instead!
 * 
 */ 
function createTestAccount($gliffy) { 
    $gliffy->_logger->debug("createBasicAccount()"); 
   
    $gliffy->updateToken();
    $url = "/accounts.xml";
    $params = array( 'action' =>  'create',
                     'accountName' => 'Test Account' . rand(1,99999) . getmypid(),
                     'accountType' => 'Test' ); 

    $response = $gliffy->_rest->post($url,$params);
   
    $testAccount = GliffyTestAccount::from_response_xml( $response );

    return $testAccount; 
} 


$results = array();


/******************************************
 *                                        *
 * START of test setup/run section        *
 *                                        *
 ******************************************/

// Setup a test account
$gliffy = new Gliffy( $_GLIFFY_PHPTestUser ); 
$testAccount = createTestAccount($gliffy); 

// make a copy of the base configuration
$_GLIFFY_oauth_consumer_keyORIGINAL = $_GLIFFY_oauth_consumer_key;
$_GLIFFY_oauth_consumer_secretORIGINAL = $_GLIFFY_oauth_consumer_secret; 
$_GLIFFY_accountIDORIGINAL = $_GLIFFY_accountID;

// update the configuration with the values from the newly created account
$_GLIFFY_oauth_consumer_key = $testAccount->oAuthConsumerKey;
$_GLIFFY_oauth_consumer_secret = $testAccount->oAuthConsumerSecret;
$_GLIFFY_accountID = $testAccount->id;

// re-initialize the gliffy request object
$gliffy = new Gliffy( $_GLIFFY_PHPTestUser ); 


// Test Account operations
include 'GetAccountInfoTest.php'; 
$test =  new GetAccountInfoTest();
$results[] = $test->runTest(); 

// Test diagram operations
include 'DiagramsTest.php'; 
$diagramsTest = new DiagramsTest(); 
$results[] = $diagramsTest->runTest(); 


// Test folder operations
include 'FoldersTest.php'; 
$foldersTest = new FoldersTest(); 
$results[] = $foldersTest->runTest(); 

// Test users opererations
include 'UsersTest.php';
$usersTest = new UsersTest();
$results[] = $usersTest->runTest();


/******************************************
 *                                        *
 * END of test setup/run section          *
 *                                        * 
 ******************************************/ 


$time = 0;
$failedCount = 0;
$totalCount = sizeof( $results );

//count failed tests
for( $i = 0; $i < sizeof($results) ; $i++ ) {
    if( !$results[$i]->pass ) {
        $failedCount++;
    }
}    



$junitResult =  "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n" . 
                "<testsuite errors=\"0\" skipped=\"0\" tests=\"$totalCount\" time=\"$time\" failures=\"$failedCount\" name=\"com.gliffy.core.restapi.php.PHPTestSuite\">";

//print out test results
for( $i = 0; $i < sizeof($results) ; $i++ ) {
    $currentResult = $results[$i];
    if( !$currentResult->pass ) {
        $junitResult = $junitResult . "<testcase time=\"$currentResult->testTime\" name=\"$currentResult->testName\">\n"; 
        $junitResult = $junitResult . "<failure type=\"gliffy.syntax.Failure\" message=\"" . $currentResult->errorMessage . "\">\n"; 
        $junitResult = $junitResult . $currentResult->stackTrace . "\n"; 
        $junitResult = $junitResult . "</failure>"; 
        $junitResult = $junitResult . "</testcase>"; 
        echo "\nFailed test:" . $currentResult->testName . " "  . $currentResult->errorMessage;
        echo "\n$currentResult->stackTrace";
    } else { 
        echo "\n" . $currentResult->testName . " PASSED in "  . $currentResult->testTime . " second(s)";
        $junitResult = $junitResult . "<testcase time=\"$currentResult->testTime\" name=\"$currentResult->testName\"/>";
    }
}


$junitResult = $junitResult . "</testsuite>"; 

#
  
$testResultsDirectory = "../test-results";
$testResultsFilename = $testResultsDirectory . "/PHPTests-" . getmypid() . ".xml"; 



//write test results
$fh = fopen($testResultsFilename, 'w') or die("can't open file");

fwrite($fh, $junitResult); 
fclose($fh); 

if( $failedCount == 0 ) { 
    echo "\n" . "All tests PASSED!\n";
    exit(0);
} else {
    echo "\n" . $failedCount . " failed tests out of " . $totalCount . " total";
    exit(1);
}
echo "\nJUnit test results written to $testResultsFilename\n";
       




?>
