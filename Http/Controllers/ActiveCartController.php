<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Http\Requests\API\CreateCartAPIRequest;
use App\Http\Requests\API\UpdateCartAPIRequest;
use App\Models\Admin\Cart;
use App\Repositories\CartRepository;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use DB;
use Illuminate\Support\Facades\Config;




class ActiveCartController extends Controller
{
    
    protected $app, $activeCart;
    
    public function __construct(){
        
        
        
    }
    
    //Get session and create cart
    public function create($itercode = null){
        $activeCart = [];
        $sessionId = app('App\Http\Controllers\ReservationController')->getLoginFromBooking($AgencyID = '0000012', $AgentID = '0000012');
        //$sessionId = "8c8d1334-309b-319e-e711-7333f440ce94";
        
        if($sessionId){
            //setcookie("reservation_session", $sessionId, time()+1080, "/", Config::get('booking_celestyal.booking_domain'));  //18 minutes expiration cookie
            setcookie("reservation_session", $sessionId, time()+2580, "/", Config::get('booking_celestyal.booking_domain'));  //18 minutes expiration cookie + 25 (wrong time on server)
            $Cart = Cart::firstOrCreate( [ 'SessionId' => $sessionId ] );
            $Cart->IterCode = $itercode;
            $Cart->save();
            $activeCart = $Cart->toArray();
        }else{
            abort(500);
        }
        return $activeCart;
    }
    
    public function getActiveCart(){
        $cart = [];
        if(isset($_COOKIE["reservation_session"])){
            $activeSession = $_COOKIE["reservation_session"];
            $activeCart = Cart::Where([['SessionId', '=', $activeSession]])->get()->first();
            $cart = $activeCart->toArray();
        }
        return $cart;
    }
    
    public function updateCart($cart){
        
        if($cart['SessionId']){
            $Cart = Cart::Where([['SessionId', '=', $cart['SessionId']]])->first();
            if(isset($cart['IterCode'])) $Cart->IterCode = $cart['IterCode'];
            if(isset($cart['ReservationInfo']))$Cart->ReservationInfo = $cart['ReservationInfo'];
            if(isset($cart['CruiseInfo'])) $Cart->CruiseInfo = $cart['CruiseInfo'];
            if(isset($cart['MarketInfo'])) $Cart->MarketInfo = $cart['MarketInfo'];
            if(isset($cart['Adults'])) $Cart->Adults = $cart['Adults'];
            if(isset($cart['Children'])) $Cart->Children = $cart['Children'];
            if(isset($cart['Staterooms'])) $Cart->Staterooms = $cart['Staterooms'];
            if(isset($cart['Excursions'])) $Cart->Excursions = $cart['Excursions'];
            if(isset($cart['Services'])) $Cart->Services = $cart['Services'];
            if(isset($cart['DrinkPackage'])) $Cart->DrinkPackage = $cart['DrinkPackage'];
            if(isset($cart['Insurance'])) $Cart->Insurance = $cart['Insurance'];
            
            
            
            
            $Cart->save();
            //DB::table('cart')->whereIn('id', $cart['id'])->update($cart);
        }
        //$this->activeCart
        
    }
    
    public function setActiveCart($value, $parameter){
        $return = "not found";
        if(isset($_COOKIE["reservation_session"])){
            $activeSession = $_COOKIE["reservation_session"];
            $activeCart = Cart::Where([['SessionId', '=', $activeSession]])->get();
            if(isset($activeCart[0])){
                $activeCart[0]->{$parameter} = $value;
                $activeCart[0]->save();
                $return = "found";
            }
        }
        return $return;
    }
    
    public function getActiveCartJson(){
        $data = $this->index();
        return response()->json($data);
    }
    
    public function createManageBookingCart($SessionId){
        $Cart = Cart::firstOrCreate( [ 'SessionId' => $SessionId ] );
        $Cart->Step = -1; //it's the flag for managing cart'
        $Cart->SessionId = $SessionId;
        $Cart->save();
        return $Cart->toArray();
    }

    public function getActiveCartManage($SessionId){
        $activeCart = Cart::Where([['SessionId', '=', $SessionId]])->get()->first();
        $cart = $activeCart->toArray();
        return $cart;
    }
    
}