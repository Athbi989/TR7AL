<?PHP
/* 
    App : TRHAL (APi)
    Description : project for code.kw ( TRHAL ) 
    About this file : Arrange and improve access from various datasets,apis....etc (no local database)
    Other : --> Everything created from scratch by Athbi
*/
date_default_timezone_set('Asia/Kuwait');
class Trhal {
    /* Initlaze Class */
    public $GOOGLE_PLACE = "https://maps.googleapis.com/maps/api/place/";
    public $GOOGLE_API_KEY ;
    public $RAPIDAPI_API_KEY ;
    

	function __construct($googleApi,$rapApi) {
        if (empty($googleApi) || empty($rapApi)) {  
			throw new Exception('Insert Keys !');
		}
        $this->GOOGLE_API_KEY = $googleApi;
        $this->RAPIDAPI_API_KEY = $rapApi;
	}
	
	function curlRequest($url,$headers = null) {
		if ($url != "" || !empty ($url) ) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"$url");
			//curl_setopt($ch, CURLOPT_POST, 1);
			//curl_setopt($ch, CURLOPT_POSTFIELDS,$vars);  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if (gettype($headers) == "array" && $headers != null) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			}				
			$respone = curl_exec ($ch);
			curl_close ($ch);
			return $respone;
		}
	}
	
	function rapidRequest($url,$headers = null) {
		if ($url != "" || !empty ($url) ) {
			if ($headers == null ) {
				$headers = array("x-rapidapi-host: ","x-rapidapi-key: " . $this->RAPIDAPI_API_KEY);
			}
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"$url");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if (gettype($headers) == "array" && $headers != null) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			}				
			$respone = curl_exec ($ch);
			curl_close ($ch);
			return $respone;
		}
	}

	
	/* Basic Country Methods */
	
	function getCountry($code) {
		//Get Country data by 2 letter code (KW)
		if ($code != "" || !empty ($code) ) {
			$call = $this->curlRequest("https://restcountries.eu/rest/v2/alpha/".$code."");
			$jsonIt = json_decode($call,true);
			return array("status"=>true,"result"=>
			array (
				"id" => $jsonIt["alpha2Code"],
				"name" => $jsonIt["name"],
				"region" => $jsonIt["region"],
				"capitalCity" => $jsonIt["capital"],
				"population" => $jsonIt["population"],
				"label" => $jsonIt["regionalBlocs"][0]["otherNames"][0],
				"latitude" => $jsonIt["latlng"][0],
				"longitude" => $jsonIt["latlng"][1]
				)
			) ;
		} else {
			return array("status"=>false,"msg"=>"One of the argument is empty") ;
		}
	}
	
	
	function getCountryBanner ($query,$width,$height) {
		if ($query != "" || !empty ($query) ) {
			$URL = $this->GOOGLE_PLACE . "textsearch/json?query=".urlencode($query)."&language=en&key=".$this->GOOGLE_API_KEY;
			$call = $this->curlRequest($URL);
			$jsonIt = json_decode($call,true);
			$photo_ref = $jsonIt["results"][0]["photos"][0]["photo_reference"] ;
			return "https://maps.googleapis.com/maps/api/place/photo?maxwidth=".$width."&maxheight=".$height."&photoreference=".$photo_ref."&key=".$this->GOOGLE_API_KEY ;
		} else {
			return array("status"=>false,"msg"=>"One of the argument is empty") ;
		}
	}
	
	
	function getCovid19 ($code) {
		if ($code != "" || !empty ($code) ) {
			$call = $this->rapidRequest("https://covid-19-data.p.rapidapi.com/country/code?format=json&code=" . $code);
			$jsonIt = json_decode($call,true);
			return array("status"=>true,"result"=>
			array (
				"confirmed" => $jsonIt[0]["confirmed"],
				"recovered" => $jsonIt[0]["recovered"],
				"critical" => $jsonIt[0]["critical"],
				"deaths" => $jsonIt[0]["deaths"]
				)
			) ;
		} else {
			return array("status"=>false,"msg"=>"One of the argument is empty") ;
		}					
	}
	
	
	function getCovid19_Restrecionts ($target) {
		if ($target != "" || !empty ($target) ) {
			$call = $this->curlRequest("https://www.trackcorona.live/api/travel");
			$jsonIt = json_decode($call,true);
			foreach ($jsonIt["data"] as $country) {
				if ($country["location"] === $target) {
					return $country["data"];
				}
			}
		} else {
			return array("status"=>false,"msg"=>"One of the argument is empty") ;
		}					
	}

	function getGlobalCovid19 () {
		$call = $this->rapidRequest("https://covid-19-data.p.rapidapi.com/totals?format=json");
		$jsonIt = json_decode($call,true);
		return array("status"=>true,"result"=>
		array (
			"confirmed" => $jsonIt[0]["confirmed"],
			"recovered" => $jsonIt[0]["recovered"],
			"critical" => $jsonIt[0]["critical"],
			"deaths" => $jsonIt[0]["deaths"]
			)
		) ;
	}
	
	
	
	
	/* Place - Attraction Country Methods */

	function getPointsOfInterst ($query) {
		if ($query != "" || !empty ($query) ) {
			$URL = $this->GOOGLE_PLACE . "textsearch/json?query=".urlencode($query)."+points+of+interest&language=ar&key=".$this->GOOGLE_API_KEY;
			$call = $this->curlRequest($URL);
			$jsonIt = json_decode($call,true);
			return $jsonIt;

		} else {
			return array("status"=>false,"msg"=>"One of the argument is empty") ;
		}					
	}
 
	function getGooglePlacePhoto ($ref,$width = 400) {
		if ($ref != "" || !empty ($ref) ) {
		$Url = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=".$width."&photoreference=".$ref."&key=".$this->GOOGLE_API_KEY;
		return $Url;
		} else {
			return array("status"=>false,"msg"=>"One of the argument is empty") ;
		}					
	}
	function getGooglePlaceURL ($ref) {
		if ($ref != "" || !empty ($ref) ) {
		$Url = "https://www.google.com/maps/search/?api=1&query=Google&query_place_id=".$ref;
		return $Url;
		} else {
			return array("status"=>false,"msg"=>"One of the argument is empty") ;
		}					
	}
	
	function getPlacesByType ($query,$type) {
		if ($type != "" || !empty ($type) ) {
			$URL = $this->GOOGLE_PLACE . "textsearch/json?query=".urlencode($query)."&type=".$type."&language=ar&key=".$this->GOOGLE_API_KEY;
			$call = $this->curlRequest($URL);
			$jsonIt = json_decode($call,true);
			return $jsonIt;
		} else {
			return array("status"=>false,"msg"=>"One of the argument is empty") ;
		}					
	}
	
 	/* Place - Coustm call */
	
	function getListPlaces ($query,$type = "points+of+interest",$page = null) {
		if ($query != "" || !empty ($query) ) {
			$pageToken = "";
			if ($page != null ) {
				$pageToken = "&pagetoken=".$page;
			}
			$URL = $this->GOOGLE_PLACE . "textsearch/json?query=".urlencode($query). "+". urlencode($type) ."&language=ar&key=".$this->GOOGLE_API_KEY . $pageToken;
			$call = $this->curlRequest($URL);
			$jsonIt = json_decode($call,true);
			return $jsonIt;
		} else {
			return array("status"=>false,"msg"=>"One of the argument is empty") ;
		}					
	}
	
	/* Hotels Country Methods */

	function getHotels_TripAdvisor ($query,$limt = 25) {
		if ($query != "" || !empty ($query) ) {
			$call = $this->rapidRequest("https://tripadvisor1.p.rapidapi.com/locations/search?location_id=1&limit=1&sort=relevance&offset=0&lang=en_US&currency=USD&units=km&query=".urlencode($query));
			$jsonIt = json_decode($call,true);
			$LoactionID = $jsonIt["data"][0]["result_object"]["location_id"]; 
			if ($LoactionID != null && !empty($LoactionID)) {
				$date = date("Y-m-d");
				$AdjustTiming = date('d/m/Y', strtotime($date. ' + 3 days'));
				$URL = "https://tripadvisor1.p.rapidapi.com/hotels/list?location_id=".$LoactionID."&adults=1&checkin=".$AdjustTiming."&rooms=1&nights=2&currency=KWD&limit=30&order=asc&lang=en_US&sort=recommended&offset=0";
				$call = $this->rapidRequest($URL);
				$jsonIt = json_decode($call,true);
				return $jsonIt;
			} else {
				return array("status"=>false,"msg"=>"Query not found") ;
			}
		} else {
			return array("status"=>false,"msg"=>"One of the argument is empty") ;
		}					
	}
	
	function getHotelsList_TripAdvisor ($query,$checkIn,$nights = 2,$page) {
		if ($query != "" || !empty ($query) ) {
			$call = $this->rapidRequest("https://tripadvisor1.p.rapidapi.com/locations/search?location_id=1&limit=1&sort=relevance&offset=0&lang=en_US&currency=USD&units=km&query=".urlencode($query));
			$jsonIt = json_decode($call,true);
			$LoactionID = $jsonIt["data"][0]["result_object"]["location_id"]; 
			if ($LoactionID != null && !empty($LoactionID)) {
				$URL = "https://tripadvisor1.p.rapidapi.com/hotels/list?location_id=".$LoactionID."&adults=1&checkin=".$checkIn."&rooms=1&nights=".$nights."&currency=KWD&limit=30&order=asc&lang=en_US&sort=recommended&offset=".$page;
				$call = $this->rapidRequest($URL);
				$jsonIt = json_decode($call,true);
				return $jsonIt;
			} else {
				return array("status"=>false,"msg"=>"Query not found") ;
			}
		} else {
			return array("status"=>false,"msg"=>"One of the argument is empty") ;
		}					
	}
 
	
	/* Flights Methods */
	function getFlights ($destnation) {
		if ($destnation != "" || !empty ($destnation) ) {
			$date = date("d/m/Y");
			$tommowor = date('d/m/Y', strtotime(' + 1 days'));
			$call = $this->curlRequest("https://api.skypicker.com/flights?fly_from=KW&limit=5&fly_to=".strtoupper($destnation)."&date_from=".$date."&returnFrom=".$tommowor."&sort=price&asc=1&partner=picky&curr=KWD");
			$jsonIt = json_decode($call,true);
			return $jsonIt;
		}
	}	
	
	function getFlightsList ($destnation,$in,$out) {
		if ($destnation != "" || !empty ($destnation) ) {
			$in = date("d/m/Y", strtotime($in));
			$out = date("d/m/Y", strtotime($out));
			$call = $this->curlRequest("https://api.skypicker.com/flights?fly_from=KW&limit=15&fly_to=".strtoupper($destnation)."&returnFrom=".$in."&returnTo=".$out."&sort=price&asc=1&partner=picky&curr=KWD");
			$jsonIt = json_decode($call,true);
			return $jsonIt;
		}
	}
	
	
	
	/* Currnecy Changer */

	function getCurrnecy ($amount = 1,$eq) {
		if ($eq != "" || !empty ($eq) ) {
			$call = $this->curlRequest("https://free.currconv.com/api/v7/convert?q=".$eq."&compact=ultra&apiKey=6ac21a9281448f1188ae");
			$jsonIt = json_decode($call,true);
			if ($jsonIt[$eq]) {
				$newAmount = $amount * $jsonIt[$eq] ;
				$currName = explode("_", $eq);
				return array("status"=>true,"msg"=>"$newAmount ". $currName[1]) ;
			} else {
				return array("status"=>false,"msg"=>"Query not found") ;
			}
		}
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>