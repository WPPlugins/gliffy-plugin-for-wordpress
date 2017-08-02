<?php

class GliffyTest {

    public $testName; 


    public function testCode() {
        throw new Exception("testCode() not implemented for this test!");
    }


    //expects a curl object
    public function assertGoodStatusCode( $curl ) {

        $info = curl_getinfo($curl); 
        $httpCode = $info['http_code'];

        if( $httpCode != 200  ) {
            if( $httpCode == 0 ) { 
                $errorMessage = curl_error( $curl );    
                throw new Exception ("Curl had a problem: $errorMessage" );
             } else { 
                throw new Exception ("Assertion failed.  Expected HTTP status of $expected but got $actual");
             }
        } 
    }

    public function assertFileSize($file,$expectedSize,$tolerance) {
        $actualSize = filesize( $file );

        if( $actualSize < $expectedSize - $tolerance ||
            $actualsSize > $expectedSize + $tolerance ) {
            $message = "Expected file size of $expectedSize but got $actualsSize\n";
            $message = "File Content:\n" . file_get_contents( $file );
            throw new Exception ( $message );
         }
    }


    // Tests that the given URL resolves 
    // with a status 20X 
    public function assertURLisGood( $url ) {
        //Check to make sure the URL actually resolves to something
        $ch = curl_init( $url  );   
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $output = curl_exec($ch);       
        $info = curl_getinfo($ch); 

        //make sure output exists
        $this->assertTrue( !is_null( $output ) ); 

        //make sure we get a 200 status code 
        $this->assertTrue( $info['http_code'] == 200 || $info['http_code'] == 201 ); 

        return $output;
    }


    public function assertLessThan( $expected, $actual ) {
        if( $actual >= $expected ) {
            throw new Exception ("Assertion failed.  Expected $expected to be less than $actual");
        }
    }



    public function assertGreaterThan( $expected, $actual ) {
        if( $actual <= $expected ) {
            throw new Exception ("Assertion failed.  Expected $expected to be greater than $actual");
        }
    }

    public function assertStringsEqual( $expected, $actual ) {
        if( strcmp( $expected, $actual) != 0 ) {
            echo "\nExpected content:\n" . $expected . "\nbut got\n" . $actual; 
            throw new Exception( "assertion failed");
        }
    }

    public function assertTrue($result) { 
        if( !$result ) {
            throw new Exception( "assertion failed");
        }
    }

    public function assertFalse($result) { 
        if( $result ) {
            throw new Exception( "assertion failed");
        }
    }

    public function assertNotNull($result) { 
        if( is_null( $result ) ) {
            throw new Exception( "assertion failed");
        }
    }


    public function assertNull($result) { 
        if( !is_null( $result ) ) {
            throw new Exception( "assertion failed");
        }
    }



    public function runTest() {
        $startTime = date("U");
      

        $result = new GliffyTestResult();
        $result->testName = get_class($this);

        try {
            $this->testCode();   
        } catch ( Exception $e ) {
            $result->pass = false;       
            $result->errorMessage = "Exception in file " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage(); 
            $result->stackTrace = $e->getTraceAsString();
        } 

        $endTime = date("U");

        
        $result->testTime = $endTime - $startTime; 
       

        return $result;
    } 

} 


class GliffyTestResult {
    public $pass = true;
    public $errorMessage = null;
    public $testName = null;
    public $stackTrace = "";
    public $testTime = 0; 
}

?>
