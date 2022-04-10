<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = array();
    if (empty($_COOKIE['sex_value'])) $_COOKIE['sex_value'] = 1;
    if (empty($_COOKIE['limb_value'])) $_COOKIE['limb_value'] = 2;
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        $messages[] = 'Спасибо, результаты сохранены.';
    }
    $errors = array();
    $errors['fio_empty'] = !empty($_COOKIE['fio_empty']);
    $errors['fio_error'] = !empty($_COOKIE['fio_error']);
    $errors['email_error'] = !empty($_COOKIE['email_error']);
    $errors['sex_empty'] = !empty($_COOKIE['sex_empty']);
    $errors['year_empty'] = !empty($_COOKIE['year_empty']);
    $errors['year_error'] = !empty($_COOKIE['year_error']);
    $errors['limb_empty'] = !empty($_COOKIE['limb_empty']);
    $errors['abilities_empty'] = !empty($_COOKIE['abilities_empty']);
    $errors['abilities_error'] = !empty($_COOKIE['abilities_error']);
    $errors['accept_error'] = !empty($_COOKIE['accept_error']);

    if ($errors['fio_empty']) {
        setcookie('fio_empty', '', 100000);
        $messages[] = '<div class="error">Заполните имя.</div>';
    }
    if ($errors['fio_error']) {
        setcookie('fio_error', '', 100000);
        $messages[] = '<div class="error">Используйте в имени символы a-z,A-Z,а-я,А-Я.</div>';
    }
    if ($errors['email_error']) {
        setcookie('email_error', '', 100000);
        $messages[] = '<div class="error">Ошибка при заполнении email.</div>';
    }
    if ($errors['year_empty']) {
        setcookie('year_empty', '', 100000);
        $messages[] = '<div class="error">Заполните год рождения.</div>';
    }
    if ($errors['sex_empty']) {
        setcookie('sex_empty', '', 100000);
        $messages[] = '<div class="error">Выберите пол.</div>';
    }
    if ($errors['year_error']) {
        setcookie('year_error', '', 100000);
        $messages[] = '<div class="error">Некорректные данные в поле: дата рождения.</div>';
    }
    if ($errors['limb_empty']) {
        setcookie('limb_empty', '', 100000);
        $messages[] = '<div class="error">Выберите число конечностей.</div>';
    }
    if ($errors['abilities_empty']) {
        setcookie('abilities_empty', '', 100000);
        $messages[] = '<div class="error">Не выбраны способности.</div>';
    }
    if ($errors['abilities_error']) {
        setcookie('abilities_error', '', 100000);
        $messages[] = '<div class="error">Некорректные данные в поле: способности.</div>';
    }
    if ($errors['accept_error']) {
        setcookie('accept_error', '', 100000);
        $messages[] = '<div class="error">Вы не согласились.</div>';
    }

    $values = array();
    $values['fio_value'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
    $values['email_value'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
    $values['year_value'] = empty($_COOKIE['year_value']) ? '' : $_COOKIE['year_value'];
    $values['bio_value'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
    $values['sex_value'] = empty($_COOKIE['sex_value']) ? '' : $_COOKIE['sex_value'];
    $values['limb_value'] = empty($_COOKIE['limb_value']) ? '' : $_COOKIE['limb_value'];

    for($i=0; $i<4; $i++){
        $values['ability'.$i] = empty($_COOKIE['ability'.$i]) ? '' : ($_COOKIE['ability'.$i]);
    }

    include('form.php');
}
else{
    $errors = FALSE;

    if (empty($_POST['fio'])) {
        setcookie('fio_empty', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else if (!preg_match("/^[а-яА-Яa-zA-Z ]+$/u", $_POST['fio'])) {
        setcookie('fio_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('fio_value', $_POST['fio'], time() + 30 * 24 * 60 * 60);
    }

    //email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        setcookie('email_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else{
        setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);
    }

    //year
    if (empty($_POST['year'])) {
        setcookie('year_empty', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else {
        $year = $_POST['year'];
        if (!preg_match("/(0?[1-9]|[12][0-9]|3[01])[\/\-\.](0?[1-9]|1[012])[ \/\.\-]/", $year)) {
            setcookie('year_error', '1', time() + 24 * 60 * 60);
            //print("Укажите корректный год.<br>");
            $errors = TRUE;
        }
        else{
            setcookie('year_value', $_POST['year'], time() + 30 * 24 * 60 * 60);
        }
    }

    //abilities
    $ability_data = ['immort', 'wall', 'levit', 'invis'];
    if (empty($_POST['abilities'])) {
        setcookie('abilities_empty', '1', time() + 24 * 60 * 60);
        //print('Выберите способность<br>');
        $errors = TRUE;
    }
    else {
        $abilities = $_POST['abilities'];
        foreach ($abilities as $ability) {
            if (!in_array($ability, $ability_data)) {
                setcookie('abilities_error', '1', time() + 24 * 60 * 60);
                $errors = TRUE;
            }
        }
        if(!$errors){
            $ability_insert = [];
            $i=0;
            foreach ($ability_data as $ability) {
                $ability_insert[$ability] = in_array($ability, $abilities) ? 1 : 0;
                setcookie('ability'.$i, $ability_insert[$ability], time() + 30 * 24 * 60 * 60);
                $i++;
            }
        }
    }

    if (!$errors) {
        setcookie('sex_value', $_POST['sex'], time() + 30 * 24 * 60 * 60);
        setcookie('bio_value', $_POST['text'], time() + 30 * 24 * 60 * 60);
        setcookie('limb_value', $_POST['limb'], time() + 30 * 24 * 60 * 60);
        setcookie('accept_value', $_POST['accept'], time() + 30 * 24 * 60 * 60);
    }

    if ($errors) {
        header('Location: index.php');
        exit();
    }
    else{
        setcookie('fio_empty', '', 100000);
        setcookie('fio_error', '', 100000);
        setcookie('email_error', '', 100000);
        setcookie('year_empty', '', 100000);
        setcookie('year_error', '', 100000);
        setcookie('sex_error', '', 100000);
        setcookie('limb_empty', '', 100000);
        setcookie('abilities_empty', '', 100000);
        setcookie('abilities_error', '', 100000);
        setcookie('accept_error', '', 100000);
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

    setcookie('save', '1');
    header('Location: index.php');
}

