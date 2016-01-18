<?php
require_once("config/dbconf.php");
session_start();

$recup = recupSave($pdo);

if(empty($_SESSION['choice']) || isset($_POST['reset'])){
    $choice  =  rand(0,100);
    $_SESSION['score'] = 0;
    $_SESSION['choice'] = $choice;
    $_SESSION['response'] = null;
    saveGame($pdo);
}else{
    $choice = $_SESSION['choice'];
    $_SESSION['score'] = $recup['score'];
    $_SESSION['response'] = $recup['response'];
}

if(!isset($_SESSION['user'])){
    saveGame($pdo);
    header("Location: /login.php");
    exit;
}

if(isset($_POST['reset_best'])){
    unset($_SESSION['best_score']);
    updateBestScore($pdo);
}

displayLeaderboard($pdo);

if(empty($_POST['guess'])){
    if( $_SESSION['response'] == null) {
        $_SESSION['response'] = 'Pas de nombre';
    }
}else{
    $guess = $_POST['guess'];
    $_SESSION['score']++;
    if($guess > $choice) {
        $_SESSION['response'] = "C'est moins";
    }elseif($guess < $choice){
        $_SESSION['response'] = "C'est plus";
    }else{
        $_SESSION['response'] = "C'est gagné";
        if( !isset($_SESSION['best_score'])
            || $_SESSION['best_score'] > $_SESSION['score']){
            $_SESSION['best_score'] = $_SESSION['score'];
            updateBestScore($pdo);
        }
        unset($_SESSION['choice']);
    }
    saveGame($pdo);
}

function displayLeaderboard($pdo){
    $stmt = $pdo->prepare("SELECT login, best_score from user ORDER BY `best_score` LIMIT 0,10");
    $stmt->execute();

    echo('<table border="1px">');
    echo('<th>name</th><th>Score</th>');
    while($result = $stmt->fetch()){
        echo('<tr>'. '<td>' . $result['login'].'</td>'. '<td>' .$result['best_score'].'</td>' . '</tr>');
    }
    echo('</table>');
}

function updateBestScore($pdo){
    $stmt = $pdo->prepare("UPDATE user SET `best_score` = :best where id = :id");
    $stmt->bindParam("best",$_SESSION['best_score']);
    $stmt->bindParam("id",$_SESSION['id']);
    $stmt->execute();
    $result = $stmt->fetch();
}

function saveGame($pdo){
    $stmt = $pdo->prepare("UPDATE user SET `score` = :score where id = :id");
    $stmt->bindParam("score", $_SESSION['score']);
    $stmt->bindParam("id", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->fetch();
    $stmt = $pdo->prepare("UPDATE user SET `choice` = :choice where id = :id");
    $stmt->bindParam("choice", $_SESSION['choice']);
    $stmt->bindParam("id", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->fetch();
    $stmt = $pdo->prepare("UPDATE user SET `response` = :response where id = :id");
    $stmt->bindParam("response", $_SESSION['response']);
    $stmt->bindParam("id", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->fetch();
    $stmt = $pdo->prepare("UPDATE user SET `guess` = :guess where id = :id");
    $stmt->bindParam("guess", $_POST['guess']);
    $stmt->bindParam("id", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->fetch();
}

function recupSave($pdo){
    $stmt = $pdo->prepare("SELECT score, choice, response, guess FROM `user` WHERE id = :id");
    $stmt->bindParam("id", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Des papiers dans un bol </title>
</head>
<body>

<?php echo $_SESSION['response'];?> <br>
Nombre de coup : <?php echo $_SESSION['score']; ?><br>
<em>[Meilleur score
    <?php
    echo !isset($_SESSION['best_score'])
        ? ":Pas de meilleur score"
        :" pour " . $_SESSION['user'] . ":  " . $_SESSION['best_score'];
    ?>]</em>
<form method="POST">
    <input type="text" name="guess" autofocus>
    <input type="submit">
    <input type="submit" name="reset" value="reset">
    <input type="submit" name="reset_best" value="reset best">
</form>
<em>Tu as testé avec <?php echo $recup['guess']?>)</em><br>
<em>(La réponse est <?php echo $choice?>)</em>

<form method="POST" action="/login.php">
    <input type="submit" name="logout" value="Logout">
</form>

</body>
</html>