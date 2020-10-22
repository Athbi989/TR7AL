<?PHP
/* 
    App : TRHAL (APi)
    Description : Simple requests page handler 
*/
require('trhalClass.php');   
$TRHAL = new Trhal ("AIzaSyB73ruVbZGoirOrvFzCec4T92XZWxFdcic","1qsad");
   
if (isset($_GET["action"])) {
    $action = $_GET["action"];
} else {
	die("Empty Request !");
}

function endRequest ($respone)  {
	die(json_encode($respone)) ;
	exit ;
}

/* -------- Init Overview --------*/

if ($action == "overview") {
	if (isset($_GET["code"])) {
	$countryCode = $_GET["code"];
	$basicCountryInfo = $TRHAL->getCountry($countryCode) ;
	$covid19 = $TRHAL->getCovid19 ($countryCode) ;
	if ($basicCountryInfo["status"] == true ) {
	$respone = array (
		"query" => $basicCountryInfo["result"]["name"],
		"label" => $basicCountryInfo["result"]["label"],
		"population" => number_format($basicCountryInfo["result"]["population"]) ,
		"capitalCity" => $basicCountryInfo["result"]["capitalCity"],
		"region" => $basicCountryInfo["result"]["region"],
		"lat" => $basicCountryInfo["result"]["longitude"],
		"lng" => $basicCountryInfo["result"]["latitude"],
		"corona" => array (
			"confirmed" => $covid19["result"]["confirmed"],
			"recovered" => $covid19["result"]["recovered"],
			"critical" => $covid19["result"]["critical"],
			"deaths" => $covid19["result"]["deaths"]
		)
	);
	} else {
	    die("Failed Grabbing info");
	}
	endRequest ($respone) ;
	} else {
		die("Arg is missing !");
	}
}



/* -------- Init Hotels --------*/

if ($action == "hotels") {
	if (isset($_GET["query"])) {
	$countryName = $_GET["query"];
	$limt = $_GET["limt"];
	$hotels_array = $TRHAL->getHotels_TripAdvisor($countryName,$limt);
	/* Uncomment this line if you want it api respone (json)
	endRequest ($hotels_array) ;
	*/
	$HTMLRespone = "";
		foreach ($hotels_array["data"] as $hotel) {
		$link = "#";
		if (count($hotel["hac_offers"]["offers"]) > 0) {
			//There's offer link
			$link = $hotel["hac_offers"]["offers"][0]["link"] ;
		} else if ((count($hotel["special_offers"]["desktop"]) > 0)) {
			$link = $hotel["special_offers"]["desktop"][0]["url"] ;
		} else if ((count($hotel["business_listings"]["desktop_contacts"]) > 0)){
			$link = $hotel["business_listings"]["desktop_contacts"][0]["value"] ;
		}
		$HTMLRespone .= '
		<div class="card__ty2">
			<div class="info">
				<div class="sub1">'.$hotel["name"].'</div>
				<div class="price">'.$hotel["price"].'</div>
				<div class="sub2">'.$hotel["location_string"].' </div>
				<div class="sub3">'.$hotel["ranking"].'</div>
				<div class="sub2">Rating : <span class="rating_label">'.$hotel["rating"].'/5</span> </div>
				<a href="'.$link.'" style="height: auto;width: 70px;" target="_blank" class="button-regular">اوفر العروض</a>
				</div>
				<div class="img">
				<img class="img-country" src="'.$hotel["photo"]["images"]["large"]["url"].'" alt="" srcset="">
			</div>
		</div>
		';
	}
	die($HTMLRespone);
	} else {
		die("Arg is missing !");
	}
}

if ($action == "getHotelList") {
	if (isset($_GET["query"])) {
		$query = $_GET["query"];
		$page = $_GET["page"];
		$nights = $_GET["nights"];
		$checkIn = $_GET["checkIn"];
		if ($page == "none" ) {
			$page = 1 ;
		}
		$job_List = $TRHAL->getHotelsList_TripAdvisor ($query,$checkIn,$nights ,$page);
		endRequest ($job_List) ;
		$respone = array ( 
			"nextToken" => $job_List["next_page_token"] ,
			"data" => $Places_Array
		);
	} else {
		die("Arg is missing !");
	}
}



/* -------- Init Places --------*/


if ($action == "PointsOfInterst") {
	if (isset($_GET["query"])) {
	$countryName = $_GET["query"];
	$points_array = $TRHAL->getPointsOfInterst($countryName);
	/* Uncomment this line if you want it api respone (json)
	endRequest ($points_array) ;
	*/
	$HTMLRespone = "";
	foreach (array_slice($points_array["results"], 0, 10) as $place) {
	if ($place["photos"][0]["photo_reference"] != "") {
		$img = $TRHAL->getGooglePlacePhoto($place["photos"][0]["photo_reference"]);
	} else {
		$img =  "http://103.105.50.163/TRHALCLASSES/assets/notfound.png";
	}
	$HTMLRespone .= '
	<div class="card__ty1">
		<div class="img">
			<img class="img-country" src="'.$img.'" alt="" srcset="">
			</div>
			<div class="info">
			<div class="sub1">'.$place["name"].'</div>
			<div class="sub2">'.$place["formatted_address"].'</div>
		</div>
	</div>
	';
	}
	die($HTMLRespone);
	} else {
		die("Arg is missing !");
	}
}


if ($action == "getPlace") {
	if (isset($_GET["query"])) {
	$countryName = $_GET["query"];
	$type = $_GET["type"];
	$place_array = $TRHAL->getPlacesByType ($countryName,$type);
	/*Uncomment this line if you want it api respone (json)
	endRequest ($place_array) ;
	*/	
	$HTMLRespone = "";
	foreach (array_slice($place_array["results"], 0, 10) as $place) {
	$url = $TRHAL->getGooglePlaceURL($place["reference"]);
	if ($place["photos"][0]["photo_reference"] != "") {
		$img = $TRHAL->getGooglePlacePhoto($place["photos"][0]["photo_reference"]);
	} else {
		$img =  "http://103.105.50.163/TRHALCLASSES/assets/notfound.png";
	}
	$HTMLRespone .= '
		<div class="card__ty2">
			<div class="info">
			<div class="sub1">'.$place["name"].'</div>
			<div class="price">'.$place["formatted_address"].'
			</div>
			<div class="sub2">Rating : <span class="rating_label">'.$place["rating"].'/5</span> </div>
			<a href="'.$url.'"  target="_blank"  class="clickable_icon">
			<span>موقع المكان</span>
			<i class="fas fa-map-marker-alt clx"></i></a>
			</div>
			<div class="img">
			<img class="img-country"
			src="'.$img.'"
			alt=""
			srcset="">
			</div>
		</div>
	';
	}
	die($HTMLRespone);
	} else {
		die("Arg is missing !");
	}
}



if ($action == "getPlaceList") {
	if (isset($_GET["query"])) {
		$query = $_GET["query"];
		$type = $_GET["type"];
		$page = $_GET["page"];
		if ($page == "none" ) {
			$page = null ;
		}
		$job_List = $TRHAL->getListPlaces ($query,$type,$page);
		$Places_Array = array () ;
		foreach ($job_List["results"] as $place) {
			$img = $TRHAL->getGooglePlacePhoto($place["photos"][0]["photo_reference"]);
			$url = $TRHAL->getGooglePlaceURL($place["reference"]);
			array_push($Places_Array,array(
				"name" => $place["name"],
				"formatted_address" => $place["formatted_address"],
				"icon" =>$place["icon"],
				"img" =>$img,
				"rating" =>$place["rating"],
				"url" =>$url
			)) ;
		}
		$respone = array ( 
			"nextToken" => $job_List["next_page_token"] ,
			"data" => $Places_Array
		);
		endRequest ($respone) ;
	} else {
		die("Arg is missing !");
	}
}


/* -------- Init Flights  --------*/

if ($action == "flights") {
	if (isset($_GET["dis"])) {
	$countryCode = $_GET["dis"];
	$flights_array = $TRHAL->getFlights($countryCode);
	/* Uncomment this line if you want it api respone (json)
	endRequest ($flights_array) ;
	*/
	$HTMLRespone = "";
	foreach (array_slice($flights_array["data"], 0, 10) as $flight) {
		$HTMLRespone .= '
		<div class="flight_single">
			<div class="part_right">
				<div class="i_con" style="align-items: flex-start;">
				<span class="i0">'.$flight["conversion"]["KWD"].' KWD</span>
				<a href="'.$flight["deep_link"].'" target="_blank" class="bt">اوفر العروض</a>
				</div>
				</div>
				<div class="part_left">
				<div class="i_con">
				<span class="i1">From '.$flight["cityFrom"].' to '.$flight["cityTo"].'</span>
				<span class="i2">'.$flight["fly_duration"].' ('.$flight["distance"].' Km)</span>
				</div>
				<div class="img-icon">
				<img src="./assets/images/plane.svg" alt="" srcset="">
				</div>
			</div>
		</div>
		';
		}
		die($HTMLRespone);
	} else {
		die("Arg is missing !");
	}
}

if ($action == "flightsList") {
	if (isset($_GET["dis"])) {
	$countryCode = $_GET["dis"];
	$checkIn = $_GET["checkIn"];
	$checkOut = $_GET["checkOut"];
	$flights_array = $TRHAL->getFlightsList ($countryCode,$checkIn,$checkOut) ;
	/* Uncomment this line if you want it api respone (json)
	endRequest ($flights_array) ;
	*/
	$HTMLRespone = "";
	foreach ($flights_array["data"] as $flight) {
		$HTMLRespone .= '
		<div class="flight_single">
			<div class="part_right">
				<div class="i_con" style="align-items: flex-start;">
				<span class="i0">'.$flight["conversion"]["KWD"].' KWD</span>
				<a href="'.$flight["deep_link"].'" target="_blank" class="bt">اوفر العروض</a>
				</div>
				</div>
				<div class="part_left">
				<div class="i_con">
				<span class="i1">From '.$flight["cityFrom"].' to '.$flight["cityTo"].'</span>
				<span class="i2">'.$flight["fly_duration"].' ('.$flight["distance"].' Km)</span>
				</div>
				<div class="img-icon">
				<img src="./assets/images/plane.svg" alt="" srcset="">
				</div>
			</div>
		</div>
		';
		}
		die($HTMLRespone);
	} else {
		die("Arg is missing !");
	}
}


/* -------- Init Currncies  --------*/

if ($action == "currconv") {
	if (isset($_GET["eq"])) {
		$eq = $_GET["eq"] ;
		$amount = $_GET["amount"] ;
		$job = $TRHAL->getCurrnecy ($amount,$eq) ;
		endRequest($job) ;
	}
}
?>



































