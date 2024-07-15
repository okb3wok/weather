<?php

include "openweather.php";
include 'open_weather_api.php';

$weather = new OpenWeather();

if($_GET["q"]){

	header('Content-Type: application/json');
	print_r( $weather->getweatherJson($_GET["q"], $API_SECRET_KEY));

}else{
	$TEMPLATE_PATH = __DIR__.'/templates/main.html';
	$template = file_get_contents($TEMPLATE_PATH);
	$template = preg_replace('{{{title}}}', 'Погода', $template);
	echo $template;
}

