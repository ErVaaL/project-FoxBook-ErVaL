<?php require_once "fordatabaseconnection.php";session_start();
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
        <?php
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
    <?php $profileIDs = getProfileIDs(); if(!in_array($_GET['profileID'],$profileIDs)) {
        echo "<p class='wrong'>Nie ma profilu o takim ID, wróć na stronę główną!</p>";
        die();}
    ?>
    <div id="profilePic"></div>&nbsp;
    <div id="profileCredentials">
        <?php
        $user = getUserObjectFromID(getProfileOwnerID());
        $usersArray = getArrayOfUsersIDs();
        @$profileID=getProfileID();
        $profileOwnerID = getProfileOwnerID();
        $profId = getUserProfileIDFromID($profileOwnerID);
        echo "<p>".$user->getCredentials($user)."</p>";
        if(@$_SESSION['loggedIn'] != ""){
            $user1Friends =  getUserFriends($_SESSION['loggedIn']);
            $user2Friends = getUserFriends($profileOwnerID);

            if(in_array($_SESSION['loggedIn'], $usersArray) && getProfileOwnerID() == $_SESSION['loggedIn']) {
                    echo "<form method='post' action='dataEditPage.php?profileID=$profileID'>
        <input type='submit' name='editProfileButton' id='editProfileButton' value='Edytuj profil' class='form-submit-button'></form>";
                }else if(!@in_array(getProfileOwnerID(),$user1Friends) && checkFriendRequestSides($_SESSION['loggedIn'],$profileOwnerID)){
                if(!in_array($_SESSION['loggedIn'],getBlockedUsers())) {
                    echo "<form method='post'>
            <input type='submit' name='addToFriendList' id='addToFriendList' class='form-submit-button' value='Zaproś do znajomych'>&nbsp;<input type='submit' name='reportUser' id='reportUser' class='form-submit-button' value='Zgłoś użytkownika'>
            </form>";
                    echo "<form method='post'><input type='submit' name='addToGroup' id='addToGroup' value='Zaproś do grupy' class='form-submit-button'></form>";
                    if (isset($_POST['addToFriendList'])) {
                        $userID = $_SESSION['loggedIn'];
                        addToNotificationsTable($userID, getProfileOwnerID(), "Zaproszenie do grona znajomych");
                        header("Location: foxbookprofilepage.php?profileID=$profId");
                    }
                }else echo "<p>Jesteś zbanowany/a, Twoje możliwości są ogarniczone</p>";
                }else if(in_array(getProfileOwnerID(),$user1Friends)){
                if(!in_array($_SESSION['loggedIn'],getBlockedUsers())) {
                    echo "<form method='post'>
                    <input type='submit' name='removeFriend' id='removeFriend' value='Usuń ze znajomych' class='form-submit-button'/>&nbsp;<input type='submit' name='reportUser' id='reportUser' class='form-submit-button' value='Zgłoś użytkownika'></form>";
                echo "<form method='post'><input type='submit' name='addToGroup' id='addToGroup' value='Zaproś do grupy' class='form-submit-button'></form>";
                if(isset($_POST['removeFriend'])){
                        $pdo =usePDO();
                        $userID = $_SESSION['loggedIn'];
                        $key1 = array_search($profileOwnerID,$user1Friends);
                        $key2 = array_search($userID,$user2Friends);
                        array_splice($user1Friends,$key1,1);
                        array_splice($user2Friends,$key2,1);
                        $updateQuery = $pdo->prepare("UPDATE user SET userFriends=? WHERE  userID=?;");
                        $input1 = implode(",",$user1Friends);
                        $input2 = implode(",",$user2Friends);
                        $updateQuery->execute(array($input1,$userID));
                        $updateQuery->execute(array($input2,$profileOwnerID));
                        $pdo->query("UPDATE notification SET NotificationStatus=4 WHERE (FromFirstUserID= $userID AND ToSecondUserID=$profileOwnerID) OR (FromFirstUserID= $profileOwnerID AND ToSecondUserID=$userID);");
                        header("Location: foxbookprofilepage.php?profileID=$profId");
                    }
                }else echo "<p>Jesteś zbanowany/a, Twoje możliwości są ogarniczone</p>";
            }
            if(isset($_POST['reportUser'])){
                header("Location: reportuserpage.php?userID=$profileOwnerID");
                die();
            }
            if(isset($_POST['addToGroup'])){
                header("Location: inviteusertogroup.php?userID=$profileOwnerID");
                die();
            }
            }

        ?>
    </div>
    <?php   $profile = getProfileObject($_GET['profileID']);
    if($profile->seeProfile !== "private" || @$_SESSION['loggedIn'] == getProfileOwnerID()){

        ?>
    <div id="user-info"><h4 class="center">Dodatkowe informacje:</h4>
        <?php
        $profileID = $_GET['profileID'];
        echo "<p>Urodziny: ";
           if($profile->showBirthDate){
               echo date("d-m-Y",strtotime($user->userDateOfBirth))."</p>";
           }else echo "prywatne </p>";
           echo "<p>Adres zamieszkania: ";
        if($profile->showAddress){
            echo $profile->address."</p>";
        }else echo "prywatne </p>";
        ?>
    </div>
    <div id="profileDescription">
        <h4 class="center">Opis</h4>
        <?php
        $userID = @$_SESSION['loggedIn'];
            $description= getProfileDescription($_GET['profileID']);
            if(empty($description)){
                echo "<p style='color: gray'>Użytkownik nie ustawił opisu</p>";
            }else echo "<p>".$description."</p>";
        ?>
    </div>
    <?php } else if($profile->seeProfile === "private" && @$_SESSION['loggedIn'] != getProfileOwnerID()) {
        echo "<h2>Użytkownik ma prywatny profil</h2>";
    }else{
       if(in_array($_SESSION['loggedIn'],explode(",",getUserFriends(getProfileOwnerID())))){?>
           <div id="user-info"><h4 class="center">Dodatkowe informacje:</h4>
        <?php
        $profileID = $_GET['profileID'];
        echo "<p>Urodziny: ";
           if($profile->showBirthDate){
               echo date("d-m-Y",strtotime($user->userDateOfBirth))."</p>";
           }else echo "prywatne </p>";
           echo "<p>Adres zamieszkania: ";
        if($profile->showAddress){
            echo $profile->address."</p>";
        }else echo "prywatne </p>";
    }else  echo "<h2>Użytkownik ma prywatny profil</h2>"; }?>
</div>

</div>
</div>
<div id="center-body-bottom">

</div>
<script>
    if( window.history.replaceState){
        window.history.replaceState(null, null, window.location.href);
    }
</script>
</body>
</html>