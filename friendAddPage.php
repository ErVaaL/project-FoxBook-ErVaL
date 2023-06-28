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
        <?php require "foxbookmotivescript.php";
        if(isset($_SESSION['message'])){
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }?>
    </div>
    <div id="headerForm">
        <?php require_once "mainscriptfoxbook.php";?>
    </div>
</div></br>
<div id="center-body">
<form method="post" action=""></br>
    <input type="text" id="searchForFriends" name="searchForFriends" placeholder='Szukaj znajomych...'><input type="submit" name="search" id="search" value="Szukaj">
</form>
    <div id="peopleList">
        <?php
        $check = 0;
        $usersIDsArray = getArrayOfUsersIDs();
        $arrayWalkPeopleSearch = function ($value,$key,$prefix){
            global $check;
            if(trim($prefix[0]) != "")
                    $regex1 = '/\w*'.$prefix[0].'\w*/mi';
                if(trim(@$prefix[1]) != "")
                    $regex2 = '/\w*'.@$prefix[1].'\w*/mi';
                $id = intval($value);
                $user = getUserObjectFromID($id);
                $profID = getUserProfileIDFromID($id);
                if((preg_match($regex1, $user->userFirstName) || @preg_match($regex2,$user->userLastName))){
                    echo "<h4 style='margin-left: 2%'>
        <a class='textLink' href='foxbookprofilepage.php?profileID=$profID' style=''>$user->userFirstName&nbsp;$user->userLastName</a></h4>";
                }else ++$check;
        };
        if(isset($_POST['search'])){
            if(trim($_POST['searchForFriends'] != "") && preg_match_all('/\w/mi',$_POST['searchForFriends'])){
            $search = explode(" ",$_POST['searchForFriends']);
            array_walk($usersIDsArray,$arrayWalkPeopleSearch,$search);
            if($check==sizeof($usersIDsArray)){
                echo "<h2 class='center'>Brak użytkowników pasujących do klucza</h2>";
            }
            }else{
                echo "<h2 class='center'>Brak użytkowników pasujących do podanego klucza</h2>";
            }
        }
        ?>
    </div>
</div>
<script>
    if( window.history.replaceState){
        window.history.replaceState(null, null, window.location.href);
    }
</script>
</body>
</html>