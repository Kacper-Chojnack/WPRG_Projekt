<?php
session_start();

$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;
$login = $_SESSION['login'];

if (!$isLoggedIn) {
    $showLimitedAccess = true;
} else {
    $user = $_SESSION['user'];
    $username = $user["login"];
    $showLimitedAccess = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LeisureTime!</title>
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
    </style>
</head>
<body>
<header class="bg-gray-900 text-white py-4">
    <div class="container mx-auto px-4 flex justify-between items-center">
        <div class="flex items-center">
            <a href="index.php" class="text-3xl font-bold">LeisureTime!</a>
            <?php if (!$showLimitedAccess): ?>
                <div class="flex justify-center items-center ml-4">
                    <a href="ranking.php" class="text-yellow-500 font-bold">Ranking</a>
                    <a href="lista_quizow.php" class="text-yellow-500 font-bold ml-4">Lista Quizów</a>
                    <a href="stworz_quiz.php" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-4">Stwórz swój quiz</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="flex items-center">
            <?php if ($isLoggedIn): ?>
                <form action="profil.php" method="get">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2" type="submit">
                        Twój profil
                    </button>
                </form>
                <form action="wyloguj.php" method="post">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline " type="submit">
                        Wyloguj
                    </button>
                </form>
            <?php else: ?>
                <form action="zarejestruj.php" method="post">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2" type="submit">
                        Zarejestruj
                    </button>
                </form>
                <form action="zaloguj.php" method="post">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Zaloguj
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="container mx-auto px-4">
    <?php if ($showLimitedAccess): ?>
        <div class="text-center my-8" style="color: white; margin-top: 125px;">
            <h1 class="text-6xl font-bold">Witaj na LeisureTime! - w samym sercu dobrej rozrywki!</h1>
            <div class="flex justify-between mt-8">
                <form action="zarejestruj.php" method="post">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2" type="submit">
                        Zarejestruj
                    </button>
                </form>
                <form action="zaloguj.php" method="post">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Zaloguj
                    </button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <h1 class="text-6xl font-bold text-center my-8" style="color: white; margin-top: 125px;">Witaj na LeisureTime! - w samym sercu dobrej rozrywki!</h1>
        <div class="flex justify-between">
            <a href="najnowszy_quiz.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Ostatnio dodany quiz</a>
            <a href="najpopularniejszy_quiz.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Najpopularniejszy quiz</a>
        </div>
    <?php endif; ?>
</div>
<footer class="bg-gray-900 text-white py-4 mt-8">
    <div class="container mx-auto px-4">
        <p class="text-center">&copy; 2023 LeisureTime!</p>
    </div>
</footer>
</body>
</html>
