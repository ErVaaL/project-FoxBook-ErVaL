<?php require_once "fordatabaseconnection.php";session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Szczegóły wydarzenia</title>
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
    <?php
    $event = getEventObjectFromID($_GET['eventID']);
    echo"<form method='post' action='foxbookgrouppage.php?groupID=$event->eventGroup' style='float:right; margin-right: 1%;margin-top: 1%'><input type='submit' value='Powrót' class='form-submit-button'></form>";
    if(in_array($_SESSION['loggedIn'],getGroupMembers($event->eventGroup))){
        $eventParticipants = getEventParticipants($event->eventID);
        echo "<div id='eventParticipants'></br><fieldset id='eventParticipantsSpace'>
                <legend class='center' style='font-size: x-large;'>Członkowie wydarzenia:</legend>";
        $callParticipants = function ($key){
            $event = getEventObjectFromID($_GET['eventID']);
            $user = getUserObjectFromID($key);
            $profID = getUserProfileIDFromID($key);
            echo "</br><a class='textLink' href='foxbookprofilepage.php?profileID=$profID' style=''>$user->userFirstName&nbsp;$user->userLastName</a>";
            if($_SESSION['loggedIn'] == $event->eventCreator){
                echo "<form method='post' style='float: right'><input type='submit' name='removeUserFromEvent[$key]' id='removeUserFromEvent' value='Usuń z wydarzenia' class='form-submit-button'></form>";
            }
        };
        array_walk($eventParticipants,$callParticipants);
        echo "</fieldset></div></br>";
        echo "<div id='eventDescriptionInDetails'>
        <fieldset id='eventParticipantsSpace'>
        <legend class='center' style='font-size: x-large;'>Opis wydarzenia:</legend>
        $event->eventDescription
</fieldset>
</div>";
        if(isset($_POST['removeUserFromEvent'])) {
            kickUserFromEvent($event->eventID, key($_POST['removeUserFromEvent']));
            sendNotification($event->eventCreator, key($_POST['removeUserFromEvent']), "Zostałeś wyrzucony z wydarzenia");
            $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Użytkownik został wyrzucony z wydarzenia</p>";
            header("Location: foxbookeventdetails.php?eventID=$event->eventID");
            die();
        }
    }else{
        echo"<h2>Nie należysz do grupy tego wydarzenia, nie możesz przejżeć jego szczegółów</h2>";
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