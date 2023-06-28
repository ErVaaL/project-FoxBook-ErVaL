<?php
require_once "fordatabaseconnection.php";
if(in_array(@$_SESSION['loggedIn'],getArrayOfAdminUsers())) {
    $isAdmin = false;
    $isBlocked = false;
    $usersArrayIDs = getArrayOfUsersIDs();
    echo "</br><div id='usersManagment'>
<h2 class='center'>Zarządzanie użytkownikami</h2>";
    echo "</br><form method='post' id='usersManagmentForm' class='center'><label for='chooseUser'>Wybierz użytkownika: </label></br></br>";
    echo "<table id='chooseUser' class='center'>";
    echo "<tr><th style='width: 60px;'>Imię</th><th style='width: 120px'>Nazwisko</th><th style='width: 120px'>Pseudonim</th><th style='width: 60px' colspan='3'>Opcje</th></tr>";
    $call = function ($value){
        $key = intval($value);
        $object = getUserObjectFromID($key);
        if($object->userUsername != "admin"){
        echo "<tr><td>$object->userFirstName</td><td>$object->userLastName</td><td>$object->userUsername</td>";
        echo "<input type='hidden' name='userKey' value='$key'>";
        if(in_array($key,getBlockedUsers())){
            echo "<td><input type='submit' name='unBlockUser[$key]' value='Odblokuj' class='form-submit-button' style='margin-left: -5%'></td>";
        }else{
            echo "<td><input type='submit' name='blockUser[$key]' value='Zablokuj' class='form-submit-button' style='margin-left: -5%'></td>";
        }
        echo "<td><input type='submit' name='deleteUser[$key]' value='Usuń' class='form-submit-button'></td>";
        if(in_array($key,getArrayOfAdminUsers())){
            echo "<td><input type='submit' name='revokePermissions[$key]' value='Cofnij uprawnienia' class='form-submit-button'></td></tr>";
        }else{
            echo "<td><input type='submit' name='givePermissions[$key]' value='Nadaj uprawnienia' class='form-submit-button'></td></tr>";
        }
        }
    };
    array_walk($usersArrayIDs,$call);
    echo "</table>";


    echo "</br>";
    $pdo = usePDO();
    if (isset($_POST['blockUser'])) {
        $blockUser = $pdo->prepare("INSERT INTO blockedusers(BlockedUser,BlockReason) VALUES (?,?)");
        $userID = key($_POST['blockUser']);
        $blockUser->execute(array($userID, "Zostałeś zbanowany"));
        $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Użytkownik został zablokowany</p>";
        header("Location: foxbookadminpanel.php");
    }else if(isset($_POST['unBlockUser'])){
        $unBlockUser = $pdo->prepare("DELETE FROM blockedusers WHERE BlockedUser=?");
        $userID = key($_POST['unBlockUser']);
        $unBlockUser->execute(array($userID));
        $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Użytkownik został odblokowany</p>";
        header("Location: foxbookadminpanel.php");
}else if(isset($_POST['deleteUser'])){
        $userDelete = $pdo->prepare("DELETE FROM user WHERE userID=?");
        $userID = key($_POST['deleteUser']);
        $userDelete->execute(array($userID));
        if($isBlocked){
            $pdo->query("DELETE FROM blockedusers WHERE BlockedUser=$userID;");
        }
        if($isAdmin){
            $pdo->query("DELETE FROM adminusers WHERE UserID=$userID");
        }
        $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Użytkownik został usunięty</p>";
        header("Location: foxbookadminpanel.php");

    }else if(isset($_POST['givePermissions'])){
        $givePerm = $pdo->prepare("INSERT INTO adminusers(UserID) VALUES (?)");
        $userID = key($_POST['givePermissions']);
        $givePerm->execute(array($userID));
        $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Użytkownik dostał uprawnienia</p>";
        header("Location: foxbookadminpanel.php");
    }else if(isset($_POST['revokePermissions'])){
        $revokePerm = $pdo->prepare("DELETE FROM adminusers WHERE UserID=?");
        $userID = key($_POST['revokePermissions']);
        $revokePerm->execute(array($userID));
        $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Użytkownik stracił uprawnienia</p>";
        header("Location: foxbookadminpanel.php");
    }
echo "</div>";
}else{
    echo "</br><h2 class='wrong'>Nie możesz być na tej stronie, wyjdź!</h2>";
}
?>