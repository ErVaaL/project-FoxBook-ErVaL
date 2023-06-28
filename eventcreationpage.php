<?php require_once "fordatabaseconnection.php";session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Tworzenie wydarzeń</title>
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
    $group = getGroupFromID($_GET['groupID']);
    include_once "htmlfiles/eventCreationForm.html";
    if(isset($_POST['goBack'])){
        header("Location: foxbookgrouppage.php?groupID=$group->groupID");
        die();
    }
    if(isset($_POST['createEvent'])) {
        date_default_timezone_set("Europe/Warsaw");
        if(strlen($_POST['eventName']) <= 100 && trim($_POST['eventName']) != ""){
            if(date("Y-m-d H:i:s",strtotime("+1 day")) < date("Y-m-d H:i:s",strtotime($_POST['eventDate']))){
                if(trim($_POST['eventDescription']) != "" && strlen($_POST['eventDescription']) <= 500){
                    createEvent($group->groupID,$_SESSION['loggedIn'],htmlspecialchars($_POST['eventName']),$_POST['eventDate'],htmlspecialchars($_POST['eventDescription']));
                    $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Wydarzenie zostało utworzone!</p>";
                    header("Location: foxbookgrouppage.php?groupID=$group->groupID");
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