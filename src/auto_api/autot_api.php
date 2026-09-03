<?php
// Palautetaan aina JSON
header("Content-Type: application/json; charset=UTF-8");
include 'db_config.php';

// Selvitetään pyyntömetodi (GET, POST, PUT, DELETE, jne.)
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Jos kysytään yksittäistä autoa ID:llä
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            getAuto(intval($_GET['id']));
        } else {
            // Muuten palautetaan kaikki autot
            getAllAutot();
        }
        break;

    case 'POST':
        // Tarkistetaan että sisältötyyppi on JSON (voi olla myös "application/json; charset=UTF-8")
        if (strpos($_SERVER["CONTENT_TYPE"], "application/json") === 0) {
            addAuto();
        } else {
            http_response_code(415);
            echo json_encode(["message" => "Sisältötyyppi ei tuettu."]);
        }
        break;

    case 'PUT':
        // PUT-metodissa käytetään samaa JSON-lukemista kuin POSTissa
        if (strpos($_SERVER["CONTENT_TYPE"], "application/json") === 0) {
            $data = json_decode(file_get_contents("php://input"), true);
            updateAuto($data);
        } else {
            http_response_code(415);
            echo json_encode(["message" => "Sisältötyyppi ei tuettu."]);
        }
        break;

    case 'DELETE':
        // Poistetaan auto vain jos ID on annettu ja validi
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            deleteAuto(intval($_GET['id']));
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID puuttuu tai ei ole kelvollinen."]);
        }
        break;

    default:
        // Jos käytetään muuta metodia (esim. PATCH), palautetaan virhe
        http_response_code(405);
        echo json_encode(["message" => "Metodi ei sallittu."]);
        break;
}

/**
 * Hakee kaikki autot tietokannasta
 */
function getAllAutot() {
    global $conn;
    $stmt = $conn->prepare("SELECT ID, merkki, tyyppi, vuosimalli FROM autot");
    $stmt->execute();
    $result = $stmt->get_result();
    $autot = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($autot);
    $stmt->close();
}

/**
 * Hakee yhden auton ID:n perusteella
 */
function getAuto($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT ID, merkki, tyyppi, vuosimalli FROM autot WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        echo json_encode($result->fetch_assoc());
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Autoa ei löytynyt ID:llä $id"]);
    }
    $stmt->close();
}

/**
 * Lisää uuden auton tietokantaan
 */
function addAuto() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);

    // Varmistetaan että kaikki kentät löytyvät
    if (!empty($data['merkki']) && !empty($data['tyyppi']) && !empty($data['vuosimalli'])) {
        $merkki = $data['merkki'];
        $tyyppi = $data['tyyppi'];
        $vuosimalli = intval($data['vuosimalli']); // Varmistetaan että vuosimalli on numero

        $stmt = $conn->prepare("INSERT INTO autot (merkki, tyyppi, vuosimalli) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $merkki, $tyyppi, $vuosimalli);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Auto lisätty onnistuneesti."]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Auton lisääminen epäonnistui."]);
        }
        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Tietoja puuttuu."]);
    }
}

/**
 * Päivittää olemassa olevan auton
 */
function updateAuto($data) {
    global $conn;
    if (!empty($data['ID']) && !empty($data['merkki']) && !empty($data['tyyppi']) && !empty($data['vuosimalli'])) {
        $id = intval($data['ID']);
        $merkki = $data['merkki'];
        $tyyppi = $data['tyyppi'];
        $vuosimalli = intval($data['vuosimalli']);

        $stmt = $conn->prepare("UPDATE autot SET merkki = ?, tyyppi = ?, vuosimalli = ? WHERE ID = ?");
        $stmt->bind_param("ssii", $merkki, $tyyppi, $vuosimalli, $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(["message" => "Auto päivitetty onnistuneesti."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Autoa ei löytynyt ID:llä $id"]);
            }
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Auton päivittäminen epäonnistui."]);
        }
        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Tietoja puuttuu."]);
    }
}

/**
 * Poistaa auton ID:n perusteella
 */
function deleteAuto($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM autot WHERE ID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["message" => "Auto poistettu onnistuneesti."]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Autoa ei löytynyt ID:llä $id"]);
        }
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Auton poistaminen epäonnistui."]);
    }
    $stmt->close();
}

// Suljetaan tietokantayhteys
$conn->close();
?>
