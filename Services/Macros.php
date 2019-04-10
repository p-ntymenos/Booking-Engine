<?php namespace App\Services;

use Collective\Html\FormBuilder;

class Macros extends FormBuilder {
    
    
    public function selectExpiration($name, $selected = null, $options = array()){
        
        $optionsDay = $optionsMonth = $optionsYear = $options;
        $optionsDay['id'] = 'exp-day-'.$optionsDay['id'];
        $optionsMonth['id'] = 'exp-month-'.$optionsMonth['id'];
        $optionsYear['id'] = 'exp-year-'.$optionsYear['id'];
        
        $optionsDay['ng-model'] = $optionsDay['group-model'].".day";
        $optionsMonth['ng-model'] = $optionsDay['group-model'].".month";
        $optionsYear['ng-model'] = $optionsDay['group-model'].".year";
        
        $optionsDay['data-placeholder'] = 'DD';
        $optionsMonth['data-placeholder'] = 'MM';
        $optionsYear['data-placeholder'] = 'YYYY';
        
        $listDays = ['' => '', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20', '21' => '21', '22' => '22', '23' => '23', '24' => '24', '25' => '25', '26' => '26', '27' => '27', '28' => '28', '29' => '29', '30' => '30', '31' => '31'];
        $listMonths = ['' => '', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12'];
        
        $listYears = [];
        
        $earliest_year = date('Y');
        $listYears[''] = '';
        foreach (range($earliest_year, $earliest_year+20, 1) as $x) {
            $listYears[$x] = $x;
        }
        
        $markup1 =  $this->select($optionsDay['ng-model'], $listDays, $selected, $optionsDay);
        $markup2 =  $this->select($optionsMonth['ng-model'], $listMonths, $selected, $optionsMonth);
        $markup3 =  $this->select($optionsYear['ng-model'], $listYears, $selected, $optionsYear);
        
        return $this->toHtmlString($markup1.$markup2.$markup3);
    }
    
    public function selectDOB($name, $selected = null, $options = array()){
        
        $optionsDay = $optionsMonth = $optionsYear = $options;
        $optionsDay['id'] = 'date-birth-day-'.trim($optionsDay['id']);
        $optionsMonth['id'] = 'date-birth-month-'.trim($optionsMonth['id']);
        $optionsYear['id'] = 'date-birth-year-'.trim($optionsYear['id']);
        
        $optionsDay['ng-model'] = $optionsDay['group-model'].".day";
        $optionsMonth['ng-model'] = $optionsDay['group-model'].".month";
        $optionsYear['ng-model'] = $optionsDay['group-model'].".year";
        
        $optionsDay['data-placeholder'] = 'DD';
        $optionsMonth['data-placeholder'] = 'MM';
        $optionsYear['data-placeholder'] = 'YYYY';
        
        if( isset($optionsDay['tabindex']) ){
            $tabindex = $optionsDay['tabindex'];
            $optionsDay['tabindex'] = $tabindex;
            $optionsMonth['tabindex'] = $tabindex+1;
            $optionsYear['tabindex'] = $tabindex+2;
        }else{
            $tabindex = 1;
        }
        
        
        $listDays = ['' => '', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20', '21' => '21', '22' => '22', '23' => '23', '24' => '24', '25' => '25', '26' => '26', '27' => '27', '28' => '28', '29' => '29', '30' => '30', '31' => '31'];
        $listMonths = ['' => '', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12'];
        
        $listYears = [];
        if(!isset($options['startYear'])){
            $options['startYear']=1920;
        }
        
        $earliest_year = $options['startYear'];
        $listYears[''] = '';
        if( !isset($options['isAdult']) ){
            foreach (range(date('Y'), $earliest_year) as $x) {
                $listYears[$x] = $x;
            }
        }else{
            foreach (range(date('Y', strtotime(date('Y').' -18 year')), $earliest_year) as $x) {
                $listYears[$x] = $x;
            }
            
        }
        $markup1 =  $this->select($optionsDay['ng-model'], $listDays, $selected, $optionsDay);
        $markup2 =  $this->select($optionsMonth['ng-model'], $listMonths, $selected, $optionsMonth);
        $markup3 =  $this->select($optionsYear['ng-model'], $listYears, $selected, $optionsYear);
        
        return $this->toHtmlString($markup1.$markup2.$markup3);
    }
    
    public function selectCountries($name, $selected = '', $options = []){
        // $countriesArray= [""=>"",
        // "AD"=>"Andorra","AE"=>"United Arab Emirates","AF"=>"Afghanistan","AG"=>"Antigua &amp; Barbuda","AI"=>"Anguilla","AL"=>"Albania","AM"=>"Armenia","AN"=>"Netherlands Antilles","AO"=>"Angola","AP"=>"Asia/Pacific Region","AQ"=>"Antarctica","AR"=>"Argentina","AS"=>"American Samoa","AT"=>"Austria","AU"=>"Australia","AW"=>"Aruba","AX"=>"Aland Islands","AZ"=>"Azerbaijan","BA"=>"Bosnia &amp; Herzegovina","BB"=>"Barbados","BD"=>"Bangladesh","BE"=>"Belgium","BF"=>"Burkina Faso","BG"=>"Bulgaria","BH"=>"Bahrain","BI"=>"Burundi","BJ"=>"Benin","BM"=>"Bermuda","BN"=>"Brunei Darussalam","BO"=>"Bolivia","BR"=>"Brazil","BS"=>"Bahamas","BT"=>"Bhutan","BV"=>"Bouvet Island","BW"=>"Botswana","BY"=>"Belarus","BZ"=>"Belize","CA"=>"Canada","CC"=>"Cocos (Keeling) Islands","CD"=>"Congo  The Democratic Rep","CE"=>"Cernobbio","CF"=>"Central African Republic","CG"=>"Congo","CH"=>"Switzerland","CI"=>"Ivory Coast","CK"=>"Cook Islands","CL"=>"Chile","CM"=>"Cameroon  Republic of ","CN"=>"China","CO"=>"Colombia","CR"=>"Costa Rica","CS"=>"Serbia and Montenegro","CU"=>"Cuba","CV"=>"Cape Verde","CX"=>"Christmas Island (Australia)","CY"=>"Cyprus","CZ"=>"Czech Republic","DE"=>"Germany","DJ"=>"Djibouti","DK"=>"Denmark","DM"=>"Dominica","DO"=>"Dominican Republic","DZ"=>"Algeria","EC"=>"Ecuador","EE"=>"Estonia","EG"=>"Egypt","EH"=>"Western Sahara","EI"=>"Eindhoven","EL"=>"Basel","ER"=>"Eritrea","ES"=>"Spain","ET"=>"Ethiopia","FI"=>"Finland","FJ"=>"Fiji","FK"=>"Falkland Islands","FM"=>"Micronesia","FO"=>"Faroe Islands","FR"=>"France","GA"=>"Gabon","GB"=>"United Kingdom","GD"=>"Grenada","GE"=>"Georgia","GF"=>"French Guiana","GG"=>"Guernsey","GH"=>"Ghana","GI"=>"Gibraltar","GL"=>"Greenland","GM"=>"Gambia","GN"=>"Guinea","GP"=>"Guadeloupe","GQ"=>"Equatorial Guinea","GR"=>"Greece","GT"=>"Guatemala","GU"=>"Guam","GW"=>"Guinea-Bissau","GY"=>"Guyana","HA"=>"Hague","HK"=>"Hong Kong","HM"=>"Heard and McDonald Islands ","HN"=>"Honduras","HR"=>"Croatia","HT"=>"Haiti","HU"=>"Hungary","ID"=>"Indonesia","IE"=>"Ireland","IL"=>"Israel","IM"=>"Isle of Man","IN"=>"India","IO"=>"British Indian Ocean Territory ","IQ"=>"Iraq","IR"=>"Iran","IS"=>"Iceland","IT"=>"Italy","JE"=>"Jersey","JM"=>"Jamaica","JO"=>"Jordan","JP"=>"Japan","KAL"=>"Kalutara","KE"=>"Kenya","KG"=>"Kyrgyzstan","KH"=>"Cambodia","KI"=>"Kiribati","KM"=>"Comoros","KN"=>"Saint Kitts and Nevis","KP"=>"Korea  Dem. Peoples Repub","KR"=>"Korea  Republic of ","KW"=>"Kuwait","KY"=>"Cayman Islands","KZ"=>"Kazakhstan","LA"=>"Lao Peoples Democratic Re ","LB"=>"Lebanon","LC"=>"St. Lucia","LG"=>"Malaga","LI"=>"Liechtenstein","LK"=>"Sri Lanka","LR"=>"Liberia","LS"=>"Lesotho","LT"=>"Lithuania","LU"=>"Luxembourg","LV"=>"Latvia","LY"=>"Libya","MA"=>"Morocco","MC"=>"Monaco","MD"=>"Moldova","ME"=>"Montenegro","MF"=>"Saint Martin","MG"=>"Madagascar","MH"=>"Marshall Islands","MI"=>"Mexico City","MK"=>"Macedonia Former Yugosla","ML"=>"Mali","MM"=>"Myanmar Union of ","MN"=>"Mongolia","MO"=>"Macau","MP"=>"Northern Mariana Islands","MQ"=>"Martinique","MR"=>"Mauritania","MS"=>"Montserrat","MT"=>"Malta","MU"=>"Mauritius","MV"=>"Maldives","MW"=>"Malawi","MX"=>"Mexico","MY"=>"Malaysia","MZ"=>"Mozambique","NA"=>"Namibia","NC"=>"New Caledonia","ND"=>"New Zealand ","NF"=>"Norfolk Island","NG"=>"Nigeria","NH"=>"Nottingham","NI"=>"Nicaragua","NL"=>"Netherlands","NO"=>"Norway","NP"=>"Nepal","NR"=>"Nauru","NT"=>"Neutral Zone (S.Arabia/Ir)","NU"=>"Niue","NZ"=>"New Zealand ","OM"=>"Oman","PA"=>"Panama","PE"=>"Peru","PF"=>"French Polynesia","PG"=>"Papua New Guinea","PH"=>"Philippines","PK"=>"Pakistan","PL"=>"Poland","PM"=>"St. Pierre and Miquelon","PMI"=>"Palma De Mallorca","PN"=>"Pitcairn","PR"=>"Puerto Rico","PT"=>"Portugal","PW"=>"Palau","PY"=>"Paraguay","QA"=>"Qatar","RA"=>"Rabat","RE"=>"Reunion","RO"=>"Romania","RS"=>"Serbia","RU"=>"Russia","RW"=>"Rwanda","SA"=>"Saudi Arabia","SB"=>"Solomon Islands","SC"=>"Seychelles","SD"=>"Sudan","SE"=>"Sweden","SG"=>"Singapore","SH"=>"St. Helena","SI"=>"Slovenia","SJ"=>"Svalbard and Jan Mayen Islands","SK"=>"Slovakia","SL"=>"Sierra Leone","SM"=>"San Marino","SN"=>"Senegal","SO"=>"Somalia","SR"=>"Suriname","ST"=>"Sao Tome and Principe","SV"=>"El Salvador","SY"=>"Syrian Arab Republic","SZ"=>"Swaziland","TA"=>"Tallinn","TC"=>"Turks and Caicos Islands","TD"=>"Chad","TF"=>"French Southern Territories","TG"=>"Togo","TH"=>"Thailand","TJ"=>"Tajikistan","TK"=>"Tokelau","TL"=>"Timor-Leste","TM"=>"Turkmenistan","TN"=>"Tunisia","TO"=>"Tonga","TP"=>"East Timor","TR"=>"Turkey","TT"=>"Trinidad and Tobago","TV"=>"Tuvalu","TW"=>"Taiwan Province of China","TZ"=>"Tanzania","UA"=>"Ukraine","UG"=>"Uganda","US"=>"United States","UY"=>"Uruguay","UZ"=>"Uzbekistan","VA"=>"Vatican City State","VC"=>"St. Vincent and the Grena","VE"=>"Venezuela","VG"=>"Virgin Islands British","VI"=>"Virgin Islands U.S","VN"=>"Vietnam","VU"=>"Vanuatu","WF"=>"Wallis and Futuna","WK"=>"Scotland","WS"=>"Samoa","YE"=>"Yemen Republic","YT"=>"Mayotte","ZA"=>"South Africa","ZM"=>"Zambia","ZW"=>"Zimbabwe"];
        $countriesArray = \Lang::get('dropdowns.Countries');
        
        return $this->select($name, $countriesArray, $selected, $options);
    }
    
    public function selectLanguages($name, $selected = '', $options = []){
        // $languageArray =[""=>"","BOS"=>"Bosnian","BRG"=>"Bulgarian","CRO"=>"Croatian","DUT"=>"Dutch","ENG"=>"English","FIN"=>"Finnish","FLE"=>"Flemish","FRE"=>"French","GER"=>"German","GRE"=>"Greek","HUN"=>"Hungerian","ITA"=>"Italian","JAP"=>"Japanese","KOR"=>"Korean","MLT"=>"Maltese","POL"=>"Polish","POR"=>"Portuguse","ROM"=>"Romanian","RUS"=>"Russian","SER"=>"Serbian","SLO"=>"Slovenian","ESP"=>"Spanish","SVK"=>"Slovakina","SWE"=>"Swedish","TUR"=>"Turkish"];

        $languageArray = \Lang::get('dropdowns.Languages');
        
        return $this->select($name, $languageArray, $selected, $options);
    }
    
    public function selectNationality($name, $selected = '', $options = []){
        // $nationalityArray = [""=>"","ALB"=>"Albanian","DZA"=>"Algerian","USA"=>"American","AND"=>"Andorran","AGO"=>"Angolan","ARG"=>"Argentine","ARM"=>"Armenian","AUS"=>"Australian","AUT"=>"Austrian","AZE"=>"Azerbaijan","BHS"=>"Bahamas","BHR"=>"Bahraini","BGD"=>"Bangladeshi","BRB"=>"Barbados","BLR"=>"Belarusian","BEL"=>"Belgian","BLZ"=>"Belizean","BEN"=>"Benin","BOL"=>"Bolivian","BIH"=>"Bosnian","BRA"=>"Brazilian","GBR"=>"British","BGR"=>"Bulgarian","BFA"=>"Burkina Faso","CMR"=>"Cameroonian","CAN"=>"Canadian","CHL"=>"Chilean","CHN"=>"Chinese","COL"=>"Colombian","COM"=>"Comoros","COD"=>"Congolese","CRI"=>"Costa Rican","HRV"=>"Croatian","CUB"=>"Cuban","CYP"=>"Cypriot","CZE"=>"Czeck","DNK"=>"Danish","DOM"=>"Dominican","NLD"=>"Dutch","ECU"=>"Ecuadorian","EGY"=>"Egyptian","SLV"=>"El Salvadoran","ERI"=>"Eritrean","EST"=>"Estonia","ETH"=>"Ethiopian","FJI"=>"Fijian","FIN"=>"Finnish","FRA"=>"French","MKD"=>"Fyr","GEO"=>"Georgian","D"=>"German","GHA"=>"Ghana","GRC"=>"Greek","GRD"=>"Grenadian","GTM"=>"Guatemalan","GIN"=>"Guinea","HTI"=>"Haiti","HAI"=>"Haitian","HND"=>"Honduran","HKG"=>"Hong Kong","HUN"=>"Hungarian","ISL"=>"Icelander","IND"=>"Indian","IDN"=>"Indonesian","IRN"=>"Iranian","IRQ"=>"Iraq","IRL"=>"Irish","ISR"=>"Israeli","ITA"=>"Italian","CIV"=>"Ivory Coast","JAM"=>"Jamaican","JPN"=>"Japanese","JOR"=>"Jordanian","KAZ"=>"Kazakhstani","KEN"=>"Kenyan","KOR"=>"Korean","KWT"=>"Kuwaiti","KGZ"=>"Kyrgyzstan","LVA"=>"Latvian","LBN"=>"Lebanese","LSO"=>"Lesotho","LBR"=>"Liberian","LBY"=>"Libyan","LIE"=>"Liechtensteiner","LTU"=>"Lithuanian","LUX"=>"Luxembourger","MDG"=>"Madagascar","MYS"=>"Malaysian","MDV"=>"Maldives","MLI"=>"Mali","MLT"=>"Maltese","MAR"=>"Maroccian","MRT"=>"Mauritian","MUS"=>"Mauritius","MEX"=>"Mexican","MOL"=>"Moldovan","MCO"=>"Monacan","MOZ"=>"Mozambican","MMR"=>"Myanmar","NAM"=>"Namibia","NAP"=>"Nepal","NZL"=>"New Zealander","OMN"=>"Oman","OND"=>"On Duran","PAK"=>"Pakistani","PSE"=>"Palestinian","PAL"=>"Palestinian","PAN"=>"Panamanian","PNG"=>"Papua New Guinea","PRY"=>"Paraguayan","PER"=>"Peruvian","PHL"=>"Philippino","POL"=>"Polish","PRT"=>"Portuguese","PUE"=>"Puerto Rican","QAT"=>"Qatari","ROM"=>"Romanian","RUS"=>"Russian","SAR"=>"S.Arabian","KNA"=>"Saint Kitts And Nevi","SAL"=>"Salvadorian","SAU"=>"Saudi Arabian","SEN"=>"Senegalese","SRB"=>"Serbian","SYC"=>"Seychelles","SGP"=>"Singaporean","FYR"=>"Skopjan","SVK"=>"Slovakian","SVN"=>"Slovenian","SOM"=>"Somalia","ZAF"=>"South African","ESP"=>"Spanish","LKA"=>"Sri Lankan","SDN"=>"Sudanese","SUR"=>"Suriname","SWE"=>"Swedish","CHE"=>"Swiss","SYR"=>"Syrian","TWN"=>"Taiwan","TAN"=>"Tanzanian","THA"=>"Thai","TGO"=>"Togo","TTO"=>"Trinidad And Tobago","TRI"=>"Trinidadian","TUN"=>"Tunisian","TUR"=>"Turkish","UGA"=>"Uganda","UKR"=>"Ukrainian","ARE"=>"United Arab Emirates","URY"=>"Uruguayan","UZB"=>"Uzbekistan","VEN"=>"Venezuelan","VNM"=>"Vietnamese","YEM"=>"Yemen","YUG"=>"Υugaslavian"];

        $nationalityArray = \Lang::get('dropdowns.Nationalities');
        return $this->select($name, $nationalityArray, $selected, $options);
    }
    
    public function dateDiff($start, $end) {
        $start_ts = strtotime($start);
        $end_ts = strtotime($end);
        $diff = $end_ts - $start_ts;
        return round($diff / 86400)+1;
    }
}