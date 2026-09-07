<?php

// Kuopion koordinaatit
$lat = 62.8924;
$lon = 27.6770;

// MET.no API
$url = "https://api.met.no/weatherapi/locationforecast/2.0/compact?lat=$lat&lon=$lon";

// User-Agent
$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: KuopionSaa/1.0 adrian.idahosa@edu.sakky.fi\r\n"
    ]
];

$context = stream_context_create($options);

// Haetaan JSON-data API:sta
$json = file_get_contents($url, false, $context);

// Tarkistetaan onnistuiko haku
if ($json === false) {
    die("Säätietojen hakeminen epäonnistui.");
}

// Muutetaan JSON PHP-taulukoksi
$data = json_decode($json, true);

// Tarkistetaan JSON
if ($data === null) {
    die("JSON-datan käsittely epäonnistui.");
}


// Suomenkielinen sääteksti
function saaSuomeksi($symboli)
{
    $kaannokset = [

        "clearsky_day" => "Selkeää",
        "clearsky_night" => "Selkeää",

        "fair_day" => "Poutaa",
        "fair_night" => "Poutaa",

        "partlycloudy_day" => "Puolipilvistä",
        "partlycloudy_night" => "Puolipilvistä",

        "cloudy" => "Pilvistä",

        "rain" => "Sadetta",
        "lightrain" => "Heikkoa sadetta",
        "heavyrain" => "Voimakasta sadetta",

        "rainshowers_day" => "Sadekuuroja",
        "rainshowers_night" => "Sadekuuroja",

        "snow" => "Lunta",
        "lightsnow" => "Heikkoa lumisadetta",
        "heavysnow" => "Voimakasta lumisadetta",

        "snowshowers_day" => "Lumikuuroja",
        "snowshowers_night" => "Lumikuuroja",

        "sleet" => "Räntää",

        "fog" => "Sumua",

        "rainandthunder" => "Ukkossadetta",
        "snowandthunder" => "Ukkoslumisadetta",
        "sleetandthunder" => "Ukkosräntää"
    ];

    return $kaannokset[$symboli] ?? "Säätilanne";
}


// Haetaan tuntiennuste
$tunnit = $data["properties"]["timeseries"];

?>

<!DOCTYPE html>
<html lang="fi">

<head>

    <meta charset="UTF-8">

    <title>Kuopion sääennuste</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #eaf3f8;
            margin: 40px;
        }

        h1 {
            color: #174a7e;
        }

        p {
            font-size: 18px;
        }

        table {
            border-collapse: collapse;
            width: 700px;
            max-width: 100%;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        th {
            background-color: #174a7e;
            color: white;
            padding: 12px;
        }

        td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        .saa {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .kuvake {
            font-size: 30px;
        }

        .kylma {
            color: blue;
            font-weight: bold;
        }

        .lampoisa {
            color: red;
            font-weight: bold;
        }

    </style>

</head>

<body>

<h1>Kuopion sääennuste</h1>

<p>
    Seuraavien kuuden tunnin sääennuste Kuopioon.
</p>

<table>

    <tr>
        <th>Aika</th>
        <th>Lämpötila</th>
        <th>Sää</th>
    </tr>


<?php

// Näytetään seuraavat 6 tuntia
for ($i = 0; $i < 6; $i++) {

    $tunti = $tunnit[$i];

    // Aika
    $aika = new DateTime($tunti["time"]);

    // Lämpötila
    $lampotila =
        $tunti["data"]["instant"]["details"]["air_temperature"];

    // Sääsymboli
    $symboli =
        $tunti["data"]["next_1_hours"]["summary"]["symbol_code"];

    // Suomenkielinen sää
    $saa = saaSuomeksi($symboli);


    // Valitaan sääkuvake
    if (strpos($symboli, "clearsky") !== false) {

        $kuvake = "☀️";

    } elseif (strpos($symboli, "fair") !== false) {

        $kuvake = "🌤️";

    } elseif (strpos($symboli, "partlycloudy") !== false) {

        $kuvake = "⛅";

    } elseif (strpos($symboli, "cloudy") !== false) {

        $kuvake = "☁️";

    } elseif (strpos($symboli, "rain") !== false) {

        $kuvake = "🌧️";

    } elseif (strpos($symboli, "snow") !== false) {

        $kuvake = "❄️";

    } elseif (strpos($symboli, "sleet") !== false) {

        $kuvake = "🌨️";

    } elseif (strpos($symboli, "fog") !== false) {

        $kuvake = "🌫️";

    } else {

        $kuvake = "🌤️";

    }


    // Lämpötilan väri
    if ($lampotila <= 0) {

        $vari = "kylma";

    } else {

        $vari = "lampoisa";

    }

?>

    <tr>

        <td>
            <?php echo $aika->format("H:i"); ?>
        </td>

        <td class="<?php echo $vari; ?>">
            <?php echo number_format($lampotila, 1, ",", ""); ?> °C
        </td>

        <td>

            <div class="saa">

                <span class="kuvake">
                    <?php echo $kuvake; ?>
                </span>

                <span>
                    <?php echo $saa; ?>
                </span>

            </div>

        </td>

    </tr>

<?php

}

?>

</table>

</body>

</html>
