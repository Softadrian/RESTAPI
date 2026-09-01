<?php
$servername = "db";
$username = "root"; // Vaihda omaan käyttäjänimeesi
$password = "root"; // Vaihda omaan salasanaasi
$dbname = "esimerkkitietokanta";

// Luo yhteys
$conn = new mysqli($servername, $username, $password, $dbname);

// Tarkista yhteys
if ($conn->connect_error) {
    die("Yhteys epäonnistui: " . $conn->connect_error);
}


header("Content-Type: application/json");

$sql = "SELECT * FROM tuotteet";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Luo taulukko tuloksista
    $tuotteet = array();
    while($row = $result->fetch_assoc()) {
        $tuotteet[] = $row;
    }
    // Tulosta JSON-muodossa
    echo json_encode($tuotteet);
} else {
    echo json_encode([]);
}

$conn->close();
?>