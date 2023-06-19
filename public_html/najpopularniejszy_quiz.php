<?php
session_start();
$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if (!$isLoggedIn) {
    header("Location: Zalogujj_PROJEKT.php");
    exit();
}

require_once "connect.php";

$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Nie udało się połączyć z bazą danych: " . $conn->connect_error);
}

$sql = "SELECT quiz_name, question_count, author_login, creation_date, execution_count 
                FROM Quiz 
                ORDER BY execution_count DESC 
                LIMIT 1";
$result = $conn->query($sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Najpopularniejszy quiz - LeisureTime!</title>
    <link rel="icon" type="image/png" href="logo.png">    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css">
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
    <h1 class="text-3xl font-bold mb-4 flex items-center justify-center">Najpopularniejszy quiz</h1>
    <div class="mx-auto">
        <table class="table-auto w-full border border-gray-300">
            <thead>
            <tr>
                <th class="px-4 py-2 bg-gray-200">Nazwa quizu</th>
                <th class="px-4 py-2 bg-gray-200">Liczba pytań</th>
                <th class="px-4 py-2 bg-gray-200">Nazwa autora</th>
                <th class="px-4 py-2 bg-gray-200">Data utworzenia</th>
                <th class="px-4 py-2 bg-gray-200">Liczba wykonań</th>

            </tr>
            </thead>
            <tbody>
            <?php
            while ($row = $result->fetch_assoc()):
                $nazwa = $row['quiz_name'];
                $ilosc_pytan = $row['question_count'];
                $nazwa_uzytkownika = $row['author_login'];
                $data = $row['creation_date'];
                $ilosc_wykonan = $row['execution_count'];

                ?>
                <tr>
                    <td class="border px-4 py-2">
                        <a href="quiz.php?=<?php echo $nazwa; ?>" class="text-red-500 font-bold hover:underline">
                            <?php echo $nazwa; ?>
                        </a>
                    </td>
                    <td class="border px-4 py-2"><?php echo $ilosc_pytan; ?></td>
                    <td class="border px-4 py-2">
                        <a href="profil.php?uzytkownik=<?php echo $nazwa_uzytkownika; ?>"
                           class="text-red-500 font-bold hover:underline">
                            <?php echo $nazwa_uzytkownika; ?>
                        </a>
                    </td>
                    <td class="border px-4 py-2"><?php echo $data; ?></td>
                    <td class="border px-4 py-2"><?php echo $ilosc_wykonan; ?></td>
                </tr>
            <?php
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

