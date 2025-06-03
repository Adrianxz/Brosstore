<?php
static public function dlocal($url, $method, $fields){

		$endpoint = "https://api-sbx.dlocalgo.com/"; //TEST
		$apiKey = "DSJzofSacFLsMpEYWtyMPuiupiKOaqcE"; //TEST
		$secretKey = "kDAdRQw4YdqLObFWi032pGW1D7xmNpuccLENsoeK"; //TEST


		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $endpoint.$url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => $method,
		  CURLOPT_POSTFIELDS => $fields,
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json',
		    'Authorization: Bearer '.$apiKey.':'.$secretKey
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		
		$response = json_decode($response);
		return $response;

	}
	?>