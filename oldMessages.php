<?php require_once "fordatabaseconnection.php";session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Dodaj znajomych</title>
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
<div id="center-body" style="overflow: hidden auto">
    <?php
    echo "</br>";
    echo "<form action='' method='post' class='center'>
<input type='submit' name='goBack' value='Powrót' class='form-submit-button'>&nbsp;";
    if(isset($_COOKIE['yourOwnMessages'])){
        echo "<input type='submit' name='seeMessagesForYou' value='Przejrzyj stare wiadomości do Ciebie' class='form-submit-button'>";
    }else{
        echo "<input type='submit' name='seeYourMessages' value='Przejrzyj swoje stare wiadomości' class='form-submit-button'>";
    }
    echo "</form>";
    echo "</br>";
    if(isset($_COOKIE['yourOwnMessages'])){
        $messagesArray = array_reverse(getReadMessagesFromYou($_SESSION['loggedIn']));
    }else{
        $messagesArray = array_reverse(getReadMessagesFromSomeone($_SESSION['loggedIn']));
    }
    $callOldMessages = function ($object){
        $user = getUserObjectFromID($object->fromWho);
        $message = $object->messageContents;
        $messageDate = date("d.m.Y H:i",strtotime($object->dateOfCreation));
        echo "<fieldset id='message-space'><p style='text-shadow: none' id='notification-line'><span style='margin-bottom: 2px;color: dimgrey'>$messageDate</span></br>Wiadomość od użytkownika $user->userFirstName:</p> 
                   <p style='' id='message-line' >$message</p></fieldset>";
    };
    array_walk($messagesArray,$callOldMessages);
    if(isset($_POST['seeYourMessages'])){
        setcookie("yourOwnMessages",true);
        header("Location: oldMessages.php");
        die();
    }
    if(isset($_POST['seeMessagesForYou'])){
        setcookie('yourOwnMessages',null);
        header("Location: oldMessages.php");
        die();
    }
    if(isset($_POST['goBack'])){
        header("Location: foxbooknotificationmanage.php");
        die();
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