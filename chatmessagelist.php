<?php
echo "<table id='sidebar-table' class='center' style='margin-left: 5%'>";
if(@$_SESSION['loggedIn'] != null && in_array($_SESSION['loggedIn'],getArrayOfUsersIDs())){
    if(!in_array($_SESSION['loggedIn'], getBlockedUsers())) {
        $friendsArray = getUserFriends($_SESSION['loggedIn']);
        if (in_array(null, $friendsArray)) {
            echo "<p class='center'>Nie masz znajomych, do których możesz wysłać wiadomość</p>";
        } else {
            $chatWalk = function ($value) {
                $user = getUserObjectFromID($value);
                echo "<tr><td style='height: 30px; width: 100%;'>
        <a class='textLink' href='foxbookmessagepage.php?toUserID=$value' style=''>$user->userFirstName&nbsp;$user->userLastName</a>
        </td></tr>";
            };
            array_walk($friendsArray, $chatWalk);
        }
    }else echo "<tr><td>Nie masz dostępu do tej sekcji</td></tr>";
}else echo "<tr><td>Musisz się zalogować by czatować</td></tr>";
echo "</table>";
?>