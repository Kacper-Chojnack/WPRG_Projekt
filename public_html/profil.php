<?php
session_start();

require "connect.php";
global $host, $db_user, $db_password, $db_name;
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);

if ($polaczenie->connect_errno) {
    echo "Błąd połączenia z bazą danych: " . $polaczenie->connect_error;
    exit();
}

$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;
if ($isLoggedIn) {
    if (isset($_GET['uzytkownik'])) {
        $username = $_GET['uzytkownik'];
    } else {
        $user = $_SESSION['user'];
        $username = $user["login"];
        header("Location: profil.php?uzytkownik=$username");
        exit();
    }
}

if (isset($_GET['uzytkownik'])) {
    $username = $_GET['uzytkownik'];
} else {
    $user = $_SESSION['user'];
    $username = $user["login"];
}

$query = "SELECT p.imie, p.nazwisko, p.opis, p.avatar 
          FROM profile AS p
          JOIN uzytkownicy AS u ON p.uzytkownik_id = u.ID 
          WHERE u.login='$username'";

$result = $polaczenie->query($query);

if (!$result) {
    echo "Błąd zapytania: " . $polaczenie->error;
    exit();
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $imie = $row["imie"];
    $nazwisko = $row["nazwisko"];
    $opis = $row["opis"];
    $avatar = "avatar/" . $row["avatar"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil - LeisureTime!</title>
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

        table {
            margin: 0 auto;
        }

        .container {
            max-height: 600px;
            overflow: auto;
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

<div class="container mx-auto px-4">
    <?php if ($isLoggedIn && isset($_SESSION['user']) && $_SESSION['user']['login'] === $username): ?>
        <h1 class="text-6xl font-bold text-center my-8" style="color: white; margin-top: 40px;">Twój Profil</h1>
    <?php else: ?>
        <h1 class="text-6xl font-bold text-center my-8" style="color: white; margin-top: 40px;">Profil
            użytkownika <?php echo $username; ?></h1>
    <?php endif; ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="flex justify-center items-center">
            <div class="w-1/4">
                <img src="<?php echo $avatar; ?>" alt="Zdjęcie profilowe" class="rounded-full w-full"
                     style="width: 300px; height: 300px;">
            </div>

            <div class="w-1/2 ml-8">
                <h2 class="text-4xl font-bold"><?php echo $imie . ' ' . $nazwisko; ?></h2>
                <p class="text-xl mt-4"><?php echo $opis; ?></p>
            </div>
            <?php if ($isLoggedIn && isset($_SESSION['user']) && $_SESSION['user']['login'] === $username): ?>
                <div class="flex justify-center mt-4">
                    <div class="ml-auto">
                        <a href="edytuj_profil.php"
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Edytuj profil
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php
        $query = "SELECT * FROM Quiz WHERE author_login='$username'";
        $quizzes = $polaczenie->query($query);

        if (!$quizzes) {
            echo "Błąd zapytania: " . $polaczenie->error;
            exit();
        }

        if ($quizzes->num_rows > 0) {
            echo '<table class="mt-4 mx-auto">';
            echo '<tr>';
            echo '<th class="px-4 py-2">Nazwa quizu</th>';
            echo '<th class="px-4 py-2">Liczba pytań</th>';
            echo '<th class="px-4 py-2">Data utworzenia</th>';
            echo '<th class="px-4 py-2">Liczba wykonań</th>';
            echo '</tr>';

            while ($quiz = $quizzes->fetch_assoc()) {
                echo '<tr>';
                echo '<td class="border px-4 py-2 text-red-500 font-bold hover:underline "><a href="https://leisuretime.pl/quiz.php?nazwa=' . $quiz['quiz_name'] . '">' . $quiz['quiz_name'] . '</a></td>';
                echo '<td class="border px-4 py-2">' . $quiz['question_count'] . '</td>';
                echo '<td class="border px-4 py-2">' . $quiz['creation_date'] . '</td>';
                echo '<td class="border px-4 py-2">' . $quiz['execution_count'] . '</td>';
                echo '</tr>';
            }

            echo '</table>';

        }
        ?>

    <?php else: ?>
        <h1 class="text-6xl font-bold text-center my-8" style="color: white; margin-top: 125px;">Nie znaleziono
            użytkownika!</h1>
    <?php endif; ?>

    <footer class="bg-gray-900 text-white py-4 mt-8">
        <div class="container mx-auto px-4 text-center">
            &copy; 2023 LeisureTime!
        </div>
    </footer>
</div>
</body>
</html>
