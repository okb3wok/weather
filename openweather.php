<?php


class OpenWeather extends DateTime
{

	public function getweatherJson($city, $api_key) {


		$url = 'https://api.openweathermap.org/data/2.5/weather';


		$query = array(
			'q'=> $city,
			'units' => 'metric',
			'lang'=>'ru',
			'appid' => $api_key
		);


		$header = array(
			'Content-Type: application/json'
		);

		$options = array(
			CURLOPT_HTTPHEADER => $header,
			CURLOPT_HEADER => false,
			CURLOPT_URL => $url . '?' . http_build_query($query),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false
		);

		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$response = curl_exec($ch);
		curl_close($ch);

		$array = json_decode($response,true);

		if ($array['cod'] == 200){

			$dt = $this -> createFromFormat('U',$array['dt']);
			$dt -> setTimezone(new DateTimeZone('Europe/Moscow'));
			$array['day'] = $dt->format("Y.m.d");
			$array['time'] = $dt->format("H:i:s");
			unset( $array['weather'][0]['id'],
				$array['base'],
				$array['main']['sea_level'],
				$array['main']['grnd_level'],
				$array['main']['temp_min'],
				$array['visibility'],
				$array['clouds'],
				$array['dt'],
				$array['coord'],
				$array['sys']['id'],
				$array['sys']['type'],
			);
		}

		return json_encode($array, JSON_UNESCAPED_UNICODE);
	}
}

