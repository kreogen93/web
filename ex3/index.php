<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['save'])) {
        print('Спасибо, результаты сохранены.');
    }
    include('form.php');
    exit();
}

$errors = FALSE;

//accept
if (empty($_POST['accept'])) {
    print("Вы не приняли соглашение!<br>");
    $errors = TRUE;
}

//fio
if (empty($_POST['fio'])) {
    print('Заполните имя.<br>');
    $errors = TRUE;
}
else if (!preg_match("/^[а-яА-Яa-zA-Z ]+$/u", $_POST['fio'])) {
    print('Недопустимые символы в имени.<br>');
    $errors = TRUE;
}

//email
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    print('Проверьте правильность ввода email<br>');
    $errors = TRUE;
}

//year
if (empty($_POST['year'])) {
    print('Заполните год.<br>');
    $errors = TRUE;
}
else {
    $year = $_POST['year'];
    if (!preg_match("/(0?[1-9]|[12][0-9]|3[01])[\/\-\.](0?[1-9]|1[012])[ \/\.\-]/", $year)) {
        print("Укажите корректный год.<br>");
        $errors = TRUE;
    }
}

//abilities
$ability_data = ['immort', 'wall', 'levit', 'invis'];
if (empty($_POST['abilities'])) {
    print('Выберите способность<br>');
    $errors = TRUE;
}
else {
    $abilities = $_POST['abilities'];
    foreach ($abilities as $ability) {
        if (!in_array($ability, $ability_data)) {
            print('Недопустимая способность<br>');
            $errors = TRUE;
        }
    }
}
$ability_insert = [];
foreach ($ability_data as $ability) {
    $ability_insert[$ability] = in_array($ability, $abilities) ? 1 : 0;
}

if ($errors) {
    exit();
}

$user = 'u47554';
$pass = '6645271';

try {
    $db = new PDO('mysql:host=localhost;dbname=u47554', $user, $pass);
    $first_stmt = $db->prepare("INSERT INTO users (name,year,sex,email,bio,limb) VALUES (?,?,?,?,?,?)");

    try{
        $db->beginTransaction();
        $first_stmt->execute(array($_POST['fio'], $year, $_POST['sex'], $_POST['email'], $_POST['text'], $_POST['limb']));
        $id = $db->lastInsertId();
        $db->commit();
    } catch (PDOException $exception){
        print "Error: " . $exception->getMessage() . "</br>";
    }

    $second_stmt = $db->prepare("INSERT INTO abilities (user_id,immort,wall,levit,invis) VALUES (?,?,?,?,?)");
    try{
        $db->beginTransaction();
        $second_stmt->execute(array($id,$ability_insert['immort'],$ability_insert['wall'],$ability_insert['levit'],$ability_insert['invis']));
        $db->commit();
    } catch (PDOException $exception){
        print "Error: " . $exception->getMessage() . "</br>";
    }

}
catch(PDOException $e) {
    print('Error : ' . $e->getMessage());
    exit();
}

header('Location: ?save=1');
