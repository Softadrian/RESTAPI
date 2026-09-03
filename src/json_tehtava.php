<?php

// Haetaan Helsingin koordinaatit Open-Meteosta
$url = "https://api.open-meteo.com/v1/forecast?latitude=60.1699&longitude=24.9384&current=temperature_2m,relative_humidity_2m,apparent_temperature,wind_speed_10m";

// Haetaan JSON-data
$response = file_get_contents($url);

// Tarkistetaan, onnistuiko haku
if ($response === false) {
    die("JSON-datan hakeminen epäonnistui.");
}

// Muutetaan JSON PHP-taulukoksi
$data = json_decode($response, true);

// Tarkistetaan, onnistuiko JSON-jäsennys
if ($data === null) {
    die("JSON-datan jäsentäminen epäonnistui.");
}

// Tulostetaan oleelliset tiedot
echo "<h1>Helsingin sää</h1>";

if (isset($data['current'])) {

    echo "Lämpötila: " . $data['current']['temperature_2m'] . " °C<br>";
    echo "Tuntuu kuin: " . $data['current']['apparent_temperature'] . " °C<br>";
    echo "Kosteus: " . $data['current']['relative_humidity_2m'] . " %<br>";
    echo "Tuulen nopeus: " . $data['current']['wind_speed_10m'] . " km/h<br>";

} else {
    echo "Säätietoja ei löytynyt.";
}

?>
