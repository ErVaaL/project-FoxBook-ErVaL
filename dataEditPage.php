<?php require_once "fordatabaseconnection.php";session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Edytuj dane</title>
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
<body style="background-color: #184006">
<div class="header" id="header">
    <div id="headerText" style="font-family: Verdana"> <?php
        echo "<form method='post' action='foxbookhomepage.php'><input type='submit' value='FoxBook' class='invisible-button' style='font-size: xxx-large; color: white;'></form>"
        ?>
    </div>
    <div id="headerChooseMotive">
        <?php require "foxbookmotivescript.php"; ?>
    </div>
</div></br>
<div class="center" id="login-body">
    </br><h2>Edytuj profil</h2></br>
    <?php
    $profileID = getProfileID();
    $usersArray = getArrayOfUsersIDs();
    if(in_array(@$_SESSION['loggedIn'],$usersArray) && $_SESSION['loggedIn'] == getProfileOwnerID() && in_array(getProfileID(),getProfileIDs()) && $profileID == $_GET['profileID']){
        $user = getUserObject();
        $description = getProfileDescription($_GET['profileID']);
        $profile = getProfileObject($_GET['profileID']);
        $birthdate = date("Y-m-d", strtotime($user->userDateOfBirth));
            echo "<form method='post' action=''>
            <label for='userName'>Imię: </label>
            <input type='text' name='userName' id='userName' value='$user->userFirstName'/></br>
            <label for='userLastName'>Nazwisko: </label>
            <input type='text' name='userLastName' id='userLastName' value='$user->userLastName'></br>
            <label for='birthDate'>Data urodzenia: </label>
            <input type='date' name='birthDate' id='birthDate' value='" . $birthdate . "'></br>
            <label for='showBirth'>Czy ma pokazywać datę urodzenia?</label></br>";
            if($profile->showBirthDate){
                echo "<input type='radio' name='showBirth' id='showBirthYes' value='1' checked>Tak&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='showBirth' id='showBirthNo' value='0'>Nie</br>";
            }else echo "<input type='radio' name='showBirth' id='showBirthYes' value='1'>Tak&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='showBirth' id='showBirthNo' value='0' checked>Nie</br>";
            echo "<label for='showAddress'>Czy ma pokazywać adres zamieszkania?</label></br>";
            if($profile->showAddress){
                echo "<input type='radio' name='showAddress' id='showAddressYes' value='1' checked>Tak&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='showAddress' id='showAddressNo' value='0'>Nie</br>";
            }else echo "<input type='radio' name='showAddress' id='showAddressYes' value='1'>Tak&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='showAddress' id='showAddressNo' value='0' checked>Nie</br>";
            echo "<label for='address'>Adres:</label></br><input type='text' name='address' id='address' placeholder='Tutaj wpisz adres' value='$profile->address'></br>";
            echo "<label for='profileVisibility'>Widoczność profilu</label></br>
                <select name='profileVisibility'>";
            if($profile->seeProfile == "public"){
            echo"<option value='public' selected>Publiczny</option>
                <option value='friends-only'>Znajomi</option>
               <option value='private'>Prywatny</option>";
            }else if($profile->seeProfile == "friends-only"){
                echo"<option value='public'>Publiczny</option>
                <option value='friends-only' selected>Znajomi</option>
               <option value='private'>Prywatny</option>";
            }else if($profile->seeProfile == "private"){
                echo"<option value='public'>Publiczny</option>
                <option value='friends-only'>Znajomi</option>
               <option value='private' selected>Prywatny</option>";
            }
echo "</select>";
            echo "<label for='profileDesc'>Opis: </label></br></br>";
        if(empty($description)){
            echo "<textarea style='float: none;resize: none;width: 300px;border: 1px solid lightgrey; color: black; text-shadow: none;border-radius: 10px' name='profileDesc' id='profileDescription' placeholder='Tutaj możesz dodać swój opis, max 500 znaków'></textarea></br></br>";
        }else{
            echo "<textarea style='float: none;resize: none;width: 300px;border: 1px solid lightgrey;color: black; text-shadow: none;border-radius: 10px' name='profileDesc' id='profileDescription' placeholder='Tutaj możesz dodać swój opis, max 500 znaków'>$description</textarea></br></br>";
        }
        echo "<label>Zmiana hasła:</label></br>";
        echo "<label for='oldPassword'>Podaj stare hasło</label></br>";
        echo "<input type='password' name='oldPassword' id='oldPassword'></br>";
        echo "<label for='newPassword'>Podaj nowe hasło</label></br>";
        echo "<input type='password' name='newPassword' id='newPassword'></br>";
        echo "<input type='submit' name='changeData' id='changeData' value='Zaktualizuj' class='form-submit-button'>&nbsp; <input type='submit' name='goBack' id='goBack' value='Powrót' class='form-submit-button'></form>";
        if(isset($_POST['changeData'])){
            $changeUserObjectData = false;
            $changeProfileObjectData = false;
            if(preg_match('/^\b\w{2}\w*\b$/mi',$_POST['userName'])){
                $user->userFirstName = ucwords(strtolower($_POST['userName']));
                $changeUserObjectData = true;
            }
            if(preg_match('/^\b\w{2}\w*\b$/mi',$_POST['userName'])){
                $user->userLastName = ucwords(strtolower($_POST['userLastName']));
                $changeUserObjectData = true;
            }
            if(preg_match('/\d{4}-\d{2}-\d{2}/',$_POST['birthDate']) && date("Y-m-d",strtotime($_POST['birthDate'])) < date("Y-m-d",strtotime("-15 years"))){
                $user->userDateOfBirth = $_POST['birthDate'];
                $changeUserObjectData = true;
            }
            if(strlen($_POST['profileDesc']) <= 500){
                $description = htmlspecialchars($_POST['profileDesc']);
                setProfileDescription($description);
            }
            if($_POST['showBirth'] != $profile->showBirthDate){
                $profile->showBirthDate = $_POST['showBirth'];
                $changeProfileObjectData = true;
            }if($_POST['showAddress'] != $profile->showAddress){
                $profile->showAddress = $_POST['showAddress'];
                $profile->address = htmlspecialchars($_POST['address']);
                $changeProfileObjectData = true;
            }
            if($_POST['profileVisibility'] != $profile->seeProfile){
                $profile->seeProfile = $_POST['profileVisibility'];
                $changeProfileObjectData = true;
            }
            if(trim($_POST['oldPassword']) != ""){
                if(password_verify($_POST['oldPassword'],$user->userPassword)) {
                    if (preg_match_all('/[A-Z]/', $_POST['newPassword']) && preg_match_all('/[!?\/+=*\(\)\'\;\":<>_\-^%$#@&]/', $_POST['newPassword']) && preg_match_all('/\d/', $_POST['newPassword']) && preg_match_all('/[a-z]/', $_POST['newPassword']) && strlen(trim($_POST['newPassword'])) >= 8) {
                        $user->userPassword = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
                    } else $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Dane zostały zmienione, poza hasłem, ponieważ nie spełniało wymagań</p>";
                }else $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Dane zostały zmienione, poza hasłem, ponieważ nie spełniało wymagań</p>";
            }

            if($changeProfileObjectData){
                setProfileObject($profile->showBirthDate,$profile->showAddress,$profile->address,$profile->seeProfile);
            }
            if($changeUserObjectData){
                setUserObject($user->userFirstName,$user->userLastName,$user->userDateOfBirth,$user->userPassword);
            }
            header("Location: foxbookprofilepage.php?profileID=$profileID");
            die();
        }else if(isset($_POST['goBack'])){
            header("Location: foxbookprofilepage.php?profileID=$profileID");
            die();
        }
    }else {
        echo "<p class='wrong'>Nie powinieneś być na tej stronie, wyjdź</p>";
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