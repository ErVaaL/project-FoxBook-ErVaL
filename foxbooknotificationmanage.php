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
$notificationsArray = getNotificationOfUserID($_SESSION['loggedIn']);
echo "</br>";
include_once "htmlfiles/readMessagesForm.html";
if(!empty($notificationsArray)){
    foreach ($notificationsArray as $object){
        if($object->fromWho == $_SESSION['loggedIn']){
            $user1 = getUserObjectFromID($object->toWho);
            if(get_class($object) == "FriendRequest"){
                echo "<p id='notification-line'>Twoje zaproszenie oczekuje na odpowiedź użytkownika: $user1->userFirstName</p>";
            }
        }else if($object->toWho == $_SESSION['loggedIn']){
            if(get_class($object) == "FriendRequest"){
                $user2 = getUserObjectFromID($object->fromWho);
                $description = $object->requestMessage;
                echo "<p style='text-shadow: none' id='notification-line'>$description od użytkownika: $user2->userFirstName <form method='post' style='margin-left: 1%'>
                <input type='submit' name='accept' id='accept' value='Zaakceptuj' class='form-submit-button'/>
                <input type='submit' name='decline' id='decline' value='Odrzuć' class='form-submit-button'/>
                </form></p>";

                if(isset($_POST['decline'])){
                    $pdo = usePDO();
                    $declineQuery = $pdo->prepare("UPDATE notification SET NotificationStatus=4 WHERE notificationID=?;");
                    $declineQuery->execute(array($object->requestID));
                    header("Location: ".$_SERVER['PHP_SELF']);
                }else if(isset($_POST['accept'])){
                    $addFriendQuery = $pdo->prepare("UPDATE user SET userFriends=? WHERE userID=? ");
                    $user1Friends = getUserFriends($object->fromWho);
                    if($user1Friends[0] == ''){
                        $user1Friends = $object->toWho;
                        $addFriendQuery->execute(array($user1Friends,$object->fromWho));
                    }else{
                        $user1Friends[] = $object->toWho;
                        $addFriendQuery->execute(array(implode(",",$user1Friends),$object->fromWho));
                    }
                    $user2Friends = getUserFriends($object->toWho);
                    if($user2Friends[0] == ''){
                        $user2Friends = $object->fromWho;
                        $addFriendQuery->execute(array($user2Friends,$object->toWho));
                    }else{
                        $user2Friends[] = $object->fromWho;;
                        $addFriendQuery->execute(array(implode(",",$user2Friends),$object->toWho));
                    }
                    $deleteQuery = $pdo->prepare("UPDATE notification SET NotificationStatus=4 WHERE notificationID=?;");
                    $deleteQuery->execute(array($object->requestID));
                    header("Location: ".$_SERVER['PHP_SELF']);
                }
            }
            if(get_class($object) == "Alert"){
                if($object->alertSeen != 4){
                    $message = $object->alertMessage;
                    echo "<fieldset id='message-space'><p style='text-shadow: none' id='notification-line'>Wiadomość od grupy</p> 
                   <p style='text-shadow: none;font-style: italic;color: lightgrey' id='message-line' >$message</p>
                <form method='post' style='margin-left: 1%'>
                <input type='submit' name='messageRead' id='messageRead' value='Okej' class='form-submit-button'/>
                </form></fieldset>";
                    if(isset($_POST['messageRead'])){
                        acceptAlert($object->requestID);
                        header("Location: foxbooknotificationmanage.php");
                    }
                }
            }
            if(get_class($object) == "GroupRequest"){
                if($object->requestStatus != 4){
                    $description = $object->requestMessage;
                    echo "<p style='text-shadow: none' id='notification-line'>$description<form method='post' style='margin-left: 1%'>
                <input type='submit' name='accept' id='accept' value='Zaakceptuj' class='form-submit-button'/>
                <input type='submit' name='decline' id='decline' value='Odrzuć' class='form-submit-button'/>
                </form></p>";
                    if(isset($_POST['decline'])){
                        $pdo = usePDO();
                        $declineQuery = $pdo->prepare("UPDATE notification SET NotificationStatus=4 WHERE notificationID=?;");
                        $declineQuery->execute(array($object->requestID));
                        $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Zaproszenie odrzucone</p>";
                        header("Location: ".$_SERVER['PHP_SELF']);
                        die();
                    }else if(isset($_POST['accept'])){
                        $addToGroupQuery = $pdo->prepare("INSERT INTO groupmembers(groupID,groupMember) VALUES (?,?);");
                        $group = getGroupFromID($object->fromWho);
                        $addToGroupQuery->execute(array($group->groupID,$object->toWho));
                        $finishedQuery = $pdo->prepare("UPDATE notification SET NotificationStatus=4 WHERE notificationID=?;");
                        $finishedQuery->execute(array($object->requestID));
                        $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Zaproszenie zaakceptowane</p>";
                        header("Location: ".$_SERVER['PHP_SELF']);
                        die();
                    }
                }
            }
            if(get_class($object) == "Message"){
                if($object->messageVisibility != 2 ){
                    $user2 = getUserObjectFromID($object->fromWho);
                    $message = $object->messageContents;
                    echo "<fieldset id='message-space'><p style='text-shadow: none' id='notification-line'>Wiadomość od użytkownika $user2->userFirstName:</p> 
                   <p style='text-shadow: none;font-style: italic;color: lightgrey' id='message-line' >$message</p>
                <form method='post' style='margin-left: 1%'>
                <input type='submit' name='messageRead' id='messageRead' value='Oznacz jako przeczytane' class='form-submit-button'/>
                </form></fieldset>";
                    if(isset($_POST['messageRead'])){
                        changeMessageRead($object->requestID);
                        header("Location: foxbooknotificationmanage.php");
                    }
                }
            }


        }
    }
}else{
    echo "<h2 class='center' id='notification-line'>Nie masz żadnych powiadomień</h2>";
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