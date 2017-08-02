<?php

/** Interacts with Gliffy REST protocol.
 * This class is de-coupled from the api key, shared secret, api URL root and user token and simply
 * handles the arrangment and signing of requests, sending them to gliffy and receiving them back.  
 * The results are all parsed into a {@link GliffyResponse}.
 * @package Gliffy
 * @example Example1.php
 */
class GliffyREST
{
    private $_binary_mime_types = array(
        "image/jpg" => true,
        "image/png" => true,
        "image/jpeg" => true
        );
    private $_base; 

    private $_oauth_token = null;
    private $_oauth_token_secret = null;

    private $_tunnel;
    private $_http_errors;
    private $_logger;
    private $_keep_content;
    private $_curl;

    private $_strict_rest;

    //private $_api_key;
    //private $_shared_secret;
    private $_oauth_consumer_key;
    private $_oauth_consumer_secret;
 


    /** Create a GliffyREST object to make REST calls to Gliffy.  The recommended usage of this
     * is to ensure proper values for globals (see {@link config_example.php}) and to create this as needed
     * using the user's login token.
     *
     * @param string the user token assigned to the current application user (may be null for certain requests).
     * @param string the API Key assigned to you by Gliffy
     * @param string the secret key assigned to you by Gliffy
     * @param boolean if false, tells Gliffy not to send HTTP errors, but to
     * encode errors as gliffy response (see {@link GliffyError}).  Default false.
     * @param boolean if true, DELETE and PUT calls are tunneled over POST.  Default true.
     */
    public function GliffyREST(
        $token,
        $oauth_consumer_key=null,
        $oauth_consumer_secret=null,
        $strict_rest=null,
        $tunnel=null)
    {
        global $_GLIFFY_restRoot;
        global $_GLIFFY_oauth_consumer_key;
        global $_GLIFFY_oauth_consumer_secret;
        global $_GLIFFY_strictREST;
        global $_GLIFFY_tunnel;
        global $_GLIFFY_logLevel;
        global $_GLIFFY_logTo_error_log;
        global $_GLIFFY_restRootUsername;
        global $_GLIFFY_restRootPassword;
        global $_GLIFFY_keepRawContent;

        $this->_base = $_GLIFFY_restRoot;
        //$this->_api_key = $api_key == null ? $_GLIFFY_apiKey : $api_key;
        $this->_oauth_consumer_key = $oauth_consumer_key == null ? $_GLIFFY_oauth_consumer_key : $oauth_consumer_key;
//        $this->_token = $token;
        //$this->_shared_secret = $shared_secret == null ? $_GLIFFY_sharedSecret : $shared_secret; 
        $this->_oauth_consumer_secret = $oauth_consumer_secret == null ? $_GLIFFY_oauth_consumer_secret : $oauth_consumer_secret;
        $this->_tunnel = $tunnel == null ? $_GLIFFY_tunnel : $tunnel;
        $this->_strict_rest = $strict_rest == null ? $_GLIFFY_strictREST : $strict_rest;
        $this->_logger = new GliffyLog($_GLIFFY_logLevel,$_GLIFFY_logTo_error_log); 
        $this->_keep_content = $_GLIFFY_keepRawContent;
        $this->_curl = $this->initialize_curl();

        $this->_logger->debug("Created GliffyREST for base " . $this->_base);
    }

    /** Update the token to use for requests.
     * @param string the new token to use in requests
     */
    public function updateToken($token, $tokenSecret = null) {
        $this->_logger->debug("UPDATE TOKEN!!!"); 

        if( $token == null ) { 
            $this->_logger->debug("Updating a token to null?"); 
        }

//        $this->_oauthTokenCredentials = $token;
        $this->_oauth_token = $token;
        $this->_oauth_token_secret = $tokenSecret;
    }

    /** Performs a get request 'text/xml' with no parameters.  
     *
     * Basically a convienience method for the full get method
     * @param string the url to GET
     * @return GliffyResponse whatever was returned by the other get
     */
    public function get_simple($url)
    { 
        return $this->get($url,"text/xml",null);
    }

    /** Send a GET request to the given URL
     * @param string the URL, relative to the base URL given to the constructor, to GET
     * @param string the mime type of how you would like the results.  If the resource doesn't support
     * this mime type, or if there is some error on the Gliffy side, you will get XML back.
     * @param array an associative array of parameters to include in the request
     * @param boolean if true, the GET is not performed; the URL is simply returned
     * @param array of additional headers to pass into request
     * @return GliffyResponse Whatever gets sent back from the server
     */
    public function get($url,$mime_type,$params,$url_only=false,$headers=array())
    { 
        $url = preg_replace("/\\s/","+",$url); 

        //If the URL starts with http, then we should use the base that's there 
        if( strpos( $url, "http") === 0 && strpos( $url, "http") == 0 ) { 
            $entire_url = $url;
        } else {
            $entire_url = $this->_base . $url;
        } 
    
        $this->_logger->debug("GET for $entire_url as " . $mime_type);

    
        $mime_type = strtolower($mime_type);
        $headers[] = "Accept: $mime_type";
        $params = $this->init("GET",$params,$headers); 


        $requestData = $this->sign($entire_url,$params,'GET');

        $binary = false;
        if (array_key_exists($mime_type,$this->_binary_mime_types))
        {
            $binary = true;
            curl_setopt($this->_curl,CURLOPT_BINARYTRANSFER,true);
        }

        $signedUrl = $requestData['url'];

        $this->_logger->debug("Entire Signed URL: " . $signedUrl );
        curl_setopt( $this->_curl,CURLOPT_URL,$signedUrl );

        if ($url_only)
            return $signedUrl;

        $results = curl_exec($this->_curl);

        $status = $this->get_status();

        if ( ($status == 200) || ($status == 201) )
        {


            if ($binary) {
                $this->_logger->debug("Binary content recieved"  );
              
                return $results;
            } 
            else {
                $response = simplexml_load_string($results); 
                $this->_logger->debug($results);
            }



            //if there is not 'status' attribute, don't check the <response> node since it's not there
            if( !is_null ( $response['success'] ) ) {
                //throws an exception if we have a problem 
                $this->checkResponse( $response ); 
            }



            return $response;
        }
        else
        {
            
            return $this->create_error($status);
        }
    }

    /** Send a POST request to the given URL
     * @param string the URL, relative to the base URL given to the constructor, to POST
     * @param array parameters to include in the POST.
     * @param array headers to include in the POST.
     * @return GliffyResponse Whatever gets sent back from the server (either XML or a blank string, most likely)
     */
    public function post($url,$params,$headers=array(),$useHttps=false)
    {
        global $_GLIFFY_verifySSLCert;

        $url = preg_replace("/\\s/","+",$url);


        if( $useHttps ) { 
            $urlBase =  str_replace( "http://" , "https://", $this->_base );   
        } else { 
            $urlBase =  $this->_base;
        } 

        $entire_url = $urlBase . $url; 

        $this->_logger->debug("POST for $entire_url");


        $mime_type = array_key_exists("rest_representation_form",$params) ? $params['rest_representation_form'] : null;
        if (!$mime_type)
            $headers[] ="Accept: text/xml";

        $params = $this->init("POST",$params,$headers); 
        
        $requestData = $this->sign($entire_url,$params,'POST'); 

        curl_setopt($this->_curl,CURLOPT_URL, $requestData['url']  );
        curl_setopt($this->_curl,CURLOPT_POSTFIELDS, $requestData['postdata']  ); 
        
        if( $_GLIFFY_verifySSLCert ) { 
            curl_setopt($this->_curl,CURLOPT_SSL_VERIFYPEER, true );
            curl_setopt($this->_curl,CURLOPT_SSL_VERIFYHOST, true );
        } else {
            curl_setopt($this->_curl,CURLOPT_SSL_VERIFYPEER, false  );
            curl_setopt($this->_curl,CURLOPT_SSL_VERIFYHOST, false  );
        }

        

        $binary = false;
        if (array_key_exists($mime_type,$this->_binary_mime_types))
        {
            $binary = true;
            curl_setopt($this->_curl,CURLOPT_BINARYTRANSFER,true);
        }

        $results = curl_exec( $this->_curl);

        $status = $this->get_status();

        if ( ($status == 200) || ($status == 201) )
        {
            $this->_logger->debug("MIME: $mime_type");
            if ($binary)
                $this->_logger->debug("Binary content recieved");
            else
                $this->_logger->debug("Respons:\n" . $results); 
          
            $response = simplexml_load_string( $results ); 


            $this->checkResponse($response);

            return $response;
        }
        else
        {
            $this->_logger->debug("Got fail status");

            return $this->create_error($status);
        }
    }

    /** Call this when are you done with an instance of this class
     */
    public function close()
    {
        curl_close($this->_curl);
    }

    /** Inits curl and the parameters list.
     * @param string method string the method to use.
     * @param array the request parameters.  This is a map of parameter name as string to value as string 
     * @param headers any headers to set.  Note that this is an array of strings with the headers formatted for CURL, e.g. [0] => "Accept: text/xml".
     * @return the request parameters, unsigned.
     */
    private function init($method, $params=null, $headers=null)
    {
        global $_GLIFFY_sslAcceptsUnTrustedCertificates;
        global $_GLIFFY_requestTimeout;
        // reset curl
        if (!$this->_curl)
        {
            $this->_curl = $this->initialize_curl();
        }

        // no way to actually unset this, it seems
        curl_setopt($this->_curl,CURLOPT_CUSTOMREQUEST ,$method);
        curl_setopt($this->_curl,CURLOPT_BINARYTRANSFER,false);
        curl_setopt($this->_curl,CURLOPT_HTTPGET,false);
        curl_setopt($this->_curl,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($this->_curl,CURLOPT_POST,false);
        curl_setopt($this->_curl,CURLOPT_POSTFIELDS,null);
        curl_setopt($this->_curl,CURLOPT_PUT,false);
        curl_setopt($this->_curl,CURLOPT_URL,null);
        curl_setopt($this->_curl,CURLOPT_SSL_VERIFYPEER,$_GLIFFY_sslAcceptsUnTrustedCertificates ? FALSE : TRUE);
        curl_setopt($this->_curl,CURLOPT_SSL_VERIFYHOST,$_GLIFFY_sslAcceptsUnTrustedCertificates ? 1 : 2);
        curl_setopt($this->_curl,CURLOPT_TIMEOUT, $_GLIFFY_requestTimeout  );

        if ($this->_logger->level() == GliffyLog::LOG_LEVEL_DEBUG)
            curl_setopt($this->_curl,CURLOPT_VERBOSE,true);
        else
            curl_setopt($this->_curl,CURLOPT_VERBOSE,false);

        curl_setopt($this->_curl,CURLOPT_RETURNTRANSFER,true);
        if ($params == null)
            $params = array();
        $params["oauth_consumer_key"] = $this->_oauth_consumer_key;
        //$params["rest_representation_form"] = "text/xml";
//        if ($this->_token != null)
//            $params["token"] = $this->_token;
//        if ($this->_oauth_token != null)
//            $params["token"] = $this->_oauth_token;


        if ($this->_tunnel)
        {
            if ( ($method != null) && ($method != "POST") && ($method != "GET"))
            {
                curl_setopt($this->_curl,CURLOPT_POST,true);
                $params['rest_request_method'] = $method;
            }
        }
        else
        {
            if ($method == "PUT")
                curl_setopt($this->_curl,CURLOPT_PUT,true);
            else if ($method == "DELETE")
                curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST ,"DELETE");
            else if ($method == "POST")
                curl_setopt($this->_curl,CURLOPT_POST,true);
            else if ($method == "GET")
                curl_setopt($this->_curl,CURLOPT_HTTPGET,true);
        }
        if ($this->_strict_rest)
        {
            $params['rest_strict'] = "true";
        }
        return $params;
    }

    /** Signs the request.


     * @param string the request url
     * @param array hash of array parameters
     * @param string HTTP method for this request (ie GET, POST, etc)
     * @return a URL that is signed
     */
    private function sign($url,$params,$httpMethod)
    {
        $sign_me = $url;

        $this->_logger->debug("Signing '$sign_me'");

        $oauthConsumer = new OAuthConsumer( $this->_oauth_consumer_key , $this->_oauth_consumer_secret,null); 

        $oauthToken = null; 

        if( $this->_oauth_token != null ) {
//        if( $this->_oauthTokenCredentials != null ) {
//            $oauthToken = new OAuthToken( (string)$this->_oauthTokenCredentials->{'oauth-token'}, (string)$this->_oauthTokenCredentials->{'oauth-token-secret'} );
           // $oauthToken = new OAuthToken( $this->_oauthTokenCredentials->{'oauth-token'}, $this->_oauthTokenCredentials->{'oauth-secret'}); 
//            $this->_logger->debug("Created OAuthToken object with token value: " .  $oauthToken->key . " and secret value: " . $this->_oauthTokenCredentials->{'oauth-token-secret'} );
            $oauthToken = new OAuthToken($this->_oauth_token, $this->_oauth_token_secret);
            $this->_logger->debug("Created OAuthToken object with token value: " .  $oauthToken->key . " and secret value: " . $oauthToken->secret );
           
        } else {
            $this->_logger->debug("Not using an oauth_token in this request");
        } 

        $oauthRequest = OAuthRequest::from_consumer_and_token($oauthConsumer, $oauthToken, $httpMethod, $sign_me, $params);
        $oauthRequest->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $oauthConsumer, $oauthToken);
        $this->_logger->debug("Signed URL is:" . $oauthRequest->to_url() ); 
        

        if( strcmp( $httpMethod, "GET" ) == 0 ) { 
            $requestData = array( "url" => $oauthRequest->to_url() );
        } else {
            //This is POST 
            $requestData = array( "url" => $url,
                                  "postdata" => $oauthRequest->to_postdata() );
        } 

        return $requestData;
    }

    private function create_query_string($params)
    {
        $string = "";
        foreach ($params as $key => $value)
        {
            $string .= "$key=" . urlencode($value) . "&";
        }
        return $string;
    }

    private function get_status()
    {
        $status = curl_getinfo($this->_curl,CURLINFO_HTTP_CODE);

        if( $status == 0 ) {
            $errorMessage = curl_error( $this->_curl );    
            throw new Exception ("Curl had a problem: $errorMessage" );
        }

        $this->_logger->debug("Got status " . $status);
        $status = intval($status);
        return $status;
    }

    private function checkResponse($response) {

        //check the status code in the xml response 
        $this->_logger->debug("response success was " . $response['success'] );
        if( strcmp( (string)$response['success'], 'true' ) != 0 ) { 
            //ruh-ro

            $statusCode =  (string) $response->error['http-status'];
            $errorMessage = (string) $response->error;

            $message = "Got error status code: " . $statusCode . " and message: " . $errorMessage;

            throw new GliffyException( $message ); 
        } 

    }

    /** Given an HTTP Status, creates a GliffyResponse that contains
     * an error with that status code.  This is useful for
     * requests that support HTTP.
     */
    private function create_error($status)
    { 
        $message = "ERROR:  Got HTTP status code of $status when we expected a 20X"; 
        throw new GliffyException( $message ); 
        return null;
    }

    private function initialize_curl()
    {
        global $_GLIFFY_restRootUsername;
        global $_GLIFFY_restRootPassword;

        $curl = curl_init();
        $this->_logger->debug("Creating a GliffyREST with " . $this->_base);
        if ( ($_GLIFFY_restRootUsername != null) || ($_GLIFFY_restRootPassword != null) )
        {
            $this->_logger->debug("Setting authentication mode with user == '$_GLIFFY_restRootUsername' and password == '$_GLIFFY_restRootPassword'");
            curl_setopt($curl,CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl,CURLOPT_USERPWD,($_GLIFFY_restRootUsername == null ? "" : $_GLIFFY_restRootUsername) . ":" . ($_GLIFFY_restRootPassword == null ? "" : $_GLIFFY_restRootPassword) );
        }
        return $curl;
    }
}

?>
