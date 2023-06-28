<?php require_once "fordatabaseconnection.php";session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Zgłaszanie użytkownika</title>
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
    $usersArray = getArrayOfUsersIDs();
    $profileID= getUserProfileIDFromID($_GET['userID']);
    if(in_array($_SESSION['loggedIn'], $usersArray)){
        if(checkIfUserReportedUser($_SESSION['loggedIn'],$_GET['userID'])){
            $possibleReports = array("Toksyczne zachowanie","Oszustwo/Podszywanie się","Przemoc słowna","Udostępnianie nieodpowiednich treści");
            require_once "htmlfiles/reportForm.html";
            if(isset($_POST['cancelReport'])){
                header("Location: foxbookprofilepage.php?profileID=$profileID");
            }
            if(isset($_POST['sendReport'])){
                if(in_array($_POST['reportType'],$possibleReports)){
                    date_default_timezone_set("Europe/Warsaw");
                    $date = date("Y-m-d H:i:s");
                    reportUser($_GET['userID'],$_SESSION['loggedIn'],$date,$_POST['reportType'],htmlspecialchars($_POST['reportDescription']));
                    $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Zgłoszenie zostało wysłane!</p>";
                    header("Location: foxbookprofilepage.php?profileID=$profileID");
                    die();
                }else echo "<p class='wrong'>Nie wolno się tak bawić i zmieniać wartość typu zgłoszenia</p>";
            }
        }else echo "</br><h2 class='wrong' style='margin-left: 1%;'>Już raz zgłaszałeś/aś tego użytkownika</h2>
<form method='post' action='foxbookprofilepage.php?profileID=$profileID'><input type='submit' value='Powrót' class='form-submit-button'></form>";
    }else echo "</br><h2 class='wrong' style='margin-left: auto;margin-right: auto'>Nie możesz zgłaszać użytkowników nie będąc zalogowanym</h2>";
    ?>
</div>
<script>
    if( window.history.replaceState){
        window.history.replaceState(null, null, window.location.href);
    }
</script>
</body>
</html>