<?PHP
/* 
    App : TRHAL (APi)
    Description : Translate Query to photo from google place api
*/
if (isset($_GET["q"])) {
	require('config.php');   
	require('trhalClass.php');
	$TRHAL = new Trhal ($TRHAL_SETTINGS["GOOGLE_MAPS_API"],$TRHAL_SETTINGS["RAPID_API_KEY"]);
	$countryName = $_GET["q"];
	$width = $_GET["w"] ;
	$height = $_GET["h"];
	$Img = $TRHAL->getCountryBanner ($countryName,$width,$height); 
} else {
	$Img = "assets/notfound.png";
}


$content = file_get_contents($Img);
header('Content-Type: image/gif');
echo $content;

?>