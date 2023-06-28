<?php require_once "fordatabaseconnection.php";session_start();?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Zaloguj się</title>
    <meta charset="UTF-8"/>
    <?php
        if(!isset($_COOKIE['color-motive']) || trim($_COOKIE['color-motive']) == "" || $_COOKIE['color-motive'] === "forest"){
            echo "<link href='stylesheets/foxbookforeststyle.css' rel='stylesheet' type='text/css'/>";
        }else if(isset($_COOKIE['color-motive']) && $_COOKIE['color-motive'] === "arctic"){
            echo "<link href='stylesheets/foxbookarcticstyle.css' rel='stylesheet' type='text/css'/>";
        }else if(isset($_COOKIE['color-motive']) && $_COOKIE['color-motive'] === "night"){
            echo "<link href='stylesheets/foxbooknightstyle.css' rel='stylesheet' type='text/css'/>";
        }
    ?>
</head>
<body style="background-color: #184006">
<div class="header" id="header">
    <div id="headerText" style="font-family: Verdana"> <?php
        echo "<a href='foxbookhomepage.php' class='textLink' style='font-size: xxx-large;color: white'>FoxBook</a>"
        ?>
    </div>
    <div id="headerChooseMotive">
        <?php require "foxbookmotivescript.php"; ?>
    </div>
</div></br>
<div class="center" id="login-body">
    </br><h2>Logowanie</h2></br>
    <?php
    $usersArray = getArrayOfUsersIDs();
    if(in_array(@$_SESSION['loggedIn'],$usersArray)){
        echo "<h2 style='color: green;'>Zalogowałeś/aś się pomyślnie!</h2>";
        echo "<form action='foxbookhomepage.php'><input type='submit' value='Powrót do strony głównej' class='form-submit-button'></form>";
    }else{
        include_once "htmlfiles/foxbookloginform.html";
        if (isset($_POST['goBack'])) header("Location: foxbookhomepage.php");
        if (isset($_POST['signInButton'])){
            include_once "loginvalidation.php";
        }
    }
    ?>
</br>
</div>
<script>
    if( window.history.replaceState){
        window.history.replaceState(null, null, window.location.href);
    }
</script>
</body>
</html>