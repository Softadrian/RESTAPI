<?php

header('Content-Type: application/json; charset=UTF-8');

$servername = "db";
$username = "root";
$password = "root";
$dbname = "autot";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "virhe" => "Tietokantayhteys epäonnistui"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


// GET
if ($method === 'GET') {

    if ($id > 0) {

        $stmt = $conn->prepare(
            "SELECT id, nimi, sahkoposti FROM users WHERE id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 0) {

            http_response_code(404);

            echo json_encode([
                "virhe" => "Käyttäjää ei löytynyt"
            ], JSON_UNESCAPED_UNICODE);

        } else {

            echo json_encode(
                $result->fetch_assoc(),
                JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            );
        }

        $stmt->close();

    } else {

        $result = $conn->query(
            "SELECT id, nimi, sahkoposti FROM users"
        );

        $users = [];

        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        echo json_encode(
            $users,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );
    }
}


// POST
elseif ($method === 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['nimi']) || !isset($data['sahkoposti'])) {

        http_response_code(400);

        echo json_encode([
            "virhe" => "nimi ja sahkoposti ovat pakollisia"
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $nimi = $data['nimi'];
    $sahkoposti = $data['sahkoposti'];

    $stmt = $conn->prepare(
        "INSERT INTO users (nimi, sahkoposti) VALUES (?, ?)"
    );

    $stmt->bind_param("ss", $nimi, $sahkoposti);

    if ($stmt->execute()) {

        http_response_code(201);

        echo json_encode([
            "viesti" => "Käyttäjä lisätty",
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
}


// PUT
elseif ($method === 'PUT') {

    if ($id <= 0) {

        http_response_code(400);

        echo json_encode([
            "virhe" => "Käyttäjän ID puuttuu"
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['nimi']) || !isset($data['sahkoposti'])) {

        http_response_code(400);

        echo json_encode([
            "virhe" => "nimi ja sahkoposti ovat pakollisia"
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $nimi = $data['nimi'];
    $sahkoposti = $data['sahkoposti'];

    $stmt = $conn->prepare(
        "UPDATE users
         SET nimi = ?, sahkoposti = ?
         WHERE id = ?"
    );

    $stmt->bind_param("ssi", $nimi, $sahkoposti, $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE id = ?"
        );

        $check->bind_param("i", $id);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows === 0) {

            http_response_code(404);

            echo json_encode([
                "virhe" => "Käyttäjää ei löytynyt"
            ], JSON_UNESCAPED_UNICODE);

        } else {

            echo json_encode([
                "viesti" => "Käyttäjän tiedot olivat jo ennallaan"
            ], JSON_UNESCAPED_UNICODE);
        }

        $check->close();

    } else {

        echo json_encode([
            "viesti" => "Käyttäjä päivitetty",
            "id" => $id,
            "nimi" => $nimi,
            "sahkoposti" => $sahkoposti
        ], JSON_UNESCAPED_UNICODE);
    }

    $stmt->close();
}


// DELETE
elseif ($method === 'DELETE') {

    if ($id <= 0) {

        http_response_code(400);

        echo json_encode([
            "virhe" => "Käyttäjän ID puuttuu"
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $stmt = $conn->prepare(
        "DELETE FROM users WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {

        http_response_code(404);

        echo json_encode([
            "virhe" => "Käyttäjää ei löytynyt"
        ], JSON_UNESCAPED_UNICODE);

    } else {

        echo json_encode([
            "viesti" => "Käyttäjä poistettu",
            "id" => $id
        ], JSON_UNESCAPED_UNICODE);
    }

    $stmt->close();
}


// Muut metodit
else {

    http_response_code(405);

    echo json_encode([
        "virhe" => "HTTP-metodia ei tueta"
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();

?>