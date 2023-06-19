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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Powodzenia! - LeisureTime!</title>
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

        table {
            margin: 0 auto;
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
    <h1 class="text-3xl font-bold text-center mb-4">Quiz - <?php echo $nazwa; ?></h1>
    <div class="max-w-2xl mx-auto bg-white p-8 rounded shadow">
        <form action="quiz_wynik.php?nazwa=<?php echo $nazwa; ?>" method="post" class="space-y-4">
            <?php foreach ($pytania as $pytanie): ?>
                <div class="mb-4">
                    <h2 class="text-2xl font-bold mb-2">Pytanie <?php echo $pytanie['id']; ?></h2>
                    <p class="mb-2"><?php echo "Treść pytania: " . $pytanie['question']; ?></p>
                    <?php if ($pytanie['image']): ?>
                        <img src="photos/<?php echo $pytanie['image']; ?>" alt="Zdjęcie" class="w-full"
                             style="width: 300px; height: 300px;">
                    <?php endif; ?>
                    <div class="flex flex-col space-y-2"><br>
                        <?php $odpowiedzi = array($pytanie['answer_a'], $pytanie['answer_b'], $pytanie['answer_c'], $pytanie['answer_d']); ?>
                        <?php shuffle($odpowiedzi); ?>
                        <?php foreach ($odpowiedzi as $odpowiedz): ?>
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="odpowiedz[<?php echo $pytanie['id']; ?>]"
                                       value="<?php echo $odpowiedz; ?>" class="mr-2">
                                <span><?php echo $odpowiedz; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="flex justify-end">
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded focus:outline-none focus:shadow-outline">
                    Zakończ
                </button>
            </div>
        </form>
    </div>
</main>

<footer class="bg-gray-900 text-white py-4 mt-8">
    <div class="container mx-auto px-4">
        <p class="text-center">&copy; 2023 LeisureTime!</p>
    </div>
</footer>

</body>
</html>
