<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

// Подключение к БД
try {
    $db = new PDO('mysql:host=localhost;dbname=u68654', 'u68654', '1979564');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print('Ошибка подключения к БД: ' . $e->getMessage());
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = [];

    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        setcookie('login', '', 100000);
        setcookie('pass', '', 100000);
        $messages[] = 'Спасибо, результаты сохранены.';
        if (!empty($_COOKIE['login']) && !empty($_COOKIE['pass'])) {
            $messages[] = sprintf(
                'Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong> и паролем <strong>%s</strong>.',
                htmlspecialchars($_COOKIE['login']),
                htmlspecialchars($_COOKIE['pass'])
            );
        }
    }

    $fields = ['fio', 'email', 'dob', 'phone', 'bio'];
    $errors = [];
    $values = [];

    foreach ($fields as $field) {
        $errors[$field] = !empty($_COOKIE[$field . '_error']);
        $values[$field] = empty($_COOKIE[$field . '_value']) ? '' : htmlspecialchars($_COOKIE[$field . '_value']);
        setcookie($field . '_error', '', 100000);
    }

    if (!empty($_SESSION['login'])) {
        try {
            $stmt = $db->prepare("SELECT * FROM application WHERE login = ?");
            $stmt->execute([$_SESSION['login']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                foreach ($fields as $field) {
                    $values[$field] = htmlspecialchars($user[$field]);
                }
            }
        } catch (PDOException $e) {
            $messages[] = 'Ошибка при загрузке данных.';
        }
    }

    include('form.php');
    exit();
}

// POST: обработка формы
$fields = ['fio', 'email', 'dob', 'phone', 'bio'];
$errors = false;

foreach ($fields as $field) {
    if (empty($_POST[$field]) || !preg_match('/^[^<>{}]+$/u', $_POST[$field])) {
        setcookie($field . '_error', '1', time() + 24 * 60 * 60);
        $errors = true;
    } else {
        setcookie($field . '_value', $_POST[$field], time() + 30 * 24 * 60 * 60);
    }
}

if ($errors) {
    header('Location: index.php');
    exit();
} else {
    foreach ($fields as $field) {
        setcookie($field . '_error', '', 100000);
    }
}

if (!empty($_SESSION['login'])) {
    try {
        $stmt = $db->prepare("UPDATE application SET fio=?, email=?, dob=?, phone=?, bio=? WHERE login=?");
        $stmt->execute([
            $_POST['fio'],
            $_POST['email'],
            $_POST['dob'],
            $_POST['phone'],
            $_POST['bio'],
            $_SESSION['login']
        ]);
    } catch (PDOException $e) {
        print('Ошибка при обновлении: ' . $e->getMessage());
        exit();
    }
} else {
    $login = 'u' . substr(uniqid(), -5);
    $pass = substr(md5(rand()), 0, 8);
    $pass_hash = md5($pass);

    setcookie('login', $login);
    setcookie('pass', $pass);

    try {
        $stmt = $db->prepare("INSERT INTO application (fio, email, dob, phone, bio, login, pass_hash)
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['fio'],
            $_POST['email'],
            $_POST['dob'],
            $_POST['phone'],
            $_POST['bio'],
            $login,
            $pass_hash
        ]);
    } catch (PDOException $e) {
        print('Ошибка при сохранении: ' . $e->getMessage());
        exit();
    }
}

setcookie('save', '1');
header('Location: index.php');
