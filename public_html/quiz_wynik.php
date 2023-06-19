<?php
session_start();

$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if (!$isLoggedIn) {
    header("Location: zaloguj.php");
    exit();
}

if (!isset($_GET['nazwa'])) {
    header("Location: lista_quizow.php");
    exit();
}

$nazwa = $_GET['nazwa'];

require_once "connect.php";
global $host, $db_user, $db_password, $db_name;

$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Nie udało się połączyć z bazą danych: " . $conn->connect_error);
}

$sql = "SELECT * FROM `$nazwa`";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $pytania = $result->fetch_all(MYSQLI_ASSOC);
} else {
    header("Location: lista_quizow.php");
    exit();
}

$conn->close();

$poprawneOdpowiedzi = 0;

foreach ($pytania as $pytanie) {
    $idPytania = $pytanie['id'];

    if (isset($_POST['odpowiedz'][$idPytania])) {
        $odpowiedzUzytkownika = $_POST['odpowiedz'][$idPytania];
        $odpowiedzPoprawna = $pytanie['correct_answer'];

        if ($odpowiedzUzytkownika === $odpowiedzPoprawna) {
            $poprawneOdpowiedzi++;
        }
    }
}

$conn = new mysqli($host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Nie udało się połączyć z bazą danych: " . $conn->connect_error);
}

$login = $_SESSION["user"]["username"];

$punkty = $poprawneOdpowiedzi * 100;

$sql = "UPDATE `ranking` SET `punkty` = `punkty` + $punkty WHERE `login` = '$login'";

$conn->query($sql);

$sql = "UPDATE `Quiz` SET `execution_count` = `execution_count` + 1 WHERE `quiz_name` = '$nazwa'";

$conn->query($sql);
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wynik Quizu - LeisureTime!</title>
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
            position: sticky;
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
    <h1 class="text-3xl font-bold text-center mb-4">Wynik Quizu - <?php echo $nazwa; ?></h1>
    <div class="max-w-2xl mx-auto bg-white p-8 rounded shadow">
        <p class="mb-4">Twój wynik: <?php echo $poprawneOdpowiedzi; ?>/<?php echo count($pytania); ?></p>

        <h2 class="text-xl font-bold mb-2">Poprawne odpowiedzi:</h2>
        <ul>
            <?php foreach ($pytania as $pytanie): ?>
                <li><?php echo $pytanie['question']; ?> - <?php echo $pytanie['correct_answer']; ?></li>
            <?php endforeach; ?>
        </ul>

        <h2 class="text-xl font-bold mt-4 mb-2">Twoje odpowiedzi:</h2>
        <ul>
            <?php foreach ($pytania as $pytanie): ?>
                <?php
                $idPytania = $pytanie['id'];

                if (isset($_POST['odpowiedz'][$idPytania])) {
                    $odpowiedzUzytkownika = $_POST['odpowiedz'][$idPytania];
                    $odpowiedzPoprawna = $pytanie['correct_answer'];

                    $odpowiedzKolor = $odpowiedzUzytkownika === $odpowiedzPoprawna ? 'text-green-500' : 'text-red-500';
                } else {
                    $odpowiedzKolor = 'text-gray-500';
                }
                ?>
                <li class="<?php echo $odpowiedzKolor; ?>">
                    <?php echo $pytanie['question']; ?> - <?php echo $odpowiedzUzytkownika; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <br>

        <a href="lista_quizow.php"
           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Wróć
            do listy quizów</a>
    </div>
</main>


<footer class="bg-gray-900 text-white py-4 mt-8">
    <div class="container mx-auto px-4">
        <p class="text-center">&copy; 2023 LeisureTime!</p>
    </div>
</footer>

</body>
</html>
