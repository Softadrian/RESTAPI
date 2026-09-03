
<?php
// Määritellään API:n URL-osoite
$apiUrl = "http://localhost/autot_api/autot_api.php";

// Alustetaan muuttuja, johon tallennetaan haettu auton data
$auto = null;

// Jos GET-parametrina on annettu 'id', haetaan kyseisen auton tiedot
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Muutetaan id kokonaisluvuksi
    $response = @file_get_contents("$apiUrl?id=$id"); // Haetaan tiedot API:sta
    $auto = json_decode($response, true); // Muutetaan JSON PHP-taulukoksi
}

// Jos lomakkeella on lähetetty päivitys
if (isset($_POST['update'])) {
    // Kerätään lomakkeen tiedot taulukkoon
    $data = [
        "ID" => intval($_POST['id']),
        "merkki" => $_POST['merkki'],
        "tyyppi" => $_POST['tyyppi'],
        "vuosimalli" => intval($_POST['vuosimalli'])
    ];

    // Alustetaan cURL-pyyntö API:lle
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Käytetään PUT-metodia
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Lähetetään data JSON-muodossa
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']); // Määritellään sisällön tyyppi

    // Suoritetaan pyyntö
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Haetaan HTTP-koodi
    curl_close($ch);

    // Tarkistetaan, onnistuiko päivitys
    if ($httpCode === 200 || $httpCode === 204) {
        header("Location: index.php?status=updated"); // Ohjataan onnistumisviestillä
    } else {
        header("Location: index.php?status=update_error"); // Ohjataan virheviestillä
    }
    exit;
}
?>




<h2>Muokkaa autoa</h2>
<form method="post">
    <input type="hidden" name="id" value="<?= htmlspecialchars($auto['ID']) ?>">
    <input type="text" name="merkki" value="<?= htmlspecialchars($auto['merkki']) ?>" placeholder="Merkki">
    <input type="text" name="tyyppi" value="<?= htmlspecialchars($auto['tyyppi']) ?>" placeholder="Tyyppi">
    <input type="number" name="vuosimalli" value="<?= htmlspecialchars($auto['vuosimalli']) ?>" placeholder="Vuosimalli">
    <button type="submit" name="update">Tallenna muutokset</button>
</form>
