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
    <?php
    $usersArray = getArrayOfUsersIDs();
    if(@$_SESSION['loggedIn'] != null && @in_array($_SESSION['loggedIn'],$usersArray)) {
        echo"</br>
<form method='post' class='center'>
  <label for='groupName' style='font-size: large'><strong>Podaj nazwę grupy</strong></label></br></br>
  <input type='text' name='groupName' id='groupName' placeholder='Nazwa grupy, maksymalnie 100 znaków' class='form-inputs'></br></br>
    <input type='submit' name='create' id='create' value='Stwórz' class='form-submit-button'>&nbsp;<input type='submit' name='cancel' id='cancel' value='Anuluj' class='form-submit-button'>
   </form>";
        if(isset($_POST['cancel'])){
            header("Location: foxbookhomepage.php");
        }
        if(isset($_POST['create'])){
            if(trim($_POST['groupName']) !="" && strlen($_POST['groupName']) <=100){
                $allGroupsNames = getAllGroupsNames();
                if(!in_array($_POST['groupName'],$allGroupsNames)){
                    createGroup($_SESSION['loggedIn'],htmlspecialchars($_POST['groupName']));
                    $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Grupa została utworzona!</p>";
                    header("Location: foxbookhomepage.php");
                }else echo "<h4 class='wrong'>Grupa o podanej nazwie już istnieje</h4>";
            }else{
                echo "<h4 class='wrong'>Musisz podać nazwę dla grupy, która nie jest dłuższa niż 100 znaków</h4>";
            }
        }
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