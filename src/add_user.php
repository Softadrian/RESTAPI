<?php

header('Content-Type: application/json; charset=UTF-8');

// Tietokantayhteys
$servername = "db";
$username = "root";
$password = "root";
$dbname = "autot";

$conn = new mysqli($servername, $username, $password, $dbname);

// Tarkistetaan tietokantayhteys
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "virhe" => "Tietokantayhteys epäonnistui"
    ]);
    exit;
}

// Tarkistetaan HTTP-metodi
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "virhe" => "Väärä pyyntö. Käytä POST-pyyntöä."
    ]);
    exit;
}

// Luetaan POST-tiedot
$nimi = $_POST['nimi'] ?? '';
$sahkoposti = $_POST['sahkoposti'] ?? '';

// Tarkistetaan, että tiedot annettiin
if (empty($nimi) || empty($sahkoposti)) {
    http_response_code(400);
    echo json_encode([
        "virhe" => "Nimi ja sähköposti ovat pakollisia."
    ]);
    exit;
}

// Käytetään prepared statementia
$stmt = $conn->prepare(
    "INSERT INTO users (nimi, sahkoposti) VALUES (?, ?)"
);

$stmt->bind_param("ss", $nimi, $sahkoposti);

// Lisätään käyttäjä
if ($stmt->execute()) {

    http_response_code(201);

    echo json_encode([
        "viesti" => "Uusi käyttäjä lisätty onnistuneesti",
        "id" => $stmt->insert_id,
        "nimi" => $nimi,
        "sahkoposti" => $sahkoposti
    ], JSON_UNESCAPED_UNICODE);

} else {

    http_response_code(500);

    echo json_encode([
        "virhe" => "Käyttäjän lisääminen epäonnistui"
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();

?>
