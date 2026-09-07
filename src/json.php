<?php

$henkilo = '{
  "nimi": "Matti Meikäläinen",
  "ikä": 30,
  "osoite": "Esimerkkikatu 1, 00100 Helsinki"
}';

$kirjat = '[
  {
    "nimi": "Tämä on kirja 1",
    "kirjailija": "Kirjailija A",
    "julkaisuvuosi": 2020
  },
  {
    "nimi": "Tämä on kirja 2",
    "kirjailija": "Kirjailija B",
    "julkaisuvuosi": 2021
  },
  {
    "nimi": "Tämä on kirja 3",
    "kirjailija": "Kirjailija C",
    "julkaisuvuosi": 2022
  }
]';

$henkiloArray = json_decode($henkilo, true);
$kirjatArray = json_decode($kirjat, true);

echo "<h2>Henkilön tiedot:</h2>";
echo "Nimi: " . $henkiloArray['nimi'] . "<br>";
echo "Ikä: " . $henkiloArray['ikä'] . "<br>";
echo "Osoite: " . $henkiloArray['osoite'] . "<br><br>";

echo "<h2>Kirjalista:</h2>";

foreach ($kirjatArray as $kirja) {
    echo "Nimi: " . $kirja['nimi'] . "<br>";
    echo "Kirjailija: " . $kirja['kirjailija'] . "<br>";
    echo "Julkaisuvuosi: " . $kirja['julkaisuvuosi'] . "<br><br>";
}

?>