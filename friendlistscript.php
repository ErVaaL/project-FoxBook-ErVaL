<?php
require_once "fordatabaseconnection.php";
echo "<table id='sidebar-table' class='center' style='margin-left: 2%'>";
if(@$_SESSION['loggedIn'] != null && in_array($_SESSION['loggedIn'],getArrayOfUsersIDs())) {
    if (!in_array($_SESSION['loggedIn'], getBlockedUsers())) {
        $friendsArray = getUserFriends($_SESSION['loggedIn']);
        $arrayWalkFriends = function ($value) {
            $user = getUserObjectFromID($value);
            $profID = getUserProfileIDFromID($value);
            echo "<tr><td style='height: 30px; width: 100%'>
        <a class='textLink' href='foxbookprofilepage.php?profileID=$profID' style=''>$user->userFirstName&nbsp;$user->userLastName</a>
        </td></tr>";
        };
        $arrayWalkFriendsSearch = function ($value, $key, $prefix) {
            $regex1 = sprintf('/%s/mi', $prefix[0]);
            $regex2 = sprintf('/%s/mi', @$prefix[1]);
            $id = intval($value);
            $user = getUserObjectFromID($id);
            $profID = getUserProfileIDFromID($id);
            if ((preg_match($regex1, $user->userFirstName) || preg_match($regex2, $user->userLastName)) && $prefix[0] != "") {
                echo "<tr><td style='height: 30px; width: 100%'>
        <a class='textLink' href='foxbookprofilepage.php?profileID=$profID' style=''>$user->userFirstName&nbsp;$user->userLastName</a>
        </td></tr>";
            }
        };
        echo "<tr><td class='center'><form  method='post'>
<input type='text' name='searchFriend' id='searchFriend' style='width: 90%;height: 20px;font-size: medium; border-radius: 20px'></br></br>
<input type='submit' name='addFriend' id='addFriend' value='Dodaj znajomych' class='form-submit-button'>
&nbsp;<input type='submit' name='searchFriendButton' id='searchFriendButton' class='form-submit-button' value='Szukaj'></form></td></tr>";
        if (isset($_POST['addFriend'])) {
            header("Location: friendAddPage.php");
        }

        if (empty($friendsArray) || in_array(null, $friendsArray)) {
            echo "<tr><td style='height: 30px'>Nie masz jeszcze przyjaciół, pora dodać nowych</td></tr>";
        } else if (isset($_POST['searchFriendButton'])) {
            if (trim($_POST['searchFriend']) != "") {
                $search = explode(" ", $_POST['searchFriend']);
                array_walk($friendsArray, $arrayWalkFriendsSearch, $search);
            } else {
                echo "<p class='wrong' style='margin-left: 1%'>Podany klucz nie pasuje, do żadnego użytkownika</p>";
                array_walk($friendsArray, $arrayWalkFriends);
            }
        } else {
            array_walk($friendsArray, $arrayWalkFriends);
        }
    } else echo "<tr><td>Nie masz dostępu do tej sekcji</td></tr>";
}else echo "<tr><td style='height: 30px'>Musisz się zalogować by widzieć swoich znajomych</td></tr>";
echo "</table>";
?>
