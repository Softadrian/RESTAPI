<?php

$apiKey = "14dc1b1dcf57f3bf81b8993bc1197bc2";
$city = "Helsinki";

$url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric&lang=fi";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if ($response === false) {
    echo "Virhe: " . curl_error($ch);
} else {

    $data = json_decode($response, true);
	
    if (isset($data['main'])) {

        $temperature = $data['main']['temp'];
        $weatherDescription = $data['weather'][0]['description'];
        $cityName = $data['name'];

        echo "Kaupunki: " . $cityName . "<br>";
        echo "Lämpötila: " . $temperature . " °C<br>";
        echo "Sää: " . ucfirst($weatherDescription) . "<br>";

    } else {
        echo "Säätietoja ei löytynyt.";
    }
}

curl_close($ch);

?>