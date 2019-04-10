<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use App\Models\UserSessions;
use App\Models\Admin\Cart;
use App\Models\Admin\Logs;
use Unirest;
use Response;
use File;


class ReservationController extends Controller
{
    public $reservationUri;
    protected $MarketInfo, $BookingContactName;
    
    
    /**
    * stdClass Object
    * (
    *   [MarketCode] => GRC
    *   [Currency] => EUR
    *   [AgencyId] => 0000012
    *   [CurrencySymbol] => €
    *   )
    *
    */
    public function __construct(){
        $this->reservationUri = Config::get('booking_celestyal.booking_uri');
        $this->MarketInfo = app('App\Http\Controllers\DrupalController')->getMarketInfo();
        $this->ActiveCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        if( !empty($this->ActiveCart) ){
            $this->MarketInfo = (object)$this->ActiveCart['MarketInfo'];
        }
        $this->BookingContactName = 'B2C-Booking';
        $this->OfficeCode = 'B2C';
        
    }
    
    public function xmlErrorHandler($xmlstr){
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xmlstr);
        $xml = explode("\n", $xmlstr);
        $passed = true;
        if (!$doc) {
            $passed = false;
        }
        return $passed;
    }
    
    public function SaveToLogs($body, $code, $bodyResponse = ''){
        
        if( $code == 'Post Login' && $bodyResponse != ''){

            if( !$this->xmlErrorHandler( $bodyResponse ) || strlen( $bodyResponse )<=70){
                $Logs = Logs::firstOrNew( ['Message' => $body,'SessionId'=> 'Error'.date('Y-m-d H:i') ]);
                abort(500);
            }
            $xml=simplexml_load_string($bodyResponse);
            $Logs = Logs::firstOrNew( ['Message' => $body,'SessionId'=> $xml->SessionInfo->SessionID ]);
            $Logs->SessionId = $xml->SessionInfo->SessionID;

        }else{
            $xml=simplexml_load_string($bodyResponse);
            if( isset($xml->SessionInfo) ){
                $Logs = Logs::firstOrNew( ['Message' => $body,'SessionId'=> $xml->SessionInfo->SessionID ]);
                $Logs->SessionId = $xml->SessionInfo->SessionID;
            }else{
                $Logs = Logs::firstOrNew( ['Message' => $body ]);
            }
            

        }
        
        $Logs->Cron_code = $code;
        $Logs->MessageResponse = $bodyResponse;
        // if(isset($_COOKIE["reservation_session"])){
        //     $Logs->SessionId = $_COOKIE["reservation_session"];
        // }
        $Logs->save();
    }
    
    public function loginSaleforce(){
        
        //$uri = 'https://login.salesforce.com/services/oauth2/authorize';
        //$uri = 'https://cs83.lightning.force.com/services/oauth2/token';
        $uri = 'https://cs83.lightning.force.com/services/oauth2/authorize';
        
        $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
        $string = [
        'username'=>'athens@backbone.gr.bbone',
        'password'=>'backb0n3',
        'client_secret'=>'3MVG9w8uXui2aB_ptfUyOR7TZq.vpByM1ss9nJxMIMbGiLnJJ6wPJJ3_5HbRuYg8qbKfm8XravGCghqUFLC99',
        'client_id'=>'3212355788238035362',
        'grant_type'=>'refresh_token'
        ];
        
        //$string = '?username=athens@backbone.gr.bbone&password=backb0n3&client_id=3MVG9w8uXui2aB_ptfUyOR7TZq.vpByM1ss9nJxMIMbGiLnJJ6wPJJ3_5HbRuYg8qbKfm8XravGCghqUFLC99&client_secret=3212355788238035362&grant_type=password';
        
        $response = Unirest\Request::post($uri, $headers, $string);
        
        return $response;
    }
    
    /*
    * Step 1 - Returns Session ID
    */
    public function getLoginFromBooking($AgencyID, $AgentID){
        
        
        $uri = Config::get('booking_celestyal.booking_uri');
        $booking_creds = Config::get('booking_celestyal.booking_creds');
        //include the connection to reservation xml tpl
        include(app_path() . '/_reservationMessages/01_DtsAgencyLoginMessage.php');
        $headers = array('Content-Type' => 'text/xml');
        
        
        $response = Unirest\Request::post( $uri, $headers, $body );
        $this->SaveToLogs($body, 'Post Login', $response->raw_body);
        if( !$this->xmlErrorHandler($response->raw_body) || strlen($response->raw_body)<=70){
            abort(500);
        }
        $xml=simplexml_load_string($response->raw_body) or die("Error: Cannot create object");
        $Cart = [];
        if(isset($xml->SessionInfo->SessionID)){
            return $xml->SessionInfo->SessionID;
        }
        
        return $Cart;
    }
    
    /*
    * Step 2 - Returns Component ID
    */
    public function getShopRequestMessage($IterCode = 'LO03170630', $SessionId = 'ff3e8243-917d-90a7-e711-b913f8db9a4e', $Adults = ['Amount'=>0], $Children = [] ){
        
        include(app_path() . '/_reservationMessages/02_DtsShopRequestMessage.php');
        $headers = array('Content-Type' => 'text/xml');
        $response = Unirest\Request::post($this->reservationUri, $headers, $body);
        $this->SaveToLogs($body, 'Post ShopRequest', $response->raw_body);
        
        $xml=simplexml_load_string($response->raw_body) or die("Error: Cannot create object");
        //return response()->json( json_decode(json_encode($xml),TRUE) );
        return (object) json_decode(json_encode($xml),TRUE);
    }
    
    /*
    * Step 3 - Returns Promo Code
    */
    public function getPricingAvailabilityRequest($componentId, $sessionId){
        $market = app('App\Http\Controllers\DrupalController')->getMarketInfo();
        $uri = Config::get('booking_celestyal.booking_uri');
        $headers = array('Content-Type' => 'text/xml');
        include(app_path() . '/_reservationMessages/03_DtsCruisePricingAvailabilityRequest.php');
        $response = Unirest\Request::post($uri, $headers, $body);
        $this->SaveToLogs($body, 'Post PricingAvailabilityReques', $response->raw_body);
        $xml=simplexml_load_string($response->raw_body) or die("Error: Cannot create object");
        return (object) json_decode(json_encode($xml),TRUE);
        
    }
    
    /*
    * Step 4 - Returns Category Id and Info
    */
    public function getCategoryAvailabilityRequest($sessionId, $componentId, $promoCode){
        $uri = Config::get('booking_celestyal.booking_uri');
        $headers = array('Content-Type' => 'text/xml');
        include(app_path() . '/_reservationMessages/04_DtsCruiseCategoryAvailabilityRequest.php');
        
        $response = Unirest\Request::post($uri, $headers, $body);
        $this->SaveToLogs($body, 'Post CategoryAvailabilityRequest', $response->raw_body);
        
        $xml = simplexml_load_string($response->raw_body);
        $array = (object) json_decode(json_encode($xml),TRUE);
        return $array;
    }
    
    /*
    * Step 5 - Returns Cabin Number, Id and Info
    */
    public function getCruiseCabinAvailabilityRequest($sessionId, $componentId, $categoryCode){
        $uri = Config::get('booking_celestyal.booking_uri');
        $headers = array('Content-Type' => 'text/xml');
        include(app_path() . '/_reservationMessages/05_DtsCruiseCabinAvailabilityRequest.php');
        
        $response = Unirest\Request::post($uri, $headers, $body);
        $this->SaveToLogs($body, 'Post CruiseCabinAvailabilityRequest', $response->raw_body);
        
        $xml = simplexml_load_string($response->raw_body);
        $array = (object) json_decode(json_encode($xml),TRUE);
        return $array;
    }
    
    /*
    * Step 6 - Returns Excursions, Packages and  Component ID (it's the product id for insurances')
    */
    public function getAssocItemsListRequestMessage($componentId, $sessionId, $Adults = ['Amount'=>0], $Children = []  ){
        $uri = Config::get('booking_celestyal.booking_uri');
        $adults = 1;
        $children = 0;
        include(app_path() . '/_reservationMessages/06_DtsAssocItemsListRequestMessage.php');
        $headers = array('Content-Type' => 'text/xml');
        
        $response = Unirest\Request::post($uri, $headers, $body);
        $this->SaveToLogs($body, 'Post AssocItemsListRequestMessage', $response->raw_body);
        
        $xml=simplexml_load_string($response->raw_body) or die("Error: Cannot create object");
        
        $array = (object) json_decode(json_encode($xml),TRUE);
        return $array;
        
    }
    
    /*
    * Step 7 - Returns Insurance Component ID (it's the product id for insurances')
    */
    public function getInsuranceId($SessionID, $ComponentID, $StartDate, $EndDate, $Adults = ['Amount'=>0], $Children = []  ){
        $uri = Config::get('booking_celestyal.booking_uri');
        $headers = array('Content-Type' => 'text/xml');
        include(app_path() . '/_reservationMessages/07_DtsInsuranceRequestMessage.php');
        
        $this->SaveToLogs($body, 'Post InsuranceId');
        $response = Unirest\Request::post($uri, $headers, $body);
        $this->SaveToLogs($body, 'Post InsuranceId', $response->raw_body);
        
        
        $xml = simplexml_load_string($response->raw_body);
        $array = (object) json_decode(json_encode($xml),TRUE);
        return $array;
        
    }
    
    /*
    * Step 7 - Recalculate prices for given component
    */
    public function getRepricedItem($SessionID, $ComponentID, $ComponentData, $Adults, $Children){
        // $SessionID = 'a267011a-3105-3183-e711-761f46be41ef';
        // $ComponentID = 'V854111';
        
        
        $uri = Config::get('booking_celestyal.booking_uri');
        $headers = array('Content-Type' => 'text/xml');
        include(app_path() . '/_reservationMessages/07_DtsRepriceItemRequestMessage.php');
        //return $body;
        $response = Unirest\Request::post($uri, $headers, $body);
        $this->SaveToLogs($body, 'Post RepricedItem', $response->raw_body);
        $xml = simplexml_load_string($response->raw_body);
        $array = (object) json_decode(json_encode($xml),TRUE);
        return $array;
        
    }
    
    /*
    * Step 8 - Submit a booking
    */
    public function postBooking($SessionID, $ComponentID, $ActiveCart){
        $uri = Config::get('booking_celestyal.booking_uri');
        $headers = array('Content-Type' => 'text/xml');
        include(app_path() . '/_reservationMessages/09_DtsBookRequestMessage.php');
        
        
        $response = Unirest\Request::post($uri, $headers, $body);
        $this->SaveToLogs($body, 'Post Booking', $response->raw_body);
        
        $xml = simplexml_load_string($response->raw_body);
        $array = (object) json_decode(json_encode($xml),TRUE);
        return $array;
    }
    
    /*
    * Step 8 - Submit a booking
    */
    public function postBookingManage($SessionID, $ComponentID, $ActiveCart, $BookingNo){
        $uri = Config::get('booking_celestyal.booking_uri');
        $headers = array('Content-Type' => 'text/xml');
        include(app_path() . '/_reservationMessages/09_DtsBookRequestMessageManage.php');
        
        
        $response = Unirest\Request::post($uri, $headers, $body);
        $this->SaveToLogs($body, 'Post Booking Manage', $response->raw_body);
        
        $xml = simplexml_load_string($response->raw_body);
        $array = (object) json_decode(json_encode($xml),TRUE);
        return $array;
    }
    
    /*
    * Step 8 - Submit test a booking
    */
    public function postTestBooking($SessionID, $ComponentID, $CategoryCode, $CabinNo, $excursionComponentID){
        $uri = Config::get('booking_celestyal.booking_uri');
        $headers = array('Content-Type' => 'text/xml');
        include(app_path() . '/_reservationMessages/11_TestBookingSubmission.php');
        
        $response = Unirest\Request::post($uri, $headers, $body);
        $this->SaveToLogs($body, 'Post Booking TestBooking', $response->raw_body);
        
        $xml = simplexml_load_string($response->raw_body);
        $array = (object) json_decode(json_encode($xml),TRUE);
        return $array;
        
    }
    
    public function getPriceToBookRequestMessage($SessionID, $ComponentID, $CategoryCode){
        $uri = Config::get('booking_celestyal.booking_uri');
        $headers = array('Content-Type' => 'text/xml');
        include(app_path() . '/_reservationMessages/08_DtsPriceToBookRequestMessage.php');
        $response = Unirest\Request::post($uri, $headers, $body);
        $this->SaveToLogs($body, 'Post Booking PriceToBookRequestMessage', $response->raw_body);
        $xml = simplexml_load_string($response->raw_body);
        $array = (object) json_decode(json_encode($xml),TRUE);
        return $array;
    }
    
    public function getApplyPaymentRequestMessage($SessionID){
        $uri = Config::get('booking_celestyal.booking_uri');
        $headers = array('Content-Type' => 'text/xml');
        include(app_path() . '/_reservationMessages/09_DtsApplyPaymentRequestMessage.php');
        
        
        $response = Unirest\Request::post($uri, $headers, $body);
        $this->SaveToLogs($body, 'Post Booking ApplyPaymentRequestMessage', $response->raw_body);
        
        $xml = simplexml_load_string($response->raw_body);
        $array = (object) json_decode(json_encode($xml),TRUE);
        return $array;
        
    }
    
    public function manageBooking($BookingNo, $Email, $Name){
        $uri = Config::get('booking_celestyal.booking_uri');
        $headers = array('Content-Type' => 'text/xml');
        include(app_path() . '/_reservationMessages/12_ManageBooking.php');
        $response = Unirest\Request::post($uri, $headers, $body);
        $this->SaveToLogs($body, 'Post manageBooking', $response->raw_body);
        $xml = simplexml_load_string($response->raw_body);
        $array = (object) json_decode(json_encode($xml),TRUE);
        return $array;
        
    }
    
    /*
    * Step 6 - Returns Excursions, Packages and  Component ID (it's the product id for insurances')
    */
    public function getAssocItemsListRequestMessageTest($componentId, $sessionId){
        $uri = Config::get('booking_celestyal.booking_uri');
        $adults = 1;
        $children = 1;
        include(app_path() . '/_reservationMessages/Test_DtsAssocItemsListRequestMessage.php');
        $headers = array('Content-Type' => 'text/xml');
        
        $response = Unirest\Request::post($uri, $headers, $body);
        $this->SaveToLogs($body, 'Post Booking AssocItemsListRequestMessageTest', $response->raw_body);
        
        $xml=simplexml_load_string($response->raw_body) or die("Error: Cannot create object");
        
        $array = json_decode(json_encode($xml),TRUE);
        return $array;
        
    }
    
}