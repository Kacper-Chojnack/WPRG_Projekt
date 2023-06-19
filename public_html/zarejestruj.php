<?php

session_start();

if (isset($_POST['email'])) {
    $wszystko_OK = true;
    $login = $_POST['login'];

    if ((strlen($login) < 3) || (strlen($login) > 20)) {
        $wszystko_OK = false;
        $_SESSION['e_login'] = "Nick musi posiadać od 3 do 20 znaków!";
    }

    if (ctype_alnum($login) == false) {
        $wszystko_OK = false;
        $_SESSION['e_login'] = "Nick może składać się tylko z liter i cyfr (bez polskich znaków)";
    }
    $email = $_POST['email'];
    $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);

    if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $email)) {
        $wszystko_OK = false;
        $_SESSION['e_email'] = "Podaj poprawny adres e-mail!";
    }
    $password = $_POST['password'];
    $samePassword = $_POST['samePassword'];

    if ((strlen($password) < 8) || (strlen($password) > 20)) {
        $wszystko_OK = false;
        $_SESSION['e_password'] = "Hasło musi posiadać od 8 do 20 znaków!";
    }

    if ($password != $samePassword) {
        $wszystko_OK = false;
        $_SESSION['e_password'] = "Podane hasła nie są identyczne!";
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    if (!isset($_POST['regulamin'])) {
        $wszystko_OK = false;
        $_SESSION['e_regulamin'] = "Potwierdź akceptację regulaminu!";
    }

    $_SESSION['fr_login'] = $login;
    $_SESSION['fr_email'] = $email;
    $_SESSION['fr_password'] = $password;
    $_SESSION['fr_samePassword'] = $samePassword;
    if (isset($_POST['regulamin'])) $_SESSION['fr_regulamin'] = true;

    include_once "connect.php";
    mysqli_report(MYSQLI_REPORT_STRICT);

    try {
        global $host, $db_user, $db_password, $db_name;
        $polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
        if ($polaczenie->connect_errno != 0) {
            throw new Exception(mysqli_connect_errno());
        } else {
            $rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE email='$email'");

            if (!$rezultat) throw new Exception($polaczenie->error);

            $ile_takich_maili = $rezultat->num_rows;
            if ($ile_takich_maili > 0) {
                $wszystko_OK = false;
                $_SESSION['e_email'] = "Istnieje już konto przypisane do tego adresu e-mail!";
            }

            $rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE login='$login'");

            if (!$rezultat) throw new Exception($polaczenie->error);

            $ile_takich_nickow = $rezultat->num_rows;
            if ($ile_takich_nickow > 0) {
                $wszystko_OK = false;
                $_SESSION['e_login'] = "Istnieje już użytkownik o takim nicku! Wybierz inny.";
            }

            if ($wszystko_OK == true) {
                if ($polaczenie->query("INSERT INTO uzytkownicy VALUES (NULL, '$login', '$password_hash', '$email')")) {
                    $id = $polaczenie->insert_id;

                    $sql_insert_profile = "INSERT INTO profile (ID, uzytkownik_id, imie) VALUES (NULL, '$id', '$login')";
                    $query = "INSERT INTO ranking (login, punkty) VALUES ('$login', 0)";

                    if ($polaczenie->query($sql_insert_profile) === TRUE && $polaczenie->query($query) === TRUE) {
                        $polaczenie->commit();
                        $_SESSION['udanarejestracja'] = true;

                        header('Location: zarejestrowano.php');
                    } else {
                        throw new Exception($polaczenie->error);
                    }
                } else {
                    throw new Exception($polaczenie->error);

                }
            }
            $polaczenie->close();
        }

    } catch (Exception $e) {
        $informationAboutBase = '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title>LeisureTime! Zarejestruj się</title>
    <link rel="icon" type="image/png" href="logo.png">
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
<form class="mx-auto" method="post">
    <label class="block text-gray-700 font-bold mb-2" for="name"><br>
        Login użytkownika
    </label>
    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
           id="login" type="text" name="login" required value="<?php
    if (isset($_SESSION['fr_login'])) {
        echo $_SESSION['fr_login'];
        unset($_SESSION['fr_login']);
    }
    ?>">
    <h1>
        <?php
        if (isset($_SESSION['e_login'])) {
            echo '<div class="error">' . $_SESSION['e_login'] . '</div>';
            unset($_SESSION['e_login']);
        }
        ?>
    </h1>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2" for="email"><br>
            Adres e-mail
        </label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
               id="email" type="email" name="email" required value="<?php
        if (isset($_SESSION['fr_email'])) {
            echo $_SESSION['fr_email'];
            unset($_SESSION['fr_email']);
        }
        ?>">
        <h1>
            <?php
            if (isset($_SESSION['e_email'])) {
                echo '<div class="error">' . $_SESSION['e_email'] . '</div>';
                unset($_SESSION['e_email']);
            }
            ?>
        </h1>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2" for="password">
            Hasło
        </label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
               id="password" type="password" name="password" required value="<?php
        if (isset($_SESSION['fr_password'])) {
            echo $_SESSION['fr_password'];
            unset($_SESSION['fr_password']);
        }
        ?>">
        <h1>
            <?php
            if (isset($_SESSION['e_password'])) {
                echo '<div class="error">' . $_SESSION['e_password'] . '</div>';
                unset($_SESSION['e_password']);
            }
            ?>
        </h1>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2" for="email">
            Powtórz hasło
        </label>
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
               id="samePassword" type="password" name="samePassword" required value="<?php
        if (isset($_SESSION['fr_samePassword'])) {
            echo $_SESSION['fr_samePassword'];
            unset($_SESSION['fr_samePassword']);
        }
        ?>">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2" for="email">
            Akceptuję <a href="regulamin.php" target="_blank">regulamin </a> <input id="regulamin" type="checkbox"
                                                                                    name="regulamin" required <?php
            if (isset($_SESSION['fr_regulamin'])) {
                echo "checked";
                unset($_SESSION['fr_regulamin']);
            }
            ?>>
            <h1>
                <?php
                if (isset($_SESSION['e_regulamin'])) {
                    echo '<div class="error">' . $_SESSION['e_regulamin'] . '</div>';
                    unset($_SESSION['e_regulamin']);
                }
                ?>
            </h1>
        </label>
    </div>

    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
            type="submit">
        Zarejestruj się
    </button>
    <br>
    <h3><?php if (!(empty($informationAboutBase))) {
            echo $informationAboutBase;
        } ?></h3>
</form>
<footer class="bg-gray-900 text-white py-4 mt-8">
    <div class="container mx-auto px-4">
        <p class="text-center">&copy; 2023 LeisureTime!</p>
    </div>
</footer>
</body>
</html>