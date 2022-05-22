<?php
$user = 'u47554';
$pass = '6645271';
header('Content-Type: text/html; charset=UTF-8');

//var_dump(md5('qoqjppyZsfWTVOSsNdkx'));

function getUserId($login){
    $user = 'u47554';
    $pass = '6645271';
    $db = new PDO('mysql:host=localhost;dbname=u47554', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
    try {
        $get_id = $db->prepare("SELECT user_id FROM login WHERE login=:login");
        $db->beginTransaction();
        $get_id->execute(array("login" => $login));
        $id = (current(current($get_id->fetchAll(PDO::FETCH_ASSOC))));
        $db->commit();
    }
    catch(PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    return $id;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = array();
    if (empty($_COOKIE['sex_value'])) $_COOKIE['sex_value'] = 1;
    if (empty($_COOKIE['limb_value'])) $_COOKIE['limb_value'] = 2;
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        setcookie('login', '', 100000);
        setcookie('pass', '', 100000);
        $messages[] = '<div class="alert alert-secondary" role="alert">Спасибо, результаты сохранены</div>';
        if (!empty($_COOKIE['pass'])) {
            $messages[] = sprintf('
<div class="alert alert-secondary" role="alert">Вы можете <a href="login.php"><button class="btn btn-secondary">войти</button></a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.</div>',
                strip_tags($_COOKIE['login']),
                strip_tags($_COOKIE['pass']));
        }
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
    $values['fio_value'] = empty($_COOKIE['fio_value']) || !empty($_SESSION['is_admin']) ? '' : $_COOKIE['fio_value'];
    $values['email_value'] = empty($_COOKIE['email_value']) || !empty($_SESSION['is_admin']) ? '' : $_COOKIE['email_value'];
    $values['year_value'] = empty($_COOKIE['year_value']) || !empty($_SESSION['is_admin']) ? '' : $_COOKIE['year_value'];
    $values['bio_value'] = empty($_COOKIE['bio_value']) || !empty($_SESSION['is_admin']) ? '' : $_COOKIE['bio_value'];
    $values['sex_value'] = empty($_COOKIE['sex_value']) || !empty($_SESSION['is_admin']) ? '' : $_COOKIE['sex_value'];
    $values['limb_value'] = empty($_COOKIE['limb_value']) || !empty($_SESSION['is_admin']) ? '' : $_COOKIE['limb_value'];

    for($i=0; $i<4; $i++){
        $values['ability'.$i] = empty($_COOKIE['ability'.$i]) ? '' : ($_COOKIE['ability'.$i]);
    }

    $check = true;
    foreach($errors as $error){
        if($error){
            $check = false;
        }
    }

    if (!isset($_SESSION)) { session_start(); }
    if ($check && !empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])) {
        $db = new PDO('mysql:host=localhost;dbname=u47554', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
        $id = getUserId($_SESSION['login']);
        try {
            $stmt = $db->prepare("SELECT * FROM users WHERE id=:id");
            $result = $stmt->execute(array("id"=>$id));
            $data = current($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        catch(PDOException $e) {
            print('Error : ' . $e->getMessage());
            exit();
        }

        $values['fio_value'] = filter_var($data['name'],  FILTER_SANITIZE_SPECIAL_CHARS);
        $values['email_value'] = filter_var($data['email'], FILTER_SANITIZE_SPECIAL_CHARS);
        $values['year_value'] = filter_var($data['year'],  FILTER_SANITIZE_SPECIAL_CHARS);
        $values['sex_value'] = $data['sex'];
        $values['limb_value'] = $data['limb'];
        $values['bio_value'] = filter_var($data['bio'], FILTER_SANITIZE_SPECIAL_CHARS);

        try {
            $stmt = $db->prepare("SELECT * FROM abilities WHERE user_id=:id");
            $result = $stmt->execute(array("id"=>$id));
            $data = current($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        catch(PDOException $e) {
            print('Error : ' . $e->getMessage());
            exit();
        }

        $ability_data = ['immort', 'wall', 'levit', 'invis'];
        for($i=0; $i<4; $i++){
            $values['ability'.$i] = $data[$ability_data[$i]];
        }

        if(empty($_SESSION['is_admin'])) {
            printf('<div class="alert alert-secondary" role="alert">Вход с логином %s', $_SESSION['login']);
        } else {
            printf('<div class="alert alert-secondary" role="alert">Изменение данных пользователя с логином %s', $_SESSION['login']);
        }
        printf('</div>');
    }
    include('form.php');
}
else if (session_start() && isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] == $_POST['csrf_token']){

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
    }

    $ability_insert = [];
    foreach ($ability_data as $ability) {
        $ability_insert[$ability] = in_array($ability, $abilities) ? 1 : 0;
    }

    if($errors){
        header('Location: index.php');
        exit();
    }
    else {
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

    if (!isset($_SESSION)) { session_start(); }

    if (!empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])) {
        $db = new PDO('mysql:host=localhost;dbname=u47554', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
        try {
            $id = getUserId($_SESSION['login']);
            $second_stmt = $db->prepare("UPDATE users SET name=:name, year=:year, sex=:sex, email=:email, bio=:bio, limb=:limb WHERE id =:id");
            $second_stmt -> execute(array("name" => $_POST['fio'], "year" => $_POST['year'], "sex" => $_POST['sex'], "email" => $_POST['email'], "bio"=>$_POST['text'], "limb"=>$_POST['limb'], "id"=>$id));
            $third_stmt = $db->prepare("UPDATE abilities SET immort=:immort, wall=:wall, levit=:levit, invis=:invis WHERE user_id=:id");
            $third_stmt->execute(array("immort" => $ability_insert['immort'], "wall" => $ability_insert['wall'], "levit" => $ability_insert['levit'], "invis" => $ability_insert['invis'], "id" => $id));

        }
        catch(PDOException $e) {
            print('Error : ' . $e->getMessage());
            exit();
        }
    }
    else{
        $login = uniqid("user");
        $pwd = rand(10000000,100000000);
        setcookie('login', $login);
        setcookie('pass', $pwd);

        try {
            $db = new PDO('mysql:host=localhost;dbname=u47554', $user, $pass);
            $first_stmt = $db->prepare("INSERT INTO users (name,year,sex,email,bio,limb) VALUES (?,?,?,?,?,?)");

            try{
                $db->beginTransaction();
                $first_stmt->execute(array($_POST['fio'], $_POST['year'], $_POST['sex'], $_POST['email'], $_POST['text'], $_POST['limb']));
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
            try {
                $third_stmt = $db->prepare("INSERT INTO login (user_id, login, pwd) VALUES (?,?,?)");
                $db->beginTransaction();
                $third_stmt->execute(array($id, $login, password_hash($pwd, PASSWORD_DEFAULT)));
                $db->commit();
            } catch (PDOException $exception){
                print "Error: " . $exception->getMessage() . "</br>";
            }
        }
        catch(PDOException $e) {
            print('Error : ' . $e->getMessage());
            exit();
        }
    }


    if (!isset($_SESSION)) { session_start(); }

    if(!empty($_SESSION['is_admin'])){
        header('Location: admin.php');
        exit();
    } else {
        setcookie('save', '1');
        header('Location: index.php');
    }
}

