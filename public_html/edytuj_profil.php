<?php
session_start();

$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if (!$isLoggedIn) {
    header("Location: zaloguj.php");
    exit();
}

require_once "connect.php";
global $host, $db_user, $db_password, $db_name;
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);

if ($polaczenie->connect_errno) {
    echo "Błąd połączenia z bazą danych: " . $polaczenie->connect_error;
    exit();
}
$user = $_SESSION['user'];
$username = $user["login"];
$username = $_GET['uzytkownik'];

$id = $user["ID"];

$result = $polaczenie->query("SELECT imie, nazwisko, opis, avatar FROM profile WHERE uzytkownik_id='$id'");

if (!$result) {
    echo "Błąd zapytania: " . $polaczenie->error;
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $imie = $_POST["imie"];
    $nazwisko = $_POST["nazwisko"];
    $opis = $_POST["opis"];
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatar = $_FILES['avatar'];
        $avatarPath = "avatar/" . basename($avatar['name']);
        $zdjecie = basename($avatar['name']);

        move_uploaded_file($avatar['tmp_name'], $avatarPath);

        $updateQuery = "UPDATE profile SET imie='$imie', nazwisko='$nazwisko', opis='$opis', avatar='$zdjecie' WHERE uzytkownik_id='$id'";
    } else {
        $updateQuery = "UPDATE profile SET imie='$imie', nazwisko='$nazwisko', opis='$opis' WHERE uzytkownik_id='$id'";
    }

    if ($polaczenie->query($updateQuery) === TRUE) {
        header("Location: profil.php");
    } else {
        echo "Błąd aktualizacji danych użytkownika: " . $polaczenie->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title>Edytuj profil - LeisureTime!</title>
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
            <?php if ($isLoggedIn && isset($_SESSION['user']) && $_SESSION['user']['login'] === $username): ?>
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

<main>
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mt-8">Edytuj swój profil</h1>
        <form class="mx-auto mt-8" method="post" enctype="multipart/form-data">
            <div class="mt-4">
                <label class="block text-gray-700 font-bold mb-2" for="imie">Imię</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="imie" type="text" name="imie" value="<?php echo isset($imie) ? $imie : ''; ?>" required>
            </div>
            <div class="mt-4">
                <label class="block text-gray-700 font-bold mb-2" for="nazwisko">Nazwisko</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="nazwisko" type="text" name="nazwisko"
                       value="<?php echo isset($nazwisko) ? $nazwisko : ''; ?>" required>
            </div>
            <div class="mt-4">
                <label class="block text-gray-700 font-bold mb-2" for="opis">Opis</label>
                <textarea
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="opis" name="opis"><?php echo isset($opis) ? $opis : ''; ?></textarea>
            </div>
            <div class="mt-4">
                <label class="block text-gray-700 font-bold mb-2" for="avatar">Zdjęcie</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="avatar" type="file" name="avatar" accept="image/*">
            </div>
            <div class="mt-8">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="submit">Zapisz
                </button>
            </div>
        </form>
    </div>
</main>
<footer class="bg-gray-900 text-white py-4">
    <div class="container mx-auto px-4">
        <p class="text-center">&copy; 2023 LeisureTime!</p>
    </div>
</footer>
</body>
</html>
