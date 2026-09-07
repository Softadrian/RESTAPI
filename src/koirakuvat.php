<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satunnaisia koirakuvia</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            text-align: center;
            margin: 0;
            padding: 30px;
        }

        h1 {
            color: #333;
        }

        .gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin: 30px auto;
            max-width: 1200px;
        }

        .gallery img {
            width: 220px;
            height: 220px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h1>Satunnaisia koirakuvia</h1>

    <?php

    // Haettavien kuvien määrä
    $count = 10;

    // Haetaan kuvat Dog CEO API:sta
    $response = file_get_contents(
        "https://dog.ceo/api/breeds/image/random/$count"
    );

    // Muutetaan JSON PHP-taulukoksi
    $data = json_decode($response, true);

    // Tarkistetaan onnistuiko haku
    if ($data && $data['status'] === 'success') {

        echo '<div class="gallery">';

        // Käydään kuvat läpi
        foreach ($data['message'] as $imgUrl) {

            echo '<img src="' .
                htmlspecialchars($imgUrl) .
                '" alt="Koirakuva">';
        }

        echo '</div>';

    } else {

        echo '<p class="error">Virhe kuvien haussa.</p>';
    }

    ?>

    <!-- Painike uusien kuvien hakemiseen -->
    <form method="get">
        <button type="submit">Hae uudet kuvat</button>
    </form>

</body>
</html>
