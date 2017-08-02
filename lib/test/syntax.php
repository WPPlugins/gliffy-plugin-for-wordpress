<?php



/**
  *  Tests to verify that the syntax of the Gliffy API PHP Client Library is correct 
  *  Usage: php syntax.php
  * 
  *
  *  Return codes:
  *      Success: 0
  *      Failed: 1
  */


$testResultsDirectory = "../test-results";
$testResultsFilename = $testResultsDirectory . "/syntaxCheck.xml";

//cleanup any previous test
unlink( $testResultsFilename );



$usage = "Usage: php syntax.php\n";
$clientRoot = "../src";


$failed = array();
$passed = array();
$time = 1;

//Here we would run the syntax checker

if ($handle = opendir( $clientRoot )) {



     /* This is the correct way to loop over the directory. */
     while (false !== ($file = readdir($handle))) {

        //only check syntax of files with php extension
        if( preg_match("/\.php$/",$file) == 1 ) { 
            $returnValue = checkSyntax( $file );
            if( $returnValue != 0 ) {
                $failed[] = $file;   
                echo(str_pad($file,40) . "FAIL\n");
            } else {
                $passed[] = $file; 
                echo(str_pad($file,40) . "PASS\n"); 
            }
        }      
      
    }

    closedir($handle);
}


createJunitOutput($passed,$failed,$time);

if( sizeof($failed) == 0  ) {
    echo "All src files PASSED php syntax check\n";
    exit(0); 
} else {
    echo "Some files FAILED php syntax check\n";
    exit(1);
}



/**
  * Checks the syntax of $file, relative to the global $clientRoot
  *
  * Return value: if 0, syntax ok, otherwise non-zero returned.
  */
function checkSyntax($file) {
    global $clientRoot;

    $output = array(); 

    exec( "php -l $clientRoot/$file",$output,$returnVar);

   
    return $returnVar;
}



function createJunitOutput($passed,$failed,$time) {

    global $testResultsFilename, $testResultsDirectory;
    $passedCount = sizeof( $passed ); 
    $failedCount = sizeof( $failed );
    $testCount = $passedCount + $failedCount;

    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n" . 
           "<testsuite errors=\"0\" skipped=\"0\" tests=\"$testCount\" time=\"$time\" failures=\"$failedCount\" name=\"com.gliffy.core.restapi.php.SyntaxTest\">";


    for( $i = 0 ; $i < $passedCount; $i++ ) {
        $testname = "testSyntax_" . $passed[$i];
        $xml = $xml . "<testcase time=\"0\" name=\"$testname\"/>";
    }


    for( $i = 0 ; $i < $failedCount; $i++ ) {
        $testname = "testSyntax_" . $failed[$i];
        $xml = $xml . "<testcase time=\"0\" name=\"$testname\">";

        $xml = $xml . "<failure type=\"gliffy.syntax.Failure\" message=\"" . $failed[$i] . " did not pass syntax test.\"/>\n";
        $xml = $xml . "</testcase>";

    } 

    $xml = $xml . "</testsuite>"; 
  
    //create the test results directory if it does not exist
    if( ! file_exists($testResultsDirectory) ) {
        mkdir($testResultsDirectory);
    }

    //write the test results
    $fh = fopen($testResultsFilename, 'w') or die("can't open file");
   
    fwrite($fh, $xml); 
    fclose($fh); 
    echo "JUnit test results written to $testResultsFilename\n";
       
}

?> 
