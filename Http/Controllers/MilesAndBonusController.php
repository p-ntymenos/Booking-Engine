<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use DB;
use SoapClient;

class MilesAndBonusController extends Controller
{
    
    protected $app, $request, $locale;
    
    protected $httpUsername, $httpPassword, $httpContext, $httpDrupalBaseUri;
    
    
    function __construct(Application $app, Request $request){
        
        //init app and request objects
        $this->app = $app;
        $this->request = $request;
        $this->locale = $app->getLocale();
        
        //init variables for drupal connection etc
        $this->httpUsername = Config::get('booking_celestyal.drupal_http_creds')['httpUsername'];
        $this->httpPassword = Config::get('booking_celestyal.drupal_http_creds')['httpPassword'];
        $this->httpDrupalBaseUri = Config::get('booking_celestyal.drupal_http_creds')['httpDrupalBaseUri'];
        $this->httpContext  = stream_context_create(array(
        'http' => array(
        'header'  => "Authorization: Basic " . base64_encode("$this->httpUsername:$this->httpPassword")
        )
        ));
        
    }
    
    public function checkFlyerId($pin){
        
        
        error_reporting ( E_ALL );
        ini_set ( 'display_errors', 1 );
        
        ini_set ( 'soap.wsdl_cache_enabled', '0' );
        ini_set ( 'soap.wsdl_cache_ttl', '0' );
        
        $wsdl = 'https://wsaeeuat.frequentflyer.aero/3rdParties-WS/thirdPartiesWs?wsdl';
        
        $options = array (
        'trace' => 1,
        'exception' => 0
        );
        $client = new \SoapClient ( $wsdl, $options );
        
        $param [ 'loginRequest' ] = array (
        'userName' => 'CELESTYAL',
        'password' => '12345678'
        );
        
        //CELESTYAL
        //CEL4TA73
        
        
        
        
        //some parameters to send
        $result = $client->login( $param );
        
        
        $request_param [ 'checkFfnValidityRequest' ] = array (
        'token'	  => $result->token,
        'flyerId' => $pin
        );
        
        $request = $client->checkFfnValidity( $request_param );
        
        return json_encode($request);
        
    }
    
    public function checkMBCurl($MemberID){
        $uri = 'http://demobooking.celestyalcruises.com/milesandbonus/?flyerid='.$MemberID;
        $dataRetr = file_get_contents($uri, false, $this->httpContext);
        
        return json_decode($dataRetr)->checkFfnValidityResponse;
    }
}