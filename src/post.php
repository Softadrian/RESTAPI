<?php

$url = 'https://jsonplaceholder.typicode.com/posts';

$data = array(
    'title' => 'Uusi Postaus',
    'body' => 'Tämä on minun ensimmäinen POST-pyyntöni!',
    'userId' => 1
);

$jsonData = json_encode($data);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Virhe: ' . curl_error($ch);
} else {
    $responseData = json_decode($response, true);

    echo '<h2>Pyyntö onnistui!</h2>';
    echo '<pre>';
    print_r($responseData);
    echo '</pre>';
}

curl_close($ch);
?>