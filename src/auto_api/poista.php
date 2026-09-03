<?php
// Määritellään API:n URL-osoite
$apiUrl = "http://localhost/autot_api/autot_api.php";

// Tarkistetaan, onko GET-parametrina annettu 'id'
if (isset($_GET['id'])) {
    // Muutetaan 'id' kokonaisluvuksi turvallisuuden vuoksi
    $id = intval($_GET['id']);

    // Alustetaan cURL-pyyntö API:lle, jossa poistetaan annettu ID
    $ch = curl_init("$apiUrl?id=$id");

    // Määritellään, että vastaus palautetaan eikä tulosteta suoraan
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Määritellään HTTP-metodiksi DELETE
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

    // Suoritetaan pyyntö ja tallennetaan vastaus
    $response = curl_exec($ch);

    // Haetaan HTTP-vastauskoodi
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Suljetaan cURL-istunto
    curl_close($ch);

    // Tarkistetaan, onnistuiko poisto (HTTP 200 OK tai 204 No Content)
    if ($httpCode === 200 || $httpCode === 204) {
        // Ohjataan käyttäjä takaisin etusivulle onnistumisviestin kanssa
        header("Location: index.php?status=deleted");
    } else {
        // Ohjataan käyttäjä takaisin etusivulle virheviestin kanssa
        header("Location: index.php?status=error");
    }

    // Lopetetaan skriptin suoritus
    exit;
}
?>
