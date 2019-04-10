<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Contracts\Foundation\Application;
use App\Models\UserSessions;
use Illuminate\Support\Facades\Cookie;
use App\Models\Admin\Cart;
use App\Models\Admin\ManageCart;
use App\Models\Admin\Insurance;
use Illuminate\Support\Facades\Config;

class ApplicationCheckoutController extends Controller
{
    protected $app, $request, $marketInfo, $activeCart;
    
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
        //$this->middleware('clientAuth');
        
    }
    
    /*
    |----------------------------------------------------------------------------------------------------------------------------------------------------
    |
    | Booking Views
    |
    |----------------------------------------------------------------------------------------------------------------------------------------------------
    */
    
    /**
    * Show the application dashboard.
    *
    */
    public function index($itercode = -1, $cruisecode = -1){
        //abort(500);
        $isValidCode = app('App\Http\Controllers\DrupalController')->getValidIterCode($itercode);
        if($itercode == -1 || $isValidCode==0){
            abort(404);
        }
        
        $this->activeCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        
        if( !isset($_COOKIE["reservation_session"]) || $this->activeCart['IterCode'] != $itercode ){
            $this->activeCart = app('App\Http\Controllers\ActiveCartController')->create($itercode);
        }
        
        $iterInfo = app('App\Http\Controllers\DrupalController')->getIterInfo( $this->activeCart['IterCode']  );
        
        
        
        $this->activeCart['CruiseInfo']['Generic'] = (object)($iterInfo);
        $cruisecode = $iterInfo->CRD_ITERCODE;
        $this->activeCart['CruiseInfo']['Summary'] = app('App\Http\Controllers\DrupalController')->getDrupalCruise($cruisecode, $itercode);
        
        $shipCode = $iterInfo->CRD_SHIP_CODE;
        
        $this->activeCart['CruiseInfo']['ShipInfo'] = app('App\Http\Controllers\DrupalController')->getDrupalShip( $shipCode )[0];
        $this->activeCart['CruiseInfo']['Destinations'] = app('App\Http\Controllers\DrupalController')->getDrupalDestination();
        
        //Aegean
        $marketSearch = 1;
        if( $this->activeCart['CruiseInfo']['Summary']->cruise_region_name == 'cuba' ){
            $marketSearch = 2;
        }
        
        $this->marketInfo = app('App\Http\Controllers\DrupalController')->getMarketInfo($marketSearch);
        $this->activeCart['MarketInfo'] = (object) $this->marketInfo;
        
        $this->activeCart['CruiseInfo']['Excursions'] = app('App\Http\Controllers\DrupalController')->getDrupalExcursion();
        $ExcPrices = app('App\Http\Controllers\DrupalController')->getPkgsPricesInfo($this->activeCart['IterCode'], $this->marketInfo->MarketCode);
        $this->activeCart['CruiseInfo']['ExcursionsPrices'] = json_decode($ExcPrices, true);
        
        $this->activeCart['CruiseInfo']['DrinkPackages'] = app('App\Http\Controllers\DrupalController')->getDrupalDrinkPackage();
        $this->activeCart['CruiseInfo']['Prices'] = (object) ['Total'=>0];
        
        
        
        app('App\Http\Controllers\ActiveCartController')->updateCart($this->activeCart);
        
        
        
        $locale = $this->app->getLocale();
        return view('layouts_booking.pages.index')->with([
        'IterCode' => $itercode,
        'lang' => $locale,
        'ActiveCart' => $this->activeCart,
        ]);
        
        
    }
    
    /**
    * GET | Cruise Details Ajax Page
    *
    */
    public function getCruisedetails(){
        $this->activeCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        return view('layouts_booking.pagesAjax.01_CruiseDetails')
        ->with([
        'ActiveCart'=>$this->activeCart
        ]);
    }
    
    /**
    * GET | Guest Info Ajax Page
    *
    */
    public function getGuestinfo(){
        $this->activeCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        return view('layouts_booking.pagesAjax.02_GuestInfo')
        ->with([
        'ActiveCart'=>$this->activeCart
        ]);
    }
    
    /**
    * GET | Preferences Ajax Page
    *
    */
    public function getPreferences(){
        $this->activeCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        
        $ExcPrice = app('App\Http\Controllers\DrupalController')->getPkgsPricesInfo($this->activeCart['IterCode'], $this->activeCart['MarketInfo']['MarketCode']);
        $DrinkPackagesContent = app('App\Http\Controllers\DrupalController')->getDrupalDrinkPackage();
        
        $ExcPrice = json_decode($ExcPrice, true);
        //$ExcPrice = [];
        return view('layouts_booking.pagesAjax.03_Preferences')
        ->with([
        'DrinkPackages' => $DrinkPackagesContent,
        'ExcPrices' => $ExcPrice,
        'ActiveCart'=>$this->activeCart
        ]);
    }
    
    /**
    * GET | Payment Ajax Page
    *
    */
    public function getPayment(){
        $insurances = Insurance::where('Lang', $this->app->getLocale())->get();
        $insurances = $insurances->toArray();
        $insKeyCodes = [];
        foreach($insurances as $ins){
            $insKeyCodes[$ins['Code']] = $ins;
        }
        $this->activeCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        $this->activeCart['CruiseInfo']['Insurances'] = $insKeyCodes;
        return view('layouts_booking.pagesAjax.04_Payment')
        ->with([
        'InsurancesContent'=>$insKeyCodes,
        'ActiveCart'=>$this->activeCart
        ]);
    }
    
    /**
    * GET | Confirmation Ajax Page
    *
    */
    public function getConfirmation(){
        $this->activeCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        return view('layouts_booking.pagesAjax.05_Confirmation')
        ->with([
        'ActiveCart'=>$this->activeCart
        ]);
    }
    
    /**
    * POST | Guests Number Ajax Page
    *
    */
    public function postGuestNumber(Request $request){
        $IterCode = $request->input('IterCode');
        
        $Adults = $request->input('Adults');
        $Children = $request->input('Children');
        
        app('App\Http\Controllers\ActiveCartController')->setActiveCart($Adults, 'Adults');
        app('App\Http\Controllers\ActiveCartController')->setActiveCart($Children, 'Children');
        $this->activeCart = (object) app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        
        $this->marketInfo = app('App\Http\Controllers\DrupalController')->getMarketInfo();
        
        app('App\Http\Controllers\ActiveCartController')->setActiveCart(1, 'Step');
        
        
        //Get Component Id
        $shopReq = app('App\Http\Controllers\ReservationController')->getShopRequestMessage($IterCode, $this->activeCart->SessionId, $Adults, $Children);
        if(isset($shopReq->AdvisoryInfo['AdvisoryMessage'])){
            return json_encode(['ApplicationError'=>$shopReq->AdvisoryInfo['AdvisoryMessage']['MessageText']]);
        }
        
        $compID = $shopReq->CruiseProducts['CruiseSailing']['ComponentInfo']['ComponentID'];
        
        $availReq = app('App\Http\Controllers\ReservationController')->getPricingAvailabilityRequest($compID, $this->activeCart->SessionId);
        
        if(isset($availReq->PricingPromotionsAvailable['CruisePromotionCode'])){
            $promoCode = $availReq->PricingPromotionsAvailable['CruisePromotionCode'];
        }else{
            $promoCode = '';
            abort(500);
        }
        
        $catAvailReq = app('App\Http\Controllers\ReservationController')->getCategoryAvailabilityRequest($this->activeCart->SessionId, $compID, $promoCode);
        
        $jsonResInfo = [];
        //Set Cabins Response to cart
        $jsonResInfo['AvailableCategory'] = $catAvailReq->AvailableCategories['AvailableCategory'];
        
        $availCabins = [];
        foreach($catAvailReq->AvailableCategories['AvailableCategory'] as $cabin){
            if($cabin['CategoryType'] == 'I'){
                $availCabins['I'][] = $cabin;
            }else if($cabin['CategoryType'] == 'O'){
                $availCabins['O'][] = $cabin;
            }else if($cabin['CategoryType'] == 'D'){
                $availCabins['D'][] = $cabin;
            }
            
        }
        $jsonResInfo['AvailableCabins'] = $availCabins;
        $jsonResInfo['ShopRequest'] = $shopReq;
        $jsonResInfo['PricingAvailability'] = $availReq;
        
        
        $AssocItems = app('App\Http\Controllers\ReservationController')->getAssocItemsListRequestMessage($compID, $this->activeCart->SessionId, $Adults, $Children );
        $excursions = [];
        if( isset($AssocItems->ActivityProducts) ){
            if( isset( $AssocItems->ActivityProducts['ActivityOccurence'] ) ){
                $excursions = $AssocItems->ActivityProducts['ActivityOccurence'];
            }
        }
        
        //$jsonResInfo['PriceToBook']  = app('App\Http\Controllers\ReservationController')->getPriceToBookRequestMessage($this->activeCart->SessionId, $compID, "IF");
        
        //$jsonResInfo['Insurances']  = app('App\Http\Controllers\ReservationController')->getPriceToBookRequestMessage($this->activeCart->SessionId, $compID, "IF");
        
        foreach($excursions as $pkg){
            $pkgKey = $pkg['ActivityInfo']['ActivityCode'];
            unset($pkg['ActivityDescription']);
            $jsonResInfo['Excursions'][$pkgKey] = $pkg;
        }
        $jsonResInfo['AssocItems'] = $AssocItems;
        
        app('App\Http\Controllers\ActiveCartController')->setActiveCart(json_decode( json_encode($jsonResInfo, JSON_UNESCAPED_SLASHES)), 'ReservationInfo');
        
        
        
        $ActiveCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        
        return $ActiveCart;
        
    }
    
    /**
    * POST | Stateroom Ajax Page
    *
    */
    public function postStateroom(Request $request){
        $ActiveCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        $Inputs = $request->all();
        
        $CategoryId = $Inputs['CategoryId'];
        $CategoryName = $Inputs['Name'];
        $Price = $Inputs['Price'];
        
        $ComponentId = $ActiveCart['ReservationInfo']['ShopRequest']['CruiseProducts']['CruiseSailing']['ComponentInfo']['ComponentID'];
        
        $catAvailReq = app('App\Http\Controllers\ReservationController')->getCruiseCabinAvailabilityRequest($ActiveCart['SessionId'], $ComponentId, $CategoryId);
        $PriceToBook = app('App\Http\Controllers\ReservationController')->getPriceToBookRequestMessage($ActiveCart['SessionId'], $ComponentId, $CategoryId);
        
        
        $CabinInfo = $catAvailReq->AvailableCabins['AvailableCabin'][0];
        
        $ActiveCart['Staterooms']['CabinInfo'] = $CabinInfo;
        $ActiveCart['Staterooms']['CategoryId'] = $CategoryId;
        $ActiveCart['Staterooms']['Name'] = $CategoryName;
        $ActiveCart['Staterooms']['Price'] = $Price;
        
        
        app('App\Http\Controllers\ActiveCartController')->updateCart($ActiveCart);
        
        $ActiveCart['ReservationInfo']['PriceToBook'] = $PriceToBook;
        app('App\Http\Controllers\ActiveCartController')->setActiveCart(json_decode( json_encode($ActiveCart['ReservationInfo'])), 'ReservationInfo');
        
        app('App\Http\Controllers\ActiveCartController')->setActiveCart('2', 'Step');
        $ActiveCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        
        return json_encode($ActiveCart);
    }
    
    /**
    * POST | Guests Informations Ajax Page
    *
    */
    public function postGuests(Request $request){
        // $ActiveCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        $ApplicationError = '';
        $Inputs = $request->all();
        $ActiveCart['Adults']['Data'] = $Inputs['Adults'];
        
        $adultAmount =  (string) count( $Inputs['Adults'] ) ;
        if(isset($Inputs['Children'])){
            $childrenAmount =  (string) count( $Inputs['Children'] ) ;
        }else{
            $childrenAmount = "0";
        }
        //check miles and bonus
        foreach( $Inputs['Adults'] as $kAdData => $adultData ){
            
            if( isset($adultData['MB']) ){
                if($adultData['MB']!=""){
                    $checkMB = app('App\Http\Controllers\MilesAndBonusController')->checkMBCurl($adultData['MB']);
                    if( $checkMB->errorCode>0){
                        $ApplicationError[$kAdData]['HasError'] = true;
                        $ApplicationError[$kAdData]['Error'] = $checkMB->processStatus;
                        if( !isset($ApplicationError['Guest']) )$ApplicationError['Guest'] = $kAdData;
                    }else{
                        $ApplicationError[$kAdData]['HasError'] = false;
                        $ApplicationError[$kAdData]['Error'] = "";
                    }
                }
            }
            
        }
        
        
        if( isset($Inputs['Children']) ){
            $ActiveCart['Children']['Data'] = $Inputs['Children'];
        }else{
            $ActiveCart['Children']['Amount'] = "0";
            $ActiveCart['Children']['Data'] = [];
        }
        app('App\Http\Controllers\ActiveCartController')->setActiveCart( json_decode(json_encode(['Amount'=>$adultAmount,'Data'=> $ActiveCart['Adults']['Data'] ] ) ), 'Adults');
        app('App\Http\Controllers\ActiveCartController')->setActiveCart( json_decode(json_encode(['Amount'=>$childrenAmount,'Data'=> $ActiveCart['Children']['Data'] ] ) ), 'Children');
        app('App\Http\Controllers\ActiveCartController')->setActiveCart('3', 'Step');
        
        $ActiveCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        $ActiveCart['ApplicationError'] = $ApplicationError;
        return json_encode($ActiveCart);
    }
    
    /**
    * POST | Preferences Ajax Page
    *
    */
    public function postPreferences(Request $request){
        $Inputs = $request->all();
        $ActiveCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        
        if( isset($Inputs['Excursions']) && is_array( $Inputs['Excursions'] ) ){
            $keys = array_keys($Inputs['Excursions']);
            $values = array_values($Inputs['Excursions']);
            $stringKeys = array_map('strval', $keys);
            $Inputs['Excursions'] = array_combine($stringKeys, $values);
        }else{
            $Inputs['Excursions'] = [];
        }
        
        
        
        
        $StartDate = $ActiveCart['ReservationInfo']['ShopRequest']['CruiseProducts']['CruiseSailing']['CruiseInfo']['SailingDate'];
        $EndDate = $ActiveCart['ReservationInfo']['ShopRequest']['CruiseProducts']['CruiseSailing']['CruiseInfo']['ReturnDate'];
        $StartDate = date('d/m/Y',strtotime($StartDate));
        $EndDate = date('d/m/Y',strtotime($EndDate));
        $componentId = $ActiveCart['ReservationInfo']['ShopRequest']['CruiseProducts']['CruiseSailing']['ComponentInfo']['ComponentID'];
        
        //Step 7
        $insuranceInfo = app('App\Http\Controllers\ReservationController')->getInsuranceId($ActiveCart['SessionId'], $componentId, $StartDate, $EndDate, $ActiveCart['Adults'], $ActiveCart['Children']);
        $ActiveCart['ReservationInfo']['Insurance'] = $insuranceInfo->InsuranceProducts['InsuranceType'];
        app('App\Http\Controllers\ActiveCartController')->setActiveCart( json_decode(json_encode( $ActiveCart['ReservationInfo'] ) ), 'ReservationInfo');
        
        
        
        app('App\Http\Controllers\ActiveCartController')->setActiveCart( json_decode(json_encode( $Inputs['Excursions'] ) ), 'Excursions');
        if( isset($Inputs['DrinkPackage']) ){
            app('App\Http\Controllers\ActiveCartController')->setActiveCart( json_decode(json_encode( $Inputs['DrinkPackage'] ) ), 'DrinkPackage');
        }
        app('App\Http\Controllers\ActiveCartController')->setActiveCart('4', 'Step');
        
        $ActiveCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        return json_encode($ActiveCart);
    }
    
    /**
    * POST | Repriced/Recalculated Components Prices (On Confirm of excursions etc)
    *
    */
    public function postReprice(Request $request){
        $Inputs = $request->all();
        $SessionID = $Inputs['SessionID'];
        $Adults = $Inputs['Adults'];
        $Children = $Inputs['Children'];
        $Excursions = $Inputs['Excursions'];
        $ComponentID = array_keys($Excursions)[0];
        $ComponentData = $Excursions[$ComponentID];
        //if no pax is posted then reprice messages returns prices for everybody. Sucks
        $returnPrice = false;
        
        foreach($ComponentData as $ckey=>$cdata){
            if($cdata === "true"){
                $returnPrice = true;
                break 1;
            }
        }
        if($returnPrice){
            $Repriced =  app('App\Http\Controllers\ReservationController')->getRepricedItem($SessionID, $ComponentID, $ComponentData, $Adults, $Children);
        }else{
            $Repriced['ComponentPrice']['ItemGrossPrice'] = 0;
        }
        return json_encode($Repriced);
    }
    
    /**
    * POST | Repriced/Recalculated Components Prices (On Confirm of excursions etc)
    *
    */
    public function postRepriceDrink(Request $request){
        $Inputs = $request->all();
        $SessionID = $Inputs['SessionID'];
        $Adults = $Inputs['Adults'];
        $Children = $Inputs['Children'];
        $DrinkPackageCompID = $Inputs['DrinkPackage'];
        $ComponentData = [];
        //if no pax is posted then reprice messages returns prices for everybody. Sucks
        $returnPrice = false;
        if( isset( $Inputs['Adults']['Data'] ) ){
            foreach( $Inputs['Adults']['Data'] as $adKey => $ad ){
                $ComponentData[] = "true";
            }
        }
        
        if( isset( $Inputs['Children']['Data'] ) ){
            foreach( $Inputs['Children']['Data'] as $chKey => $ch ){
                $ComponentData[] = "true";
            }
        }
        
        
        $Repriced =  app('App\Http\Controllers\ReservationController')->getRepricedItem($SessionID, $DrinkPackageCompID, $ComponentData, $Adults, $Children);
        // if($returnPrice){
        
        // }else{
        //     $Repriced['ComponentPrice']['ItemGrossPrice'] = 0;
        // }
        return json_encode($Repriced);
    }
    
    
    /**
    * POST | Booking Ajax Page
    *
    */
    public function postBooking(Request $request){
        $Inputs = $request->all();
        
        
        app('App\Http\Controllers\ActiveCartController')->setActiveCart(json_decode( json_encode( $Inputs['Insurance'] ) ), 'Insurance');
        
        $ActiveCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        
        $ComponentID = $ActiveCart['ReservationInfo']['ShopRequest']['CruiseProducts']['CruiseSailing']['ComponentInfo']['ComponentID'];
        $booking =  app('App\Http\Controllers\ReservationController')->postBooking($ActiveCart['SessionId'], $ComponentID, $ActiveCart);
        
        
        $ActiveCart['ReservationInfo']['PostBooking'] = $booking;
        
        app('App\Http\Controllers\ActiveCartController')->setActiveCart(json_decode( json_encode( $ActiveCart['ReservationInfo'] ) ), 'ReservationInfo');
        app('App\Http\Controllers\ActiveCartController')->setActiveCart('5', 'Step');
        
        $ActiveCart = app('App\Http\Controllers\ActiveCartController')->getActiveCart();
        return json_encode($ActiveCart);
    }
    
    /*
    |----------------------------------------------------------------------------------------------------------------------------------------------------
    |
    | Manage Booking Views
    |
    |----------------------------------------------------------------------------------------------------------------------------------------------------
    */
    
    public function deleteCookie($cookieName){
        
        if (isset($_COOKIE[$cookieName])) {
            unset($_COOKIE[$cookieName]);
            setcookie($cookieName, null, -1, '/');
            return true;
        } else {
            return false;
        }
        
    }
    
    /**
    * GET | Manage Booking Login Page
    *
    */
    public function getManageBookingLogin($error = ''){
        
        return view('layouts_manage.login')->with([
        'Error'=> $error,
        'IterCode' => '',
        'mail-lastname'=>'',
        'bnum'=>'',
        'lang' => $this->app->getLocale(),
        'ActiveCart' => [],
        ]);
        
    }
    
    /**
    * GET | Manage Booking Dashboard
    *
    */
    public function getManageBookingDashboard(){
        
        if( !isset($_COOKIE['reservation_session_manage']) ){
            return $this->getManageBookingLogin('Session has expired.');
        }
        
        $creds = json_decode( $_COOKIE['reservation_session_manage'] );
        $Name = $Email = $BookingNum = '';
        if( isset( $creds->Name ) ){
            $Name = $creds->Name;
        }
        if( isset( $creds->Email ) ){
            $Email = $creds->Email;
        }
        
        if( isset( $creds->BookingNo ) ){
            $BookingNum = $creds->BookingNo;
        }
        
        $ActiveCart = [];
        $MCartModel = ManageCart::Where([['BookingNo', '=', $BookingNum ]])->get()->first();
        if( isset( $MCartModel ) ){
            $MCart = $MCartModel->toArray();
            if( isset($MCart['Excursions']) && is_array($MCart['Excursions']) ){
                foreach($MCart['Excursions'] as $eocKey => $excOnCart){
                    foreach( $excOnCart[array_keys($excOnCart)[0]] as $kko=>$ko){
                        $excOnCart[array_keys($excOnCart)[0]][$kko ]=true;
                    }
                    $MCart['Excursions'][$eocKey] = $excOnCart;
                }
            }
            
            if( !isset($MCart['ReservationInfo']['Login']) ){
                $ActiveCart["ReservationInfo"]["Login"] = (array) app('App\Http\Controllers\ReservationController')->manageBooking( $BookingNum, $Email, $Name );
                $MCartModel->ReservationInfo = $ActiveCart["ReservationInfo"];
                $MCartModel->save();
            }else{
                $ActiveCart['ReservationInfo']['Login'] = $MCart['ReservationInfo']['Login'];
            }
            
            
            
        }else{
            $MCartModel = new ManageCart();
            $MCartModel->BookingNo = $BookingNum;
            $ActiveCart["ReservationInfo"]["Login"] = (array) app('App\Http\Controllers\ReservationController')->manageBooking( $BookingNum, $Email, $Name );
            
        }
        
        // if( isset( $MCartModel ) ){
        
        // }else{
        
        // }
        
        //$ActiveCart["ReservationInfo"]["Login"] = (array) app('App\Http\Controllers\ReservationController')->manageBooking( $BookingNum, $Email, $Name );
        
        
        if( isset( $ActiveCart["ReservationInfo"]["Login"]['AdvisoryInfo']['AdvisoryMessage']['MessageText'] ) ){
            return $this->getManageBookingLogin( $ActiveCart["ReservationInfo"]["Login"]['AdvisoryInfo']['AdvisoryMessage']['MessageText'] );
        }
        
        $SessionId = $ActiveCart["ReservationInfo"]["Login"]["SessionInfo"]["SessionID"];
        $ActiveCart["SessionId"] = $SessionId;
        $ActiveCart["BookinNo"] = $ActiveCart["ReservationInfo"]["Login"]["BookingContext"]["BookingNo"];
        
        $itercode = $ActiveCart["ReservationInfo"]["Login"]['CruiseBookings']['CruiseSailing']['CruiseID'];
        $cruisecode = $ActiveCart["ReservationInfo"]["Login"]['CruiseBookings']['CruiseSailing']['ItineraryCode'];
        $ActiveCart['CruiseInfo']['Generic'] = (array) app('App\Http\Controllers\DrupalController')->getIterInfo( $itercode );
        $ActiveCart['CruiseInfo']['Summary'] = (array) app('App\Http\Controllers\DrupalController')->getDrupalCruise($cruisecode, $itercode);
        $ActiveCart['CruiseInfo']['ShipInfo'] = (array) app('App\Http\Controllers\DrupalController')->getDrupalShip()[0];
        $ActiveCart['CruiseInfo']['Destinations'] = (array) app('App\Http\Controllers\DrupalController')->getDrupalDestination();
        $compID = $ActiveCart["ReservationInfo"]["Login"]['CruiseBookings']['CruiseSailing']['ComponentInfo']['ComponentID'];
        
        $ActiveCart['Adults'] = ["Amount"=>2,"Data"=>[] ];
        $ActiveCart['Children'] = ["Amount"=>0,"Data"=>[] ];
        $adultCount = $childrenCount = 0;
        
        
        if( isset( $ActiveCart["ReservationInfo"]["Login"]['ParticipantList']['ParticipantData']['PersonNo'] ) ){
            $adultCount++;
            $ActiveCart['Adults']['Data'][] = $ActiveCart["ReservationInfo"]["Login"]['ParticipantList']['ParticipantData'];
            $ActiveCart['Children']['Data'] = [];
        }else{
            
            foreach( $ActiveCart["ReservationInfo"]["Login"]['ParticipantList']['ParticipantData'] as $participant){
                
                if($participant['PersonType'] == "A"){
                    $adultCount++;
                    $ActiveCart['Adults']['Data'][] = $participant;
                }else{
                    $childrenCount++;
                    $ActiveCart['Children']['Data'][] = $participant;
                }
            }
            
        }

        $AssocItems = [];
        
        if( !isset($MCart['ReservationInfo']['AssocItems']) ){
                $AssocItems = app('App\Http\Controllers\ReservationController')->getAssocItemsListRequestMessage($compID, $SessionId, $ActiveCart['Adults'], $ActiveCart['Children'] );
        }else{
                $AssocItems = (object)$MCart['ReservationInfo']['AssocItems'];
        }
        
        
        
        
        
        
        $excursions = [];
        if( isset($AssocItems->ActivityProducts) && isset( $AssocItems->ActivityProducts['ActivityOccurence'] ) ){
            $excursions = $AssocItems->ActivityProducts['ActivityOccurence'];
        }
        
        foreach($excursions as $pkg){
            $pkgKey = $pkg['ActivityInfo']['ActivityCode'];
            unset($pkg['ActivityDescription']);
            $ActiveCart["ReservationInfo"]['Excursions'][$pkgKey] = $pkg;
        }
        $ActiveCart["ReservationInfo"]["AssocItems"] = (array) $AssocItems;
        //Aegean
        $marketSearch = 1;
        if( $ActiveCart['CruiseInfo']['Summary']['cruise_region'] == 'Cuba' ){
            $marketSearch = 2;
        }
        
        
        $ActiveCart['IterCode'] = $itercode;
        $ActiveCart['CruiseInfo']['Excursions'] = (array)app('App\Http\Controllers\DrupalController')->getDrupalExcursion();
        $ActiveCart['CruiseInfo']['DrinkPackages'] = (array)app('App\Http\Controllers\DrupalController')->getDrupalDrinkPackage();
        $ActiveCart['CruiseInfo']['Prices'] = (array) ['Total'=>0];
        
        $this->marketInfo = app('App\Http\Controllers\DrupalController')->getMarketInfo($marketSearch);
        $ActiveCart['MarketInfo'] = (object) $this->marketInfo;
        
        $ExcPrices = app('App\Http\Controllers\DrupalController')->getPkgsPricesInfo($itercode, $this->marketInfo->MarketCode);
        $ActiveCart['CruiseInfo']['ExcursionsPrices'] = json_decode($ExcPrices, true);
        
        $bookedExc = $inclusiveExc = [];
        
        if( isset( $ActiveCart["ReservationInfo"]["Login"]["ActivityBookings"]["ActivityBooking"][0] ) ){
            foreach( $ActiveCart["ReservationInfo"]["Login"]["ActivityBookings"]["ActivityBooking"] as $keyAct => $objAct){
                if( $objAct["PriceInfo"]["GrossPriceAmount"] == 0 ){
                    $inclusiveExc[ $objAct["ActivityCode"] ] = $objAct;
                }else{
                    $bookedExc[ $objAct["ActivityCode"] ] = $objAct;
                }
            }
        }else{

            // $ActiveCart["ReservationInfo"]["Login"]["ActivityBookings"]["ActivityBooking"]
            // $bookedExc[ $objAct["ActivityCode"] ] = $objAct;
        }
        
        $locale = $this->app->getLocale();
        $ActiveCart = json_decode(json_encode($ActiveCart), true);
        
        $ExcPrice = app('App\Http\Controllers\DrupalController')->getPkgsPricesInfo($itercode, $this->marketInfo->MarketCode );
        $ExcPrice = json_decode($ExcPrice, true);
        
        // app('App\Http\Controllers\ActiveCartController')->updateCart($ActiveCart);
        // $ActiveCart = app('App\Http\Controllers\ActiveCartController')->getActiveCartManage($ActiveCart['SessionId']);
        
        if( isset($MCart['Excursions']) )
        $ActiveCart['Excursions'] = $MCart['Excursions'];
        
        // echo "<pre]>";
        // dd($ActiveCart);
        // echo "</pre]>";
        // exit;
        $DrinkPackagesContent = app('App\Http\Controllers\DrupalController')->getDrupalDrinkPackage();
        if( isset($ActiveCart["ReservationInfo"]) ){
        $MCartModel->ReservationInfo = $ActiveCart["ReservationInfo"];
        }
        $MCartModel->save();
        
        $insurances = Insurance::where('Lang', $this->app->getLocale())->get();
        $insurances = $insurances->toArray();
        $insKeyCodes = [];
        foreach($insurances as $ins){
            $insKeyCodes[$ins['Code']] = $ins;
        }

        return view('layouts_manage.pages.index')
        ->with([
        'DrinkPackages' => $DrinkPackagesContent,
        'inclusiveExc'=>$inclusiveExc,
        'bookedExc'=>$bookedExc,
        'ExcPrices'=>$ExcPrice,
        'IterCode' => $itercode,
        'lang' => $locale,
        'ActiveCart' => $ActiveCart,
        'InsurancesContent'=>$insKeyCodes
        ]);
        
        
    }
    
    public function stringarraykeys(){
        $MCart = ManageCart::Where([['BookingNo', '=', '2576161' ]])->get()->first();
        $MCart = $MCart->toArray();
        $ActiveCart['Excursions'] = [];
        foreach($MCart['Excursions'] as $eocKey => $excOnCart){
            foreach( $excOnCart[array_keys($excOnCart)[0]] as $kko=>$ko){
                $excOnCart[array_keys($excOnCart)[0]][ strval("\0".$kko) ]=$ko;
            }
            $MCart['Excursions'][$eocKey] = $excOnCart;
        }
        
        return $MCart['Excursions'];
    }
    
    /**
    * GET | Manage Booking Logout
    *
    */
    public function getManageBookingLogout(){
        $this->deleteCookie('reservation_session_manage');
        return $this->getManageBookingLogin();
    }
    
    /**
    * POST | Manage Booking Login
    *
    */
    public function postManageBookingLogin(Request $request){
        $Name = $Email = "";
        $LastnameOrEmail = $request->input('mail-lastname');
        $BookingNum = $request->input('bnum');
        if( $LastnameOrEmail && $BookingNum ){
            
            if (!filter_var($LastnameOrEmail, FILTER_VALIDATE_EMAIL) === false) {
                $Email = $LastnameOrEmail;
            } else {
                $Name = $LastnameOrEmail;
            }
            $cookieArray = json_encode( ["Name"=>$Name,"Email"=>$Email,"BookingNo"=>$BookingNum] );
            setcookie("reservation_session_manage", $cookieArray, time()+10000, "/", Config::get('booking_celestyal.booking_domain'));  //18 minutes expiration cookie
            return redirect('/manage/dashboard');
            
        }else{
            return $this->getManageBookingLogin('Booking Number is mandatory!!!');
        }
    }
    
    /**
    * POST | Save Manage Cart Details according to BookingNo
    *
    */
    public function postManageCart(Request $request){
        $ActiveCart = $request->all();
        
        $SessionId = $ActiveCart['SessionId'];
        $Excursions = $ActiveCart['Excursions'];
        $DrinkPackage = $ActiveCart['DrinkPackage'];
        $BookingNo = $ActiveCart['BookingNo'];
        // $book = app('App\Http\Controllers\ReservationController')->postBookingManage($SessionId, '', $ActiveCart, $BookingNo);
        // return json_encode($book);
        
        $MCart = ManageCart::firstOrCreate( [ 'BookingNo' => $BookingNo ] );
        $MCart->SessionId = $SessionId;
        $MCart->Excursions = json_decode(json_encode( $Excursions ) );
        $MCart->DrinkPackage = json_decode( json_encode( $DrinkPackage ) );
        $MCart->save();
        return $MCart->toArray();
        
    }
    
    public function getPaymentResponse(Request $request){
        $Inputs = $request->all();
        if( !isset( $Inputs['SessionID'] ) || !isset($Inputs['BookingNo']) || !isset( $Inputs['rslt'] ) ){
            abort(404);   
        }
        $SessionID = $Inputs['SessionID'];
        $BookingNo = $Inputs['BookingNo'];
        $rslt = $Inputs['rslt'];
        
        $MCartModel = ManageCart::Where([ ['BookingNo', '=', $BookingNo ], ['SessionId', '=', $SessionID ] ])->get()->first();
        if( isset( $MCartModel ) ){
            $ActiveCart = $MCart = $MCartModel->toArray();
            
        }
        
        
        if( $rslt == "CREDIT CARD REFUSED" ){

        }else{
            $book = app('App\Http\Controllers\ReservationController')->postBookingManage($SessionID, '', $ActiveCart, $BookingNo);
        }
        
        
        
        $itercode = $ActiveCart["ReservationInfo"]["Login"]['CruiseBookings']['CruiseSailing']['CruiseID'];
        $cruisecode = $ActiveCart["ReservationInfo"]["Login"]['CruiseBookings']['CruiseSailing']['ItineraryCode'];
        $ActiveCart['CruiseInfo']['Generic'] = (array) app('App\Http\Controllers\DrupalController')->getIterInfo( $itercode );
        $ActiveCart['CruiseInfo']['Summary'] = (array) app('App\Http\Controllers\DrupalController')->getDrupalCruise($cruisecode, $itercode);
        $ActiveCart['CruiseInfo']['ShipInfo'] = (array) app('App\Http\Controllers\DrupalController')->getDrupalShip()[0];
        $ActiveCart['CruiseInfo']['Destinations'] = (array) app('App\Http\Controllers\DrupalController')->getDrupalDestination();
        $ExcPrices = app('App\Http\Controllers\DrupalController')->getPkgsPricesInfo($itercode, $ActiveCart['ReservationInfo']['Login']['BookingContext']['MarketCode'] );
        $ActiveCart['CruiseInfo']['ExcursionsPrices'] = json_decode($ExcPrices, true);
        
        return view('layouts_manage.confirm')
        ->with([
        // 'DrinkPackages' => $DrinkPackagesContent,
        // 'inclusiveExc'=>$inclusiveExc,
        // 'bookedExc'=>$bookedExc,
        // 'ExcPrices'=>$ExcPrice,
        'IterCode' => '',
        'lang' => $this->app->getLocale(),
        'ActiveCart' => $ActiveCart,
        ]);
    }
    
    public function postManageBookingChange(Request $request){
        print_r($request);
    }
    
}