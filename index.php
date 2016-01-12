<?php
    session_start();

    $name = "toto";
    $pass = "toto42";
    $log = false;
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=toto;charset=utf8', 'root', '');
    }
    catch (Exception $e)
    {
        die('Erreur : ' . $e->getMessage());
    }

    $sql = $bdd->query("SELECT login FROM `user` WHERE login = \'toto\' AND password = \'toto42\'");

    $donnees = $sql->fetch();

    echo($sql['name']);

    //$sql->closeCursor();

    if(!isset($_POST['name']) || !isset($_POST['password']))
    {
    echo('bad login');
    }else{
        if($_POST['password'] == $pass && $_POST['name'] == $name){
            echo('log ok');
            $_SESSION['log'] = 'ok';
            $log = true;
            header('Location: plusoumoins.php');
            exit();
        }else{
            echo("bad login");
        }
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
<form method="post">
    <h4>Login</h4>
    <input type="text" name="name">
    <h4>Mdp</h4>
    <input type="password" name="password">
    <input type="submit"><br>
    <input type='hidden' name='log' value='true'>
</form>

</body>
</html>