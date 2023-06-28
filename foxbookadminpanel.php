<?php require_once "fordatabaseconnection.php";session_start();ob_start();?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Panel Administratora</title>
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
        echo "<a href='foxbookhomepage.php' class='textLink' style='font-size: xxx-large;color: white'>FoxBook</a>";
        ?>
    </div>
    <div id="headerChooseMotive">
        <?php require "foxbookmotivescript.php";
        if(isset($_SESSION['message'])){
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        } ?>
    </div>
    <div id="headerForm">
        <?php require_once "mainscriptfoxbook.php";?>
    </div>
</div></br>
<div id="controlPanel">
    <?php require_once "adminpanelscript.php";    ?>
    <div id="reportsManagement">
        <h2 class="center">Zgłoszenia użytkowników</h2>
        <?php
        $reports = getAllReports();
        $callReports = function ($object){
            $user = getUserObjectFromID($object->reportedUser);
            $userProfile = getUserProfileIDFromID($object->reportedUser);
            $date = date("d.m.Y H:i", strtotime($object->reportDate));
            echo "<fieldset id='user-reported'>
                    <a class='textLink' href='foxbookprofilepage.php?profileID=$userProfile' style=''>$user->userFirstName&nbsp;$user->userLastName&nbsp;$user->userUsername</a></br>
                    Rodzaj przewinienia: $object->reportType, zgłoszony dnia: $date</br>
                    Przewinienie: $object->reportDescription</br>
                    <form method='post'><input type='submit' name='banUser[$object->reportID]' id='banUser' value='Zablokuj' class='form-submit-button'>&nbsp;
                    <input type='submit' name='rejectReport[$object->reportID]' id='rejectReport' value='Odrzuć' class='form-submit-button'></form>
        </fieldset>";
        };
        if(!empty($reports)){
            array_walk($reports,$callReports);
        }else echo "<h4 class='center'>Brak zgłoszeń</h4>";

        if(isset($_POST['banUser'])){
            $report = getReportObjectFromID(key($_POST['banUser']));
            var_dump(key($_POST['banUser']));
            blockUser($report->reportedUser, sprintf('Zostałeś zablokowany/a za:%s', $report->reportType));
            changeReportStatus($report->reportID,4);
            $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Użytkownik został zablokowany</p>";
           header("Location: foxbookadminpanel.php");
           die();
        }
        if(isset($_POST['rejectReport'])){
            $report = getReportObjectFromID(key($_POST['rejectReport']));
            changeReportStatus($report->reportID,4);
            $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Zgłoszenie odrzucone</p>";
            header("Location: foxbookadminpanel.php");
            die();
        }
        ?>

    </div>
</div>
<script>
    if( window.history.replaceState){
        window.history.replaceState(null, null, window.location.href);
    }
</script>
</body>
</html>