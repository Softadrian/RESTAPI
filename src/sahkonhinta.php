<?php

$apiUrl = "https://api.spot-hinta.fi/Today";

$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

if (!$data) {
    die("Ei saatu dataa.");
}

echo "<h2>Pörssisähkön tuntihinnat tänään</h2>";
echo "<p>Tässä näkyvät tämän päivän sähkön hinnat tunneittain.</p>";

echo "<table border='1' cellpadding='5' cellspacing='0'>";

echo "<tr>";
echo "<th>Aloitusaika</th>";
echo "<th>Hinta (c/kWh, ALV sis.)</th>";
echo "</tr>";

foreach ($data as $row) {

    echo "<tr>";

    echo "<td>";
    echo $row['DateTime'];
    echo "</td>";

    echo "<td>";
    echo number_format($row['PriceWithTax'], 2);
    echo "</td>";

    echo "</tr>";
}

echo "</table>";

?>
