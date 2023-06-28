<?php require_once "fordatabaseconnection.php"; session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Dodawanie komentarza</title>
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
    $usersArray = getArrayOfUsersIDs();
    $postsArr = getAllPosts();
    $userID = $_SESSION['loggedIn'];
    $postID = $_GET['postID'];
    if(in_array($postID,getAllPosts())) {
        if (@$_SESSION['loggedIn'] != null && @in_array($_SESSION['loggedIn'], $usersArray)) {
            if (@$_SESSION['commentEdit'] != null) {
                $commID = $_SESSION['commentEdit'];
                @$commentContents = getCommentContents($commID);
                echo "</br><form method='post' class='center'>
            <label for='commentContents'>Treść komentarza:</label></br></br>
            <textarea id='commentContents' name='commentContents' placeholder='Tutaj wpisz komentarz...' style='width: 40%;height: 200px; resize: none'>$commentContents</textarea></br>
            <input type='submit' name='editComment' id='editComment' value='Zatwierdź zmiany' class='form-submit-button'>
            <input type='submit' name='goBack' id='goBack' value='Powrót' class='form-submit-button'></form>";
            } else {
                echo "</br><form method='post' class='center'>
            <label for='commentContents'>Treść komentarza:</label></br></br>
            <textarea id='commentContents' name='commentContents' placeholder='Tutaj wpisz komentarz...' style='width: 40%;height: 200px; resize: none'></textarea></br>
            <input type='submit' name='addComment' id='addComment' value='Dodaj komentarz' class='form-submit-button'>
            <input type='submit' name='goBack' id='goBack' value='Powrót' class='form-submit-button'></form>";
            }
        } else echo "<p>Nie powinieneś/aś być na tej stronie, wyjdź</p>";
        if (isset($_POST['addComment'])) {
            $pdo = usePDO();
            $addQuery = $pdo->prepare("INSERT INTO Comments(CommentedPost,CommentSender,CommentContents,CommentEdited,CommentDate) VALUES (?,?,?,?,?);");
            if (trim($_POST['commentContents']) != "" && strlen($_POST['commentContents']) < 200) {
                date_default_timezone_set("Europe/Warsaw");
                $dateComm = date("Y-m-d H:i:s");
                $addQuery->execute(array($postID, $userID, htmlspecialchars($_POST['commentContents']), 0, $dateComm));
                $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Komentarz dodany!</p>";
                header("Location: foxbookhomepage.php");
            } else echo "<p class='wrong'>Komentarz może zawierać maksymalnie 200 znaków i nie może być pusty</p>";
        } else if (isset($_POST['editComment'])) {
            $pdo = usePDO();
            $editQuery = $pdo->prepare("UPDATE Comments SET CommentContents=?,CommentEdited=? WHERE CommentID=?");
            if (trim($_POST['commentContents']) != "" && strlen($_POST['commentContents']) < 200) {
                $editQuery->execute(array(htmlspecialchars($_POST['commentContents']), 1, $commID));
                $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Komentarz zedytowany</p>";
                unset($_SESSION['commentEdit']);
                header("Location: commentsToPost.php?postID=$postID");
            } else echo "<p class='wrong'>Komentarz może zawierać maksymalnie 200 znaków i nie może być pusty</p>";
        }
        if (isset($_POST['goBack'])) {
            unset($_SESSION['commentAdding']);
            unset($_SESSION['editComment']);
            header("Location: foxbookhomepage.php");
        }
    }else{
        echo "</br><h2 class='wrong' style='margin-left: auto; margin-right: auto'>Nie ma postu o takim ID, wróć na stronę główną</h2>";
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