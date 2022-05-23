

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Lab 7</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>

<body>

<?php

    $user='u47554';
    $pass='6645271';
    $db = new PDO('mysql:host=localhost;dbname=u47554', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

    try {
        $stmt = $db->prepare("SELECT password FROM admin WHERE login=:login");
        $stmt->execute(array("login"=>$_SERVER['PHP_AUTH_USER']));
        $password = current(current($stmt->fetchAll(PDO::FETCH_ASSOC)));
    }
    catch(PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }

    if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) || md5($_SERVER['PHP_AUTH_PW']) != $password) {
        header('HTTP/1.1 401 Unanthorized');
        header('WWW-Authenticate: Basic realm="My site"');
        echo '<h1>401 Требуется авторизация</h1>';
        exit();
    }

    session_start();
    $_SESSION['is_admin']=true;

    echo '<h1 class="text-center m-5">Вы успешно авторизовались и видите защищенные паролем данные.</h1>';

    $first_stmt = $db->prepare("SELECT * FROM users");
    try {
        $first_stmt->execute();
        $data = $first_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    $second_stmt = $db->prepare("SELECT * FROM abilities");
    try {
        $second_stmt->execute();
        $abilities = $second_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }

    $value = 1;
    $get_immort_amount = $db->prepare("SELECT count(*) FROM abilities WHERE immort=:value");
    try {
        $get_immort_amount->execute(array('value'=>$value));
        $immort_amount = current(current($get_immort_amount->fetchAll(PDO::FETCH_ASSOC)));
    } catch(PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    $get_wall_amount = $db->prepare("SELECT count(*) FROM abilities WHERE wall=:value");
    try {
        $get_wall_amount->execute(array('value'=>$value));
        $wall_amount = current(current($get_wall_amount->fetchAll(PDO::FETCH_ASSOC)));
    } catch(PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    $get_levit_amount = $db->prepare("SELECT count(*) FROM abilities WHERE levit=:value");
    try {
        $get_levit_amount->execute(array('value'=>$value));
        $levit_amount = current(current($get_levit_amount->fetchAll(PDO::FETCH_ASSOC)));
    } catch(PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    $get_invis_amount = $db->prepare("SELECT count(*) FROM abilities WHERE invis=:value");
    try {
        $get_invis_amount->execute(array('value'=>$value));
        $invis_amount = current(current($get_invis_amount->fetchAll(PDO::FETCH_ASSOC)));
    } catch(PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
?>
    <div class="container-fluid">
        <h4 class="text-center m-5">
            Superpowers stats:
        </h4>
        <table class="table">
            <thead>
            <tr>
                <th class="col">Immortality</th>
                <th class="col">Passing Through Walls</th>
                <th class="col">Levitation</th>
                <th class="col">Invisibility</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th class="col"><?php echo $immort_amount;?></th>
                <th class="col"><?php echo $wall_amount;?></th>
                <th class="col"><?php echo $levit_amount;?></th>
                <th class="col"><?php echo $invis_amount;?></th>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="container-fluid">
        <h4 class="text-center m-5">
            Users data:
        </h4>
        <table class="table">
            <thead>
                <tr>
                    <th class="col">ID</th>
                    <th class="col">Name</th>
                    <th class="col">Year</th>
                    <th class="col">Sex</th>
                    <th class="col">Email</th>
                    <th class="col">Bio</th>
                    <th class="col">Limb</th>
                    <th class="col">Change</th>
                    <th class="col">Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php
                foreach($data as $user_data){
                    echo '<tr>';
                    foreach ($user_data as $item){
                        echo '<th>';
                        echo filter_var($item,FILTER_SANITIZE_SPECIAL_CHARS);
                        echo '</th>';
                    }
                    echo '<th>';
                    echo '<form method="POST"><input class="btn btn-light" type="submit" name="change'.$user_data["id"].'" value="Change"/></form>';
                    echo '</th>';
                    echo '<th>';
                    echo '<form method="POST"><input class="btn btn-light" type="submit" name="delete'.$user_data["id"].'" value="Delete"/></form>';
                    echo '</th>';
                    echo '</tr>';
                }
            ?>
    </div>
</body>

<?php

    foreach ($data as $user_data){
        if(isset($_POST['change'.$user_data['id']])){
            $stmt = $db->prepare("SELECT login FROM login WHERE user_id=:id");
            try {
                $stmt->execute(array('id'=>$user_data['id']));
                $login = current(current($stmt->fetchAll(PDO::FETCH_ASSOC)));
            } catch(PDOException $e) {
                print('Error : ' . $e->getMessage());
                exit();
            }
            $_SESSION['login'] = $login;
            $_SESSION['uid'] = $user_data['id'];
            header('Location: index.php');
            exit();
        }
    }
    foreach ($data as $user_data){
        if(isset($_POST['delete'.$user_data['id']])){
            $stmt = $db->prepare("DELETE FROM users WHERE id=:id");
            try {
                $stmt->execute(array('id'=>$user_data['id']));
            } catch(PDOException $e) {
                print('Error : ' . $e->getMessage());
                exit();
            }
            header('Location: admin.php');
            exit();
        }
    }

?>

