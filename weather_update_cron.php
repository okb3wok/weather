<?php
include "openweather.php";
include 'open_weather_api.php';

$weather = new OpenWeather();

$datetime = new DateTime();
$datetime->setTimezone(new DateTimeZone('Europe/Moscow'));

$SETTINGS_PATH = __DIR__.'/settings.json';

$settings_str = file_get_contents($SETTINGS_PATH);
$settings = json_decode($settings_str,true);


$now_time = $datetime->format('Y-m-d H:i:s');

$REGEX_5MIN = '/([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9][05]):[0-9]{2}/';
$REGEX_DAY = '/([0-9]{4}-[0-9]{2}-[0-9]{2}) 23:55:[0-9]{2}/';

preg_match($REGEX_5MIN, $now_time,$matches_5min);
preg_match($REGEX_DAY, $now_time,$matches_day);



if($matches_5min){

	$cities = $settings['places'];

	for($i=0; $i<count($cities); $i++)
	{

		if (file_exists(__DIR__.'/place_' . $cities[$i] . '.json')) {
			$place_weather = file_get_contents(__DIR__.'/place_' . $cities[$i] . '.json');
            $place_weather = json_decode($place_weather,true);
		}else{
			$place_weather = [];
			$place_weather['hourly'] = [];
			$place_weather['daily'] = [];
			$handle = fopen (__DIR__.'/place_' . $cities[$i] . '.json', 'w+');
			fwrite($handle, json_encode($place_weather));
			fclose($handle);
		}

        $is_current_temp_written = 0;
        for($j=0; $j<count($place_weather['hourly']); $j++)
        {
            if($place_weather['hourly'][$j]['dt']==$matches_5min[1]){
                $is_current_temp_written = 1;
            }

        }

        if(!$is_current_temp_written){
            $current = json_decode($weather->getweatherJson($cities[$i], $API_SECRET_KEY),true);
            //print_r($place_weather);
            $place_weather['hourly'][] = array( "dt" =>$matches_5min[1], "t" => $current['main']['temp_max']);
            $place_weather['current'] = $current;

			if(count($place_weather['hourly']) > 288){
				$place_weather['hourly'] = array_slice($place_weather['hourly'], -288, 288);
			}


	        if($matches_day){
		        $t_min = 100;
		        $t_max = -100;

		        for($k=0; $k<count($place_weather['hourly']); $k++)
		        {
					if($place_weather['hourly'][$k]['t']>$t_max){
						$t_max = $place_weather['hourly'][$k]['t'];
					}
					if($place_weather['hourly'][$k]['t']<$t_min){
						$t_min = $place_weather['hourly'][$k]['t'];
					}
		        }

		        $place_weather['daily'][] = array("dt" => $matches_day[1], "t_max" => $t_max, "t_min" => $t_min);
	        }


	        $handle = fopen (__DIR__.'/place_' . $cities[$i] . '.json', 'w+');
            fwrite($handle, json_encode($place_weather, JSON_UNESCAPED_UNICODE));
            fclose($handle);
        }
	}
}

$settings['lastTimeStop'] = $datetime->format('Y-m-d H:i:s');

$handle = fopen ($SETTINGS_PATH, "w+");
fwrite($handle, json_encode($settings,JSON_UNESCAPED_UNICODE));
fclose($handle);

