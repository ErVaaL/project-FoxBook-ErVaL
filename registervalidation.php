<?php
include_once "fordatabaseconnection.php";
function checkIfValid()
{
    $userArr = getArrayOfUsers();
    $check = 0;
    if (!preg_match('/\w{2}\w*/', htmlspecialchars($_POST['name']))) {
        ++$check;
        echo "<p class='wrong'>Imie musi składać się z więcej niż 1 litery</p>";
    }
    if (!preg_match('/\w{2}\w*/', htmlspecialchars($_POST['lastname']))) {
        ++$check;
        echo "<p class='wrong'>Nazwisko musi składać się z więcej niż 1 litery</p>";
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        ++$check;
        echo "<p class='wrong'>Podany adres e-mail jest w niepoprawnym formacie</p>";
    }
    if($userArr != null){
        foreach ($userArr as $object){
            if(strtolower($object->userUsername) == strtolower($_POST['username'])){
                ++$check; echo "<p class='wrong'>Podany pseudonim jest już zajęty</p>";
                break;
            }
            if(strtolower($object->userEmail) == strtolower($_POST['email'])){
                ++$check; echo "<p class='wrong'>Podany adres mailowy jest już zarejestrowany</p>";
                break;
            }
        }
    }
    if (!preg_match_all('/\w/', htmlspecialchars($_POST['username']))) {
        ++$check;
        echo "<p class='wrong'>Podany pseudonim musi zawierać min. 1 literę</p>";
    }
    if (!preg_match('/\b\d{4}-\d{2}-\d{2}\b/',$_POST['dateOfBirth'])){
        ++$check;
        echo "<p class='wrong'>Podany format daty jest nieodpowiedni</p>";
    }
    if (date("Y-m-d",strtotime("-15 years")) < date("Y-m-d", strtotime($_POST['dateOfBirth']))){
        ++$check;
        echo "<p class='wrong'>Musisz mieć minimum 15 lat by założyć konto</p>";
    }
    if (!preg_match_all('/[A-Z]/',$_POST['password']) || !preg_match_all('/[!?\/+=*\(\)\'\;\":<>_\-^%$#@&]/',$_POST['password']) || !preg_match_all('/\d/',$_POST['password']) || !preg_match_all('/[a-z]/',$_POST['password']) || strlen(trim($_POST['password'])) < 8){
        ++$check;
        echo "<p class='wrong'>Hasło nie spełnia wymagań</p>";
    }
    if ($check == 0) return true; else return false;
}
    if(checkIfValid()){
        $pdo= usePDO();
       $queryAddUser = $pdo->prepare("INSERT INTO user(userFirstName,userLastName,userEmail,userDateOfBirth,userPassword,userUsername,userProfile) VALUES (?,?,?,?,?,?,?);");
       $queryAddProfile = $pdo->query("INSERT INTO profile(profileShowBirthDate,profileShowAddress,profileSeeProfile) VALUES (0,0,'private');");
       $profileID = $pdo->lastInsertId();
       echo $profileID;
       $tempArr = array(htmlspecialchars($_POST['name']),
           htmlspecialchars($_POST['lastname']),
           $_POST['email'],
           $_POST['dateOfBirth'],
           password_hash(($_POST['password']),PASSWORD_DEFAULT),
           htmlspecialchars($_POST['username']),
           $profileID);
       $queryAddUser->execute($tempArr);
       $_SESSION['justRegistered'] = true;
       header("Location: foxbookregisterpage.php");
    }else echo "<p class='wrong'>Nie udało się zarejestrować</p>"
?>