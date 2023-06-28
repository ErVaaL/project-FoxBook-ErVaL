<?php session_start();?>
<!DOCTYPE html>
<html>
<head>
    <title>Zarejestruj się</title>
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
    </br><h2>Rejestracja</h2></br>
    <?php
    if(@!$_SESSION['justRegistered']){
        include_once "htmlfiles/foxbookregisterform.html";
        if(isset($_POST['goBack'])) header("Location: foxbookhomepage.php");
        if(isset($_POST['register'])){
            include_once "registervalidation.php";
        }
    }else{
        echo "<h2 style='color: green'>Zarejestrowałeś/aś się, możesz się teraz zalogować</h2>";
        echo "<form method='post' action='foxbookloginpage.php'>
            <input type='submit' value='Logowanie' class='form-submit-button'>
        </form>";
        unset($_SESSION['justRegistered']);
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