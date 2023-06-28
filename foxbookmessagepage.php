<?php require_once "fordatabaseconnection.php";session_start();ob_start();?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Wyślij wiadomość</title>
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
<body>
<div class="header" id="header">
    <div id="headerText"> <?php
        echo "<a href='foxbookhomepage.php' class='textLink' style='font-size: xxx-large;color: white'>FoxBook</a>"
        ?>
    </div>
    <div id="headerChooseMotive">
        <?php require "foxbookmotivescript.php"; ?>
    </div>
    <div id="headerForm">
        <?php require_once "mainscriptfoxbook.php";?>
    </div>
</div></br>
<div id="center-body">
<?php require_once "htmlfiles/messageFile.html";
if(isset($_POST['cancel'])){
    header("Location: foxbookhomepage.php");
    die();
}
if(isset($_POST['sendMessage'])){
    if(strlen($_POST['messageContents']) <= 255 && trim($_POST['messageContents']) != ""){
        sendMessage($_SESSION['loggedIn'],$_GET['toUserID'],htmlspecialchars($_POST['messageContents']));
        header("Location: foxbookhomepage.php");
        die();
    }else echo "<p class='wrong'>Wiadomość nie spełnia wymagań, nie udało się wysłać</p>";
}
?>
</div>
<script>
    if( window.history.replaceState){
        window.history.replaceState(null, null, window.location.href);
    }
</script>
</body>
</html>
