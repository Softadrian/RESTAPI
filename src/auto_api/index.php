<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Autotietokanta</title>
       <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { margin-top: 30px; }
        form { margin-bottom: 20px; padding: 10px; border: 1px solid #ccc; }
        input, button { margin: 5px; padding: 5px; }
        pre { background: #f4f4f4; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Autotietokanta</h1>

<?php
// API:n osoite, josta autot haetaan
$apiUrl = "http://localhost/autot_api/autot_api.php";

// Taulukko, johon kerätään kaikki haetut autot (kaikki + yksittäinen)
$autot = [];




if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'updated':
            echo "<div style='color: green;'>Auton tiedot päivitetty onnistuneesti.</div>";
            break;
        case 'update_error':
            echo "<div style='color: red;'>Tietojen päivitys epäonnistui.</div>";
            break;
        case 'deleted':
            echo "<div style='color: green;'>Auto poistettu onnistuneesti.</div>";
            break;
        case 'error':
            echo "<div style='color: red;'>Auton poisto epäonnistui.</div>";
            break;
        case 'added':
            echo "<div style='color: green;'>Auton lisäys onnistus.</div>";
            break;
        case 'add_error':
            echo "<div style='color: red;'>Auton lisäys epäonnistui.</div>";
            break;
    }
}


/*
// Hae kaikki autot, jos getKaikki-parametri on asetettu
if (isset($_GET['getKaikki'])) {
    $response = @file_get_contents($apiUrl); // Tee HTTP-pyyntö API:lle
    if ($response) {
        $kaikki = json_decode($response, true); // Muunna JSON PHP-taulukoksi
        if (is_array($kaikki)) {
            // Lisää kaikki autot yhteiseen taulukkoon
            $autot = array_merge($autot, $kaikki);
        }
    }
}
*/
if (isset($_GET['getKaikki'])) {
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if ($response !== false) {
        $kaikki = json_decode($response, true);
        if (is_array($kaikki)) {
            $autot = array_merge($autot, $kaikki);
        }
    } else {
        // Virheenkäsittely
        $error = curl_error($ch);
        echo "Virhe API-kutsussa: " . htmlspecialchars($error);
    }

    curl_close($ch);
}


// 🔹 Hae yksittäinen auto ID:llä, jos getID-parametri on asetettu
if (isset($_GET['getID'])) {
    $id = intval($_GET['getId']); // Muunna ID kokonaisluvuksi
    $response = @file_get_contents("$apiUrl?id=$id"); // Tee pyyntö yksittäiselle autolle

    if ($response) {
        $yksi = json_decode($response, true); // Muunna JSON PHP-taulukoksi
        // Tarkista, että vastaus sisältää auton tiedot
        if (is_array($yksi) && isset($yksi['ID'])) {
            // Lisää yksittäinen auto yhteiseen taulukkoon
            $autot[] = $yksi;
        }
    }
}

// 🔹 Näytä taulukko, jos autoja löytyi
if (!empty($autot)) {
    echo "<table border='1'>";
   
     echo "<tr><th>ID</th><th>Merkki</th><th>Tyyppi</th><th>Vuosimalli</th><th>Toiminnot</th></tr>";

      

    // Käy läpi kaikki haetut autot ja tulosta rivit
    foreach ($autot as $auto) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($auto['ID']) . "</td>";
        echo "<td>" . htmlspecialchars($auto['merkki']) . "</td>";
        echo "<td>" . htmlspecialchars($auto['tyyppi']) . "</td>";
        echo "<td>" . htmlspecialchars($auto['vuosimalli']) . "</td>";
        echo "<td>";
        echo '<a href="muokkaa.php?id=' . urlencode($auto['ID']) . '">Muokkaa</a> | ';
        echo '<a href="poista.php?id=' . urlencode($auto['ID']) . '">Poista</a>';
        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    // Jos autoja ei löytynyt, näytä viesti
    echo "Autoja ei löytynyt.";
}
?>
<!-- 🔹 Lomake: Hae kaikki autot -->
<h2>Hae Kaikki autot</h2>
<form method="get">
    <button type="submit" name="getKaikki">Hae</button>
</form>
<h2>Lisää uusi auto</h2>
<form method="post" action="uusiauto.php">
    <button type="submit">Lisää uusi auto</button>
</form>
<!-- 🔹 Lomake: Hae auto ID:llä -->
<h2>Hae auto ID:llä</h2>
<form method="get">
    <input type="number" name="getId" placeholder="Syötä ID">
    <button type="submit" name="getID">Hae</button>
</form>



</body>
</html>
