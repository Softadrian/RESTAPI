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

// Haetaan käyttäjät
$sql = "SELECT id, nimi, sahkoposti FROM users";
$result = $conn->query($sql);

$users = [];

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} else {

    echo json_encode([
        "viesti" => "Käyttäjiä ei löytynyt"
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();

?>
