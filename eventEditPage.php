<?php require_once "fordatabaseconnection.php";session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Edycja wydarzeń</title>
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
    if(!in_array($_SESSION['loggedIn'],getBlockedUsers())) {
        echo "</br>
<form method='post' class='center'>
  <label for='eventName' style='font-size: x-large; font-family: Verdana'>Nazwa wydarzenia</label></br></br>
  <input type='text' name='eventName' id='eventName' placeholder='Wpisz nazwę wydarzenia (max 100 znaków)' class='form-inputs' value='$event->eventName'></br></br>
  <label for='eventDate' style='font-size: x-large; font-family: Verdana'>Podaj datę wydarzenia</label></br></br>
  <input type='datetime-local' name='eventDate' id='eventDate' class='form-inputs' value='" . $event->eventDate . "'></br></br>
  <label for='eventDescription' style='font-size: x-large; font-family: Verdana'>Opis wydarzenia</label></br></br>
  <textarea style='width: 500px; height: 200px; resize: none' name='eventDescription' id='eventDescription' placeholder='Opisz wydarzenie (max 500 znaków)'>$event->eventDescription</textarea></br></br>
  <input type='submit' name='editEventData' id='editEventData' value='Zatwierdź' class='form-submit-button'>
  &nbsp; <input type='submit' name='goBack' id='goBack' value='Anuluj' class='form-submit-button'>
  &nbsp; <input type='submit' name='deleteEvent' id='deleteEvent' value='Usuń wydarzenie' class='form-submit-button'>
</form>";
    }else echo "</br><h2 class='center'>Nie możesz edytować wydarzeń, będą zbanowanym/ą</h2><form method='post' action='foxbookhomepage.php' class='center'>
<input type='submit' value='Powrót na stronę domową' class='form-submit-button'></form>";
    if(isset($_POST['goBack'])){
        header("Location: foxbookgrouppage.php?groupID=$event->eventGroup");
    }
    if(isset($_POST['deleteEvent'])){
        deleteEvent($event->eventID);
        $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Wydarzenie zostało usunięte</p>";
        header("Location: foxbookgrouppage.php?groupID=$event->eventGroup");
    }
    if(isset($_POST['editEventData'])) {
        date_default_timezone_set("Europe/Warsaw");
        if(strlen($_POST['eventName']) <= 100 && trim($_POST['eventName']) != ""){
            if(date("d-m-Y-H-i-s",strtotime("+1 day")) < date("d-m-Y-H-i-s",strtotime($_POST['eventDate']))){
                if(trim($_POST['eventDescription']) != "" && strlen($_POST['eventDescription']) <= 500){
                    eventEdit($event->eventID,htmlspecialchars($_POST['eventName']),$_POST['eventDate'],htmlspecialchars($_POST['eventDescription']));
                    $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Wydarzenie zostało zmienione!</p>";
                    header("Location: foxbookgrouppage.php?groupID=$event->eventGroup");
                }else echo "<p class='wrong'>Opis nie może być pusty oraz musi być nie dłuższy niż 500 znaków</p>";
            }else echo "<p class='wrong'>Data musi być minimalnie ustawiona na następny dzień</p>";
        }else echo "<p class='wrong'>Musisz ustawić nazwę wydarzenia o maksymalnej długości 100 znaków</p>";
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