<?php

// Tietokantayhteys
$host = "db";
$user = "root";
$password = "root";
$database = "autot";

$conn = new mysqli($host, $user, $password, $database);

// Tarkistetaan yhteys
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Tietokantayhteys epäonnistui"]);
    exit;
}

// JSON-vastaus
header("Content-Type: application/json; charset=UTF-8");

// Haetaan kaikki tuotteet
$sql = "SELECT id, name, price, description, category FROM products";
$result = $conn->query($sql);

$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Tulostetaan JSON
echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$conn->close();
?>
