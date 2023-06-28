<?php require_once "fordatabaseconnection.php";session_start(); ob_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>FoxBook</title>
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
    <div id="headerText">
        <?php
        echo "<a href='foxbookhomepage.php' class='textLink' style='font-size: xxx-large;color: white'>FoxBook</a>"
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
<div id="sidebar-left">
    <h2 class="center">Lista znajomych</h2>
    <?php include_once "friendlistscript.php"?>
</div>
<div id="sidebar-right">
    <h2 class="center">Czaty</h2>
    <?php require_once "chatmessagelist.php";?>
</div>
<div id="center-body-upper">
</br>
    <h2 class="center">Witaj na stronie grupy:</h2>
   <?php
        $group = getGroupFromID($_GET['groupID']);
        echo "<h2 class='center'>$group->groupName</h2>";
        if($group->groupCreatorID == $_SESSION['loggedIn']){
            if(!in_array($_SESSION['loggedIn'],getBlockedUsers())){
            echo "<form method='post' class='center'>
        <input type='submit' name='deleteGroup' id='deleteGroup' value='Usuń grupę' class='form-submit-button'>";
            }else echo "<p class='center'>Będąc zbanowanym, nie możesz zarządać grupą</p>";
        }else if(in_array($_SESSION['loggedIn'],getGroupMembers($group->groupID))){
       echo "<form method='post' class='center'>
        <input type='submit' name='leaveGroup' id='leaveGroup' value='Opuść grupę' class='form-submit-button'>";
        }
        if(isset($_POST['leaveGroup'])){
            leaveGroup($group->groupID,$_SESSION['loggedIn']);
            $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Nie jesteś już członkiem grupy</p>";
            header("Location: foxbookhomepage.php");
        }
        if(isset($_POST['deleteGroup'])){
            deleteGroup($group->groupID);
            $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Grupa została usunięta</p>";
            header("Location: foxbookhomepage.php");
        }
   ?>
</div>
<div id="center-body-bottom">
<?php
$group = getGroupFromID($_GET['groupID']);
if($_SESSION['loggedIn'] == $group->groupCreatorID){
  echo "</br><h2 class='center'>Wydarzenia grupy:</h2>";
  if(!in_array($_SESSION['loggedIn'],getBlockedUsers())){
  echo "<form method='post' >
<input type='submit' name='eventCreation' value='Utwórz wydarzenie' style='margin-left: 45%;margin-right: 50%' class='form-submit-button'></form>";
  if(isset($_POST['eventCreation'])){
      header("Location: eventcreationpage.php?groupID=$group->groupID");
  }
  }else echo "<h3 class='center'>Nie możesz tworzyć wydarzeń</h3>";
}else if(in_array($_SESSION['loggedIn'],getGroupMembers($_GET['groupID']))){
    echo "</br><h2 class='center'>Wydarzenia grupy:</h2>";
}
$events = getGroupEvents($group->groupID);
if(empty($events)){
    echo "<h3 class='center'>Na grupie nie ma żadnych wydarzeń</h3>";
}else{
    $callEvents = function ($object){
        $user = getUserObjectFromID($object->eventCreator);
        $date = date("d.m.Y H:i", strtotime($object->eventDate));
        if(getEventStatus($object->eventID) != 2){
            echo "<fieldset id='eventSpace'>
            <p style='margin-bottom: -2%'><span style='color: dimgrey'>$user->userFirstName $user->userLastName wydarzenie dnia: $date</span></p>
            <p><strong>$object->eventName</strong></p>
            <p style='margin-top: 5%'>$object->eventDescription</p></br></br>";
            if(in_array($_SESSION['loggedIn'],getEventParticipants($object->eventID))){
                echo "<form method='post' style='float: left;'><input type='submit' name='leaveEvent[$object->eventID]' id='leaveEvent' value='Zrezygnuj' class='form-submit-button'>&nbsp;
<input type='submit' name='eventDetails[$object->eventID]' id='eventDetails' value='Szczegóły' class='form-submit-button'></form>";
            }else{
                echo "<form method='post' style='float: left;'><input type='submit' name='joinEvent[$object->eventID]' id='joinEvent' value='Dołącz' class='form-submit-button'>&nbsp;
<input type='submit' name='eventDetails[$object->eventID]' id='eventDetails' value='Szczegóły' class='form-submit-button'></form>";
            }
            if($_SESSION['loggedIn'] == $object->eventCreator){
                echo "<form method='post' style='float: right;'><input type='submit' name='editEvent[$object->eventID]' id='editEvent' value='Edytuj wydarzenie' class='form-submit-button'></form>";
            }
            echo "</fieldset>";
        }
    };
    array_walk($events,$callEvents);
    if(isset($_POST['joinEvent'])){
        joinEvent(key($_POST['joinEvent']));
        $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Dołączyłeś/aś do wydarzenia!</p>";
        header("Location: foxbookgrouppage.php?groupID=$group->groupID");
        die();
    }
    if(isset($_POST['leaveEvent'])){
        leaveEvent(key($_POST['leaveEvent']));
        $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Zrezygnowałeś/aś z uczestniczenia w wydarzeniu</p>";
        header("Location: foxbookgrouppage.php?groupID=$group->groupID");
        die();
    }
    if(isset($_POST['eventDetails'])){
        $eventID = key($_POST['eventDetails']);
        header("Location: foxbookeventdetails.php?eventID=$eventID");
        die();
    }
    if(isset($_POST['editEvent'])){
        $eventID = key($_POST['editEvent']);
        header("Location: eventEditPage.php?eventID=$eventID");
        die();
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