<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use DB;


class DrupalController extends Controller
{
    //
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

    public function getValidIterCode($itercode){
        $drupalConnection = DB::connection('mysql_drupal');
        $validIter = $drupalConnection->select("SELECT count(CRD_ID) as counter  FROM bbt_cruises_departs WHERE CRD_CODE = '".$itercode."' ")[0];
        return $validIter->counter;
    }
    
    public function getClientIp(){
        
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')){
            $ipaddress = getenv('HTTP_CLIENT_IP');
        }else if(getenv('HTTP_X_FORWARDED_FOR')){
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        }else if(getenv('HTTP_X_FORWARDED')){
            $ipaddress = getenv('HTTP_X_FORWARDED');
        }else if(getenv('HTTP_FORWARDED_FOR')){
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        }else if(getenv('HTTP_FORWARDED')){
            $ipaddress = getenv('HTTP_FORWARDED');
        }else if(getenv('REMOTE_ADDR')){
            $ipaddress = getenv('REMOTE_ADDR');
        }else{
            $ipaddress = 'UNKNOWN';
            $ipaddress = "80.106.218.89";
        }
        
        //GREECE
        $ipaddress = "80.106.218.89";
        
        //USA
        //$ipaddress = "172.110.128.7";
        
        return $ipaddress;
        
    }
    
    /**
    * stdClass Object
    * (
    *   [MarketCode] => GRC
    *   [Currency] => EUR
    *   [AgencyId] => 0000012
    *   [CurrencySymbol] => €, $, £ etc
    *   )
    *   Type = 1 for aegan products and 2 for cuban
    */
    public function getMarketInfo($type = 1){
        $ip = $this->getClientIp();
        $drupalConnection = DB::connection('mysql_drupal');
        $data = $drupalConnection->select("SELECT cc FROM geo_ip WHERE INET_ATON('".$ip."') BETWEEN start AND end LIMIT 1")[0];
        if($type == 1){
            $mrkcode = $drupalConnection->select("SELECT CRM_MARKETCODE as MarketCode, CRM_CURRENCY as Currency, CRM_AGENCYID as AgencyId  FROM bbt_cruises_markets as markets WHERE CRM_CODE = '".$data->cc."' LIMIT 1")[0];
        }else{
            $mrkcode = $drupalConnection->select("SELECT CRM_CUBAMARKET as MarketCode, CRM_CURRENCY as Currency, CRM_AGENCYID as AgencyId  FROM bbt_cruises_markets as markets WHERE CRM_CODE = '".$data->cc."' LIMIT 1")[0];
        }
        if($mrkcode->Currency == 'EUR'){
            $mrkcode->CurrencySymbol = "€";
        }else{
            $mrkcode->CurrencySymbol = "$";
        }
        return $mrkcode;
    }

    public function getPkgsPricesInfo($itercode = "LO03170630", $marketcode = "GRC"){
        $query = "SELECT * FROM bbt_cruises_pricing where CPR_UNIQUE = '".$itercode."-".$marketcode."'";
        $drupalConnection = DB::connection('mysql_drupal');
        $data = $drupalConnection->select($query)[0];
        if( isset( json_decode($data->CPR_PCKG)->components ) ){
            $data = json_decode($data->CPR_PCKG)->components;
        }else{
            $data = [];
        }
        return json_encode( $data  );
    }

    public function getInclusiveInfo($itercode = "LO03170630", $marketcode = "GRC"){
        $query = "SELECT CRC_INCL_PCKG as Inclusive FROM bbt_cruises_pricing where CPR_UNIQUE = '".$itercode."-".$marketcode."'";
        $drupalConnection = DB::connection('mysql_drupal');
        $data = $drupalConnection->select($query)[0];
        $data = $data->Inclusive;
        
        return json_encode( $data );
    }
    
    public function getMarketInfoJson(){
        return json_encode($this->getMarketInfo());
    }
    
    public function getIterInfo( $iterInfo = -1 ){
        $data = [];
        if($iterInfo != -1){
            $drupalConnection = DB::connection('mysql_drupal');
            $data = $drupalConnection->select("SELECT * FROM bbt_cruises_departs where CRD_CODE = '".$iterInfo."' ")[0];
        }
        return $data;
    }
    
    public function getDrupalCruise($cruiseCode = null, $itercode = null){
        if(!isset($cruiseCode)){
            $cruiseCode = 'G3J';
        }
        if(!isset($itercode)){
            $itercode = 'LO03170630';
        }
        
        $data = [];
        if($cruiseCode){
            $uri = $this->httpDrupalBaseUri.$this->locale.'/api/v1/cruises.json?booking=1&cruise_code='.$cruiseCode;
            $dataRetr = file_get_contents($uri, false, $this->httpContext);
            if(!isset( json_decode( $dataRetr )->cruises[0] ) ){
                abort(500, 'Cruise Not Found<br>Get Drupal Cruise');
            }
            $data = json_decode( $dataRetr )->cruises[0];
            $data->filters = json_decode( $dataRetr )->filters;
            $data->destinations = json_decode( $dataRetr )->filters->ports;
            $tmpSchedule = json_decode( $dataRetr )->cruises[0]->schedule->$itercode;
            $data->schedule = [];

            foreach($tmpSchedule as $day => $dayContent){
                foreach($dayContent as $sq=>$sqContent){
                $sqContent->day = $day;
                $data->schedule[$sq] = $sqContent;
                }

            }
            //$data->schedule = $tmpSchedule;
            // return json_encode($data->schedule);
            // unset($data->departures);
            
            //$data->iterinfo = $this->getIterInfo( $iterInfo  )
            // $data['filters'] = json_encode( $data );
        }
        return $data;
    }
    
    public function getDrupalShip($shipCode = 'LO'){
        $data = [];
        if($shipCode){
            $uri = $this->httpDrupalBaseUri.$this->locale.'/api/v1/ships-service.json?booking=1&field_ship_code_alt='.$shipCode.'&field_ship_code='.$shipCode;
            $data = file_get_contents($uri, false, $this->httpContext);
            $data = json_decode( $data );
            
            foreach($data[0]->field_interior as $key => $interior ){
                $data[0]->field_interior[$interior->code] = $interior;
                unset($data[0]->field_interior[$key]);
            }
            foreach($data[0]->field_exterior as $key => $exterior ){
                $data[0]->field_exterior[$exterior->code] = $exterior;
                unset($data[0]->field_exterior[$key]);
            }
            foreach($data[0]->field_suite as $key => $suite ){
                $data[0]->field_suite[$suite->code] = $suite;
                unset($data[0]->field_suite[$key]);
            }
            
        }
        return $data;
    }
    
    public function getDrupalExcursion(){
        $data = [];
        $uri = $this->httpDrupalBaseUri.$this->locale.'/api/v1/excursions-service.json?booking=1';
        $data = file_get_contents($uri, false, $this->httpContext);
        $data = json_decode( $data );
        return $data;
    }
    
    public function getDrupalDrinkPackage(){
        $data = [];
        $uri = $this->httpDrupalBaseUri.$this->locale.'/api/v1/drinks-service.json?booking=1';
        $data = file_get_contents($uri, false, $this->httpContext);
        $data = json_decode( $data );
        return $data;
    }
    
    public function getDrupalService(){
        $data = [];
        $uri = $this->httpDrupalBaseUri.$this->locale.'/api/v1/additionalservices-service.json?booking=1';
        $data = file_get_contents($uri, false, $this->httpContext);
        $data = json_decode( $data );
        return $data;
    }
    
    public function getDrupalDestination(){
        $data = [];
        $uri = $this->httpDrupalBaseUri.$this->locale.'/api/v1/ports-service.json?booking=1';
        $data = file_get_contents($uri, false, $this->httpContext);
        $data = json_decode( $data );
        foreach($data as $dest){
            unset($dest->nid);
            unset($dest->destination_summary);
            unset($dest->destination_description);
        }
        
        return $data;
    }
    
    
    
    
    
}