<?php
session_start();

$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if ($isLoggedIn) {
    header("Location: index.php");
    exit();
}

if (isset($_POST["login"])) {
    $login = $_POST["login"];
    $password = $_POST["password"];
    require_once "connect.php";
    $sql = "SELECT * FROM uzytkownicy WHERE login = '$login'";
    global $host, $db_user, $db_password, $db_name, $conn;
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $id = $user['id'];
    if ($user) {
        if (password_verify($password, $user["password"])) {
            $_SESSION["zalogowany"] = true;
            $_SESSION["user"] = $user;
            $_SESSION['user']['username'] = $login;
            $_SESSION['id'] = $id;
            header("Location: zalogowano.php");
            exit();
        } else {
            $password_error = "Podano nieprawidłowe hasło.";
        }
    } else {
        $login_error = "Podano nieprawidłowy login.";
    }

}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>LeisureTime! Zaloguj się</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css">
    <style>
        form {
            max-width: 600px;
            margin: 0 auto;
        }

        html, body {
            height: 100%;
            margin: 0;
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
        <a href="index.php" class="text-3xl font-bold">LeisureTime!</a>
        <div class="flex items-center">
            <?php if ($isLoggedIn): ?>
                <form action="wyloguj.php" method="post">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
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
<main class="flex-grow">
    <form class="mx-auto mt-8" method="post">
        <label class="block text-gray-700 font-bold mb-2" for="login">Login użytkownika</label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
               id="login" type="text" name="login" required>
        <?php if (isset($login_error)): ?>
            <div class="text-red-500"><?php echo $login_error; ?></div>
        <?php endif; ?>
        <div class="mt-4">
            <label class="block text-gray-700 font-bold mb-2" for="password">Hasło</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                   id="password" type="password" name="password" required>
            <?php if (isset($password_error)): ?>
                <div class="text-red-500"><?php echo $password_error; ?></div>
            <?php endif; ?>
        </div>
        <?php
        if (isset($_SESSION['blad'])) {
            echo '<div class="error">' . $_SESSION['blad'] . '</div>';
            unset($_SESSION['blad']);
        }
        ?>
        <?php if ($isLoggedIn): ?>
            <p>Zalogowany użytkownik: <?php echo $_SESSION["user"]["username"]; ?></p>
            <p>ID użytkownika: <?php echo $_SESSION["id"]; ?></p>
        <?php endif; ?>
        <div class="mt-8">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">Zaloguj się
            </button>
        </div>
    </form>
</main>
<footer class="bg-gray-900 text-white py-4 mt-8">
    <div class="container mx-auto px-4">
        <p class="text-center">&copy; 2023 LeisureTime!</p>
    </div>
</footer>
</body>
</html>
