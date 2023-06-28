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
<div id="center-body">
</br>
    <form method="post" class="center">
        <label for="chooseGroup" style="font-size: large">Wybierz grupę z do której chcesz dać zaproszenie</label></br></br>
        <select name="chooseGroup" id="chooseGroup">
    <?php
    $userGroups = getUserGroups($_SESSION['loggedIn']);

    if(!empty($userGroups)){
        $callGroups = function ($object){
            echo "<option value='$object->groupID'>$object->groupName</option>";
        };
        array_walk($userGroups,$callGroups);
    }else{
        echo "<h2 class='wrong'>Nie masz grup do których możesz zaprosić tego użytkownika</h2>";
    }

    ?>
        </select></br></br>
        <input type="submit" name="sendInvite" id="sendInvite" value="Wyślij zaproszenie" class="form-submit-button">&nbsp;
        <input type="submit" name="goBack" id="goBack" value="Powrót" class="form-submit-button">
    </form>
    <?php
        if(isset($_POST['sendInvite'])){
            $profileID= getUserProfileIDFromID($_GET['userID']);
            if(!in_array($_GET['userID'], getGroupMembers($_POST['chooseGroup']))){
                sendGroupInvitation($_POST['chooseGroup'],$_GET['userID']);
                $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Zaproszenie wysłane!</p>";
                header("Location: foxbookprofilepage.php?profileID=$profileID");
                die();
            }else{
                echo "<h2 class='center'>Użytkownik już znajduje się w wybranej grupie</h2>";
            }
        }
        if(isset($_POST['goBack'])){
            $profileID= getUserProfileIDFromID($_GET['userID']);
            header("Location: foxbookprofilepage.php?profileID=$profileID");
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