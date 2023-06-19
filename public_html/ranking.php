<?php
session_start();
$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if (!$isLoggedIn) {
    header("Location: zaloguj.php");
    exit();
}
require "connect.php";
global $host, $db_user, $db_password, $db_name;
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);

if ($polaczenie->connect_errno) {
    echo "Błąd połączenia z bazą danych: " . $polaczenie->connect_error;
    exit();
}

$query = "SELECT login, punkty FROM ranking ORDER BY punkty DESC";
$result = $polaczenie->query($query);

if (!$result) {
    echo "Błąd zapytania: " . $polaczenie->error;
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ranking - LeisureTime!</title>
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

        .bg-brown-200 {
            background-color: #CD7F32;
        }
    </style>
</head>
<body>
<header class="bg-gray-900 text-white py-4">
    <div class="container mx-auto px-4 flex justify-between items-center">
        <div class="flex items-center">
            <a href="index.php" class="text-3xl font-bold">LeisureTime!</a>
            <?php if ($isLoggedIn && isset($_SESSION['user'])): ?>
                <div class="flex justify-center items-center ml-4">
                    <a href="ranking.php" class="text-yellow-500 font-bold">Ranking</a>
                    <a href="lista_quizow.php" class="text-yellow-500 font-bold ml-4">Lista Quizów</a>
                    <a href="stworz_quiz.php"
                       class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-4">Stwórz
                        swój quiz</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="flex items-center">
            <?php if ($isLoggedIn): ?>
                <form action="profil.php" method="get">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2"
                            type="submit">
                        Twój profil
                    </button>
                </form>
                <form action="wyloguj.php" method="post">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline "
                            type="submit">
                        Wyloguj
                    </button>
                </form>
            <?php else: ?>
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
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="container mx-auto mt-8">
    <h1 class="text-3xl font-bold mb-4 flex items-center justify-center">Ranking</h1>
    <div class="mx-auto">
        <table class="table-auto w-full border border-gray-300">
            <thead>
            <tr>
                <th class="px-4 py-2 bg-gray-200">Miejsce</th>
                <th class="px-4 py-2 bg-gray-200">Użytkownik</th>
                <th class="px-4 py-2 bg-gray-200">Punkty</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $place = 1;
            while ($row = $result->fetch_assoc()):
                $username = $row['login'];
                $points = $row['punkty'];
                ?>
                <?php if ($place === 1): ?>
                <tr class="bg-yellow-200">
            <?php elseif ($place === 2): ?>
                <tr class="bg-gray-300">
            <?php elseif ($place === 3): ?>
                <tr class="bg-brown-200">
            <?php else: ?>
                <tr>
            <?php endif; ?>
                <td class="border px-4 py-2"><?php echo $place; ?></td>
                <td class="border px-4 py-2">
                    <a href="profil.php?uzytkownik=<?php echo $username; ?>"
                       class="text-red-500 font-bold hover:underline">
                        <?php echo $username; ?>
                    </a>
                </td>
                <td class="border px-4 py-2"><?php echo $points; ?></td>
                </tr>
                <?php
                $place++;
            endwhile;
            ?>
            </tbody>
        </table>
    </div>
</main>


<footer class="bg-gray-900 text-white py-4 mt-8">
    <div class="container mx-auto px-4">
        <p class="text-center">&copy; 2023 LeisureTime!</p>
    </div>
</footer>

</body>
</html>

<?php
$result->free_result();
$polaczenie->close();
?>
