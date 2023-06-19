<?php

session_start();

if ((isset($_SESSION['zarejestrowany'])) && ($_SESSION['zalogowany'] == true)) {
    header('Location: zarejestrowano.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leisuretime! Zarejestrowano</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css">
    <style>
        html {
            height: 100%;
            margin: 0;
            min-height: 100vh;
        }

        body {
            background-image: url("tlo.jpg");
            background-size: cover;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .center-button {
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>
<header class="bg-gray-900 text-white py-4">
    <div class="container mx-auto px-4 flex justify-between items-center">
        <a href="index.php" class="text-3xl font-bold">LeisureTime!</a>
        <div class="flex items-center">
            <form action="zarejestruj.php" method="post">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2"
                        type="submit">
                    Zarejestruj
                </button>
            </form>
            <form action="zaloguj.php" method="post">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="submit">
                    Zaloguj
                </button>
            </form>
        </div>
    </div>
</header>
<div class="container mx-auto px-4">
    <h2 class="text-6xl font-bold text-center my-8" style="color: white; margin-top: 125px;">Pomyślnie
        zarejestrowano!</h2><br>
    <div class="center-button">
        <form action="zaloguj.php" method="post">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline text-center"
                    type="submit">
                Zaloguj
            </button>
        </form>
    </div>
    <footer class="bg-gray-900 text-white py-4 mt-8">
        <div class="container mx-auto px-4">
            <p class="text-center">&copy; 2023 LeisureTime!</p>
        </div>
    </footer>
</body>
</html>
