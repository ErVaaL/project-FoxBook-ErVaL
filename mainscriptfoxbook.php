<?php
require_once "fordatabaseconnection.php";
$id = @$_SESSION['loggedIn'];
$usersArray = getArrayOfUsersIDs();
if(@$_SESSION['loggedIn'] != null && @in_array($id,$usersArray)){
    if(in_array($_SESSION['loggedIn'],getArrayOfAdminUsers()) && $_SESSION['loggedIn'] == 1){
        $user = getUserObject();
        echo "<form method='post'><label for='logout'>Użytkownik " . $user->userUsername
            . " jest zalogowany</label></br>
<input type='submit' name='goToAdminPanel' id='goToAdminPanel' value='Panel admina' class='form-submit-button'>
<input type='submit' name='notifications' id='notifications' value='Powiadomienia' class='form-submit-button'>
&nbsp;<input type='submit' name='logout' id='logout' class='form-submit-button' value='Wyloguj'></form>";
        if(isset($_POST['goToAdminPanel'])){
            header("Location: foxbookadminpanel.php");
        }
        if (isset($_POST['logout'])) {
            unset($_SESSION['loggedIn']);
            header("Location: foxbookhomepage.php");
        }
        if (isset($_POST['notifications'])) {
            header("Location: foxbooknotificationmanage.php");
        }
    }else if(in_array($_SESSION['loggedIn'],getArrayOfAdminUsers())) {
        $profileID = getProfileID();
        $user = getUserObject();
        echo "<form method='post'><label for='logout'>Użytkownik " . $user->userUsername
            . " jest zalogowany</label></br>
<input type='submit' name='goToAdminPanel' id='goToAdminPanel' value='Panel admina' class='form-submit-button'>
<input type='submit' name='notifications' id='notifications' value='Powiadomienia' class='form-submit-button'>
&nbsp;<input type='submit' name='toProfile' id='toProfile' value='Profil' class='form-submit-button'>
&nbsp;<input type='submit' name='logout' id='logout' class='form-submit-button' value='Wyloguj'></form>";
        if (isset($_POST['logout'])) {
            unset($_SESSION['loggedIn']);
            header("Location: foxbookhomepage.php");
        }
        if (isset($_POST['notifications'])) {
            header("Location: foxbooknotificationmanage.php");
        }
        if (isset($_POST['toProfile'])) {
            header("Location: foxbookprofilepage.php?profileID=$profileID");
        }
        if(isset($_POST['goToAdminPanel'])){
            header("Location: foxbookadminpanel.php");
        }
    }else{
        $profileID = getProfileID();
        $user = getUserObject();
        echo "<form method='post'><label for='logout'>Użytkownik " . $user->userUsername
            . " jest zalogowany</label></br>
<input type='submit' name='notifications' id='notifications' value='Powiadomienia' class='form-submit-button'>
&nbsp;<input type='submit' name='toProfile' id='toProfile' value='Profil' class='form-submit-button'>
&nbsp;<input type='submit' name='logout' id='logout' class='form-submit-button' value='Wyloguj'></form>";
        if (isset($_POST['logout'])) {
            unset($_SESSION['loggedIn']);
            header("Location: foxbookhomepage.php");
        }
        if (isset($_POST['notifications'])) {
            header("Location: foxbooknotificationmanage.php");
        }
        if (isset($_POST['toProfile'])) {
            header("Location: foxbookprofilepage.php?profileID=$profileID");
        }
    }
}else{
    echo "<form method='post'>
            <input type='submit' name='signIn' id='signIn' value='Zaloguj się' class='form-submit-button'>
            <input type='submit' name='signUp' id='signUp' value='Zarejestruj się' class='form-submit-button'>
        </form>";
    if (isset($_POST['signIn'])){
        header("Location: foxbookloginpage.php");
    }else if(isset($_POST['signUp'])){
        header("Location: foxbookregisterpage.php");
    }
}
?>