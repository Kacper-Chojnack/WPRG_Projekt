<?php
session_start();

$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if (!$isLoggedIn) {
    header("Location: zaloguj.php");
    exit();
}

$id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['title']) && !empty($_POST['title']) &&
        isset($_POST['description']) && !empty($_POST['description']) &&
        isset($_POST['numQuestions']) && !empty($_POST['numQuestions'])
    ) {
        $title = $_POST['title'];
        $title = str_replace(' ', '_', $title);
        $title = urlencode($title); // Zakoduj nazwę quizu

        $description = $_POST['description'];
        $numQuestions = (int)$_POST['numQuestions'];

        require_once "connect.php";

        $conn = new mysqli($host, $db_user, $db_password, $db_name);

        if ($conn->connect_error) {
            die("Nie udało się połączyć z bazą danych: " . $conn->connect_error);
        }

        $sql_check = "SELECT quiz_name FROM Quiz WHERE quiz_name = '$title'";
        $result_check = $conn->query($sql_check);

        if ($result_check->num_rows > 0) {
            $error = "Nazwa quizu jest już zajęta. Wybierz inną nazwę.";
        } else {
            if ($numQuestions > 0 && $numQuestions <= 10) {
                $_SESSION['quiz_title'] = urldecode($title);
                $_SESSION['quiz_description'] = $description;
                $_SESSION['quiz_numQuestions'] = $numQuestions;

                header("Location: wypelnij_quiz.php");
                exit();
            } else {
                $error = "Liczba pytań musi być między 1 a 10.";
            }
        }

        $conn->close();
    } else {
        $error = "Wszystkie pola są wymagane.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stwórz quiz - LeisureTime!</title>
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
    <h1 class="text-3xl font-bold text-center mb-4">Stwórz swój quiz</h1>
    <div class="max-w-2xl mx-auto bg-white p-8 rounded shadow">
        <form action="stworz_quiz.php" method="post">
            <?php if (isset($error)): ?>
                <p class="text-red-500 mb-4"><?php echo $error; ?></p>
            <?php endif; ?>
            <div class="mb-4">
                <label for="title" class="block font-bold text-gray-700">Tytuł quizu:</label>
                <input type="text" id="title" name="title" class="w-full border border-gray-300 rounded px-4 py-2"
                       required>
            </div>
            <div class="mb-4">
                <label for="description" class="block font-bold text-gray-700">Opis quizu:</label>
                <textarea id="description" name="description" class="w-full border border-gray-300 rounded px-4 py-2"
                          required></textarea>
            </div>
            <div class="mb-4">
                <label for="numQuestions" class="block font-bold text-gray-700">Liczba pytań (max 10):</label>
                <input type="number" id="numQuestions" name="numQuestions"
                       class="w-full border border-gray-300 rounded px-4 py-2" min="1" max="10" required>
            </div>
            <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Przejdź do tworzenia quizu
            </button>
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
