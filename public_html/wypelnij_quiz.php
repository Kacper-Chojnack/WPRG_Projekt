<?php
session_start();
$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;

if (!$isLoggedIn) {
    header("Location: Zaloguj.php");
    exit();
}

$id = $_SESSION['id'];

if (
    !isset($_SESSION['quiz_title']) ||
    !isset($_SESSION['quiz_description']) ||
    !isset($_SESSION['quiz_numQuestions'])
) {
    header("Location: stworz_quiz.php");
    exit();
}

$title = $_SESSION['quiz_title'];
$description = $_SESSION['quiz_description'];
$numQuestions = $_SESSION['quiz_numQuestions'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valid = true;
    $error = "";

    for ($i = 1; $i <= $numQuestions; $i++) {
        if (
            !isset($_POST['question_' . $i]) || empty($_POST['question_' . $i]) ||
            !isset($_POST['question_type_' . $i]) || empty($_POST['question_type_' . $i]) ||
            !isset($_POST['correct_answer_' . $i]) || empty($_POST['correct_answer_' . $i]) ||
            !isset($_POST['answer_a_' . $i]) || empty($_POST['answer_a_' . $i]) ||
            !isset($_POST['answer_b_' . $i]) || empty($_POST['answer_b_' . $i]) ||
            !isset($_POST['answer_c_' . $i]) || empty($_POST['answer_c_' . $i]) ||
            !isset($_POST['answer_d_' . $i]) || empty($_POST['answer_d_' . $i])
        ) {
            $valid = false;
            $error = "Wszystkie pola są wymagane.";
            break;
        }
    }

    if ($valid) {
        require_once "connect.php";
        global $host, $db_user, $db_password, $db_name;
        $conn = new mysqli($host, $db_user, $db_password, $db_name);

        if ($conn->connect_error) {
            die("Nie udało się połączyć z bazą danych: " . $conn->connect_error);
        }

        $table_name = str_replace(' ', '_', $title);
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            question TEXT NOT NULL,
            type VARCHAR(50) NOT NULL,
            image VARCHAR(255),
            correct_answer VARCHAR(255) NOT NULL,
            answer_a VARCHAR(255) NOT NULL,
            answer_b VARCHAR(255) NOT NULL,
            answer_c VARCHAR(255) NOT NULL,
            answer_d VARCHAR(255) NOT NULL
        )";

        if ($conn->query($sql) === FALSE) {
            die("Błąd tworzenia tabeli: " . $conn->error);
        }

        for ($i = 1; $i <= $numQuestions; $i++) {
            $question = $_POST['question_' . $i];
            $question_type = $_POST['question_type_' . $i];
            $image = $_FILES['image_' . $i]['name'];
            $correct_answer = $_POST['correct_answer_' . $i];
            switch ($correct_answer) {
                case 'A':
                    $correct_answer = $_POST['answer_a_' . $i];
                    break;
                case 'B':
                    $correct_answer = $_POST['answer_b_' . $i];
                    break;
                case 'C':
                    $correct_answer = $_POST['answer_c_' . $i];
                    break;
                case 'D':
                    $correct_answer = $_POST['answer_d_' . $i];
                    break;

            }
            $answer_a = $_POST['answer_a_' . $i];
            $answer_b = $_POST['answer_b_' . $i];
            $answer_c = $_POST['answer_c_' . $i];
            $answer_d = $_POST['answer_d_' . $i];

            $target_dir = "photos/";
            $target_file = $target_dir . basename($_FILES['image_' . $i]['name']);
            move_uploaded_file($_FILES['image_' . $i]['tmp_name'], $target_file);

            $sql = "INSERT INTO $table_name (question, type, image, correct_answer, answer_a, answer_b, answer_c, answer_d) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssss", $question, $question_type, $image, $correct_answer, $answer_a, $answer_b, $answer_c, $answer_d);
            $stmt->execute();
            $stmt->close();
        }

        $conn->close();
        $quiz_name = $_SESSION['quiz_title'];
        $author_login = $_SESSION['user']['login'];
        $question_count = $numQuestions;
        $creation_date = date('Y-m-d H:i:s');
        $execution_count = 0;

        require_once "connect.php";

        $conn = new mysqli($host, $db_user, $db_password, $db_name);
        if ($conn->connect_error) {
            die("Nie udało się połączyć z bazą danych: " . $conn->connect_error);
        }

        $sql = "INSERT INTO Quiz (quiz_name, question_count, author_login, creation_date, execution_count)
        VALUES ('$quiz_name', '$question_count', '$author_login', '$creation_date', '$execution_count')";

        if ($conn->query($sql) === TRUE) {
            echo "Rekord został dodany do tabeli Quiz.";
        } else {
            echo "Błąd podczas dodawania rekordu: " . $conn->error;
        }

        $conn->close();
        header("Location: sukces.php");
        exit();
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
        <form method="POST" enctype="multipart/form-data" class="max-w-2xl mx-auto bg-white p-8 rounded shadow">
            <?php for ($i = 1; $i <= $numQuestions; $i++): ?>
                <h3 class="text-xl font-bold mb-4">Pytanie <?php echo $i; ?>:</h3>

                <div class="mb-4">
                    <label class="block mb-2">Pytanie:</label>
                    <input type="text" name="question_<?php echo $i; ?>" required
                           class="w-full px-3 py-2 border rounded"/>
                </div>

                <div class="mb-4">
                    <label class="block mb-2">Typ pytania:</label>
                    <select name="question_type_<?php echo $i; ?>" required class="w-full px-3 py-2 border rounded">
                        <option value="text">Tekstowe</option>
                        <option value="image">Z obrazkiem</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-2">Obrazek:</label>
                    <input type="file" name="image_<?php echo $i; ?>" accept="image/*" class="w-full"/>
                </div>

                <div class="mb-4">
                    <label class="block mb-2">Odpowiedź poprawna:</label>
                    <div class="flex">
                        <label class="mr-4">
                            <input type="radio" name="correct_answer_<?php echo $i; ?>" value="A" required> A
                        </label>
                        <label class="mr-4">
                            <input type="radio" name="correct_answer_<?php echo $i; ?>" value="B" required> B
                        </label>
                        <label class="mr-4">
                            <input type="radio" name="correct_answer_<?php echo $i; ?>" value="C" required> C
                        </label>
                        <label class="mr-4">
                            <input type="radio" name="correct_answer_<?php echo $i; ?>" value="D" required> D
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block mb-2">Odpowiedź A:</label>
                    <input type="text" name="answer_a_<?php echo $i; ?>" required
                           class="w-full px-3 py-2 border rounded"/>
                </div>

                <div class="mb-4">
                    <label class="block mb-2">Odpowiedź B:</label>
                    <input type="text" name="answer_b_<?php echo $i; ?>" required
                           class="w-full px-3 py-2 border rounded"/>
                </div>

                <div class="mb-4">
                    <label class="block mb-2">Odpowiedź C:</label>
                    <input type="text" name="answer_c_<?php echo $i; ?>" required
                           class="w-full px-3 py-2 border rounded"/>
                </div>

                <div class="mb-4">
                    <label class="block mb-2">Odpowiedź D:</label>
                    <input type="text" name="answer_d_<?php echo $i; ?>" required
                           class="w-full px-3 py-2 border rounded"/>
                </div>
            <?php endfor; ?>

            <div class="text-center">
                <input type="submit" value="Zapisz quiz"
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"/>
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