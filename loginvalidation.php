<?php
include_once "fordatabaseconnection.php";
$userId = 0;
    $login = htmlspecialchars($_POST['login']);
    $password =htmlspecialchars($_POST['password']);
    $correctData = false;
    $userArr = getArrayOfUsers();
if($userArr != null){
    foreach ($userArr as $object){
            if((strtolower($login) == strtolower($object->userUsername) || strtolower($login) == strtolower($object->userEmail)) && password_verify($password,$object->userPassword)){
                $correctData = true;
                if($correctData){
                    $pdo = usePDO();
                    $userGetId = $pdo->prepare("SELECT userID FROM user WHERE userEmail=?");
                    $userGetId->execute(array($object->userEmail));
                    $userId = $userGetId->fetch(PDO::FETCH_NUM)[0];
                    break;
                }
            }else continue;
        }
    }
    if ($correctData) {
        $_SESSION['loggedIn'] = $userId;
        header("Location: foxbookloginpage.php");
    }else echo "<p class='wrong'>Niepoprawny login lub hasło</p>";

?>