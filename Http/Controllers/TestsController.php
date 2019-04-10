<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Cookie;
use App\Models\Admin\Cart;
use App\Models\Admin\UserSessions;

class TestsController extends Controller
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
        $this->marketInfo = app('App\Http\Controllers\DrupalController')->getMarketInfo();
        // if(!isset($_COOKIE["reservation_session"])){
        //     $sessionId = app('App\Http\Controllers\ReservationController')->getLoginFromBooking($this->marketInfo->AgencyId, $this->marketInfo->AgencyId);
        // }
        //$this->activeCart = (object) app('App\Http\Controllers\ActiveCartController')->create();
        
        
    }
    public function TestBooking(Request $request){
        
        $locale = $this->app->getLocale();
        
        $itercode = 'LO03170630';
        // $ActiveCart = app('App\Http\Controllers\ActiveCartController')->create();
        // $sessionId = $ActiveCart['SessionId'];
        $sessionId = "c6cd6140-ce7a-b3b6-e711-661f6aac2776";
        
        
        //Step 2
        $cruiseInfo = app('App\Http\Controllers\ReservationController')->getShopRequestMessage($itercode, $sessionId, 2);
        $cruiseCode = $cruiseInfo->CruiseProducts['CruiseSailing']['CruiseInfo']['ItineraryCode'];
        
        $DrupalCruise = app('App\Http\Controllers\DrupalController')->getDrupalCruise($cruiseCode);
        
        app('App\Http\Controllers\ActiveCartController')->setActiveCart($cruiseCode, 'CruiseCode');
        
        
        $componentId = $cruiseInfo->CruiseProducts['CruiseSailing']['ComponentInfo']['ComponentID'];
        
        app('App\Http\Controllers\ActiveCartController')->setActiveCart(json_encode(["ComponentId" => $componentId]), 'CruiseInfo');
        
        $StartDate = $cruiseInfo->CruiseProducts['CruiseSailing']['CruiseInfo']['SailingDate'];
        $EndDate = $cruiseInfo->CruiseProducts['CruiseSailing']['CruiseInfo']['ReturnDate'];
        $StartDate = date('d/m/Y',strtotime($StartDate));
        $EndDate = date('d/m/Y',strtotime($EndDate));
        
        //Step 3
        $pricingInfo = app('App\Http\Controllers\ReservationController')->getPricingAvailabilityRequest($componentId, $sessionId);
        
        
        //Step 4
        $categoryInfo = app('App\Http\Controllers\ReservationController')->getCategoryAvailabilityRequest($sessionId, $pricingInfo->CruiseComponent['ComponentID'], $pricingInfo->PricingPromotionsAvailable['CruisePromotionCode']);
        
        //Step 5
        $cabinInfo = app('App\Http\Controllers\ReservationController')->getCruiseCabinAvailabilityRequest($sessionId, $pricingInfo->CruiseComponent['ComponentID'], $categoryInfo->AvailableCategories['AvailableCategory'][0]['CategoryCode']);
        $CabinNo = $cabinInfo->AvailableCabins['AvailableCabin'][0]['CabinNo'];
        $CategoryCode = $cabinInfo->SelectedCategory['CategoryCode'];
        
        //Step 6
        $packagesInfo = app('App\Http\Controllers\ReservationController')->getAssocItemsListRequestMessage($pricingInfo->CruiseComponent['ComponentID'], $sessionId);
        //$exc = $packagesInfo->ActivityProducts['ActivityOccurence'][0]['ComponentInfo']['ComponentID'];
        //Step 7
        $insuranceInfo = app('App\Http\Controllers\ReservationController')->getInsuranceId($sessionId, $pricingInfo->CruiseComponent['ComponentID'], $StartDate, $EndDate);
        
        
        
        $excursionComponentID = $packagesInfo->ActivityProducts['ActivityOccurence'][0]['ComponentInfo']['ComponentID'];
        
        
        //Step 7 Reprice
        $repriceItems = [];
        $repriceItems = app('App\Http\Controllers\ReservationController')->repriceItemRequest($sessionId, $pricingInfo->CruiseComponent['ComponentID']);
        
        //Step 8
        $postBooking = [];
        $postBooking = app('App\Http\Controllers\ReservationController')->
        postTestBooking($sessionId, $pricingInfo->CruiseComponent['ComponentID'], $CategoryCode, $CabinNo, $excursionComponentID);
        
        return view('layouts_booking.debug.book')->with([
        'lang' => $locale,
        'cruiseInfo' => $cruiseInfo,
        'pricingInfo' => $pricingInfo,
        'categoryInfo' => $categoryInfo,
        'cabinInfo' => $cabinInfo,
        'packagesInfo'=> $packagesInfo,
        'insuranceInfo' => $insuranceInfo,
        'cruiseInfoFromDrupal' => [],
        'repriceItems' => $repriceItems,
        'postBooking'=> $postBooking
        
        ]);
        
        
    }
    
    public function TestManageBooking(){
        
        $locale = $this->app->getLocale();
        $cruiseInfo = $cruiseInfo = $pricingInfo = $categoryInfo = $cabinInfo = $packagesInfo = $insuranceInfo = [];
        $repriceItems = [];
        $postBooking = [];

        $BookingNo = '2576141';//'2498834';
        $Email = 'pantelis@backbone.gr';
        $Name = '';
        $postBooking = app('App\Http\Controllers\ReservationController')->manageBooking($BookingNo, $Email, $Name);

        $SessionId = $postBooking->SessionInfo['SessionID'];
        $ComponentId = $postBooking->CruiseBookings['CruiseSailing']['ComponentInfo']['ComponentID'];
        

        $packagesInfo = app('App\Http\Controllers\ReservationController')->getAssocItemsListRequestMessage($ComponentId, $SessionId);

        return view('layouts_booking.debug.manage')->with([
        'lang' => $locale,
        'cruiseInfo' => $cruiseInfo,
        'pricingInfo' => $pricingInfo,
        'categoryInfo' => $categoryInfo,
        'cabinInfo' => $cabinInfo,
        'packagesInfo'=> $packagesInfo,
        'insuranceInfo' => $insuranceInfo,
        'cruiseInfoFromDrupal' => [],
        'repriceItems' => $repriceItems,
        'postBooking'=> $postBooking
        
        ]);
    }
}