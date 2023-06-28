<?php require_once "fordatabaseconnection.php"; session_start(); ob_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Komentarze</title>
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
<div id="sidebar-left">
    <h2 class="center">Lista znajomych</h2>
    <?php include_once "friendlistscript.php"?>
</div>
<div id="sidebar-right">
    <h2 class="center">Czaty</h2>
    <?php require_once "chatmessagelist.php";?>
</div>
<div id="center-body">
</br>
    <?php
    $usersArray = getArrayOfUsersIDs();
    $postID = $_GET['postID'];
    $post = getPostObject($_GET['postID']);
    $user = getUserObjectFromID($post->postSender);
    $profileID = getUserProfileIDFromID($post->postSender);
    $date = date("d.m.Y H:i", $post->postDate);
    if($post->postEdited){
        $postTitle = $post->postTitle."&nbsp;<span style='font-size: x-small; color: dimgray; font-weight: lighter'>  (edytowany)</span>";
    }else $postTitle = $post->postTitle;
    echo "<div id='postArea'>
                 </br>
                <a style='margin-bottom: 2px; color: dimgrey;' id='postContent' href='foxbookprofilepage.php?profileID=$profileID' class='textLink'>$user->userFirstName $user->userLastName</a><span style='color: dimgray;'>$date</span>
           
                <h3 id='postContent' style='margin-top: 2px'>$postTitle</h3>
                <p style='margin-top: 2px; word-wrap: break-word' id='postContent'>$post->postContents</p>";
    if($post->postSender === @$_SESSION['loggedIn']){
        echo "<form method='post' style='float: right; margin: 0;'><input type='hidden' name='postID' value='$postID'><input type='submit' name='editPost' id='editPost' value='Edytuj' class='form-submit-button'>&nbsp;<input type='submit' value='Usuń' name='deletePost' id='deletePost' class='form-submit-button'></form></br>";
    }
    if($post->postLikes != 0){
        echo "<p style='margin-left: 2%; color: dimgray'>$post->postLikes polubień</p>";
    }if(@$_SESSION['loggedIn'] != null && @in_array($_SESSION['loggedIn'],$usersArray)){
        if(!in_array($_SESSION['loggedIn'], getLikesOfPost($postID))){
            echo "<form method='post' style='float: left; margin-top: -2%; margin-left: 1%'><input type='hidden' name='postID' value='$postID'><input type='submit' name='likePost' id='likePost' value='Lubię to!' class='form-submit-button'></form></br>";
        }else{
            echo "<form method='post'><input type='hidden' name='postID' value='$postID'><input type='submit' name='removeLike' id='removeLike' value='Cofnij polubienie' class='form-submit-button'></form></br>";
        }
        echo "<form method='post' style=''><input type='hidden' name='postID' id='postID' value='$postID'>
<input type='submit' name='addComment' id='addComment' class='form-submit-button' value='Dodaj komentarz'>
<input type='submit' name='goBack' id='goBack' value='Powrót' class='form-submit-button' style='float: right'></form>";
    }
    echo "</div></br>";
    if(isset($_POST['likePost'])){
        addLike($_POST['postID']);
        header("Location: commentsToPost.php?postID=$postID");
    }else if(isset($_POST['removeLike'])){
        removeLike($_POST['postID']);
        header("Location: commentsToPost.php?postID=$postID");
    }
    if(isset($_POST['editPost'])){
        $_SESSION['postEdit'] = $postID;
        header("Location: foxbookpostcreation.php");
        die();
    }
    foreach (array_reverse(getPostComments($postID)) as $arrays){
        $user = getUserObjectFromID($arrays['CommentSender']);
        $contents = $arrays['CommentContents'];
        $commID = $arrays['CommentID'];
        $date = date("d.m.Y H:i",strtotime($arrays['CommentDate']));
        if($arrays['CommentEdited']){
            $date .= "&nbsp;<span style='font-size: x-small; color: dimgray; font-weight: lighter'>  (edytowany)</span>";
        }
        echo "</br><div id='postComment'>";
        echo "</br><a style='margin-bottom: 2px; color: dimgrey;' id='postContent' href='foxbookprofilepage.php?profileID=$profileID' class='textLink'>$user->userFirstName $user->userLastName</a><span style='color: dimgray;'>$date</span>";
        echo "</br></br>";
        echo "<p style='margin-top: 2px; word-wrap: break-word' id='postContent'>$contents</p>";
        if($arrays['CommentSender'] == @$_SESSION['loggedIn']){
            echo "<form method='post'><input type='hidden' name='commID' id='commID' value='$commID'>
        <input type='submit' name='editComm' id='editComm' value='Edytuj' class='form-submit-button'> 
        <input type='submit' name='deleteComm' id='deleteComm' value='Usuń' class='form-submit-button'>
        </form>";
        }
        echo "</div>";
    }
    if(isset($_POST['goBack'])){
        header("Location: foxbookhomepage.php");
    }
    if(isset($_POST['editComm'])){
        $_SESSION['commentEdit'] = $_POST['commID'];
        header("Location: addComments.php?postID=$postID");
    }else if(isset($_POST['deleteComm'])){
        $commIDToDel = $_POST['commID'];
        $pdo = usePDO();
        $pdo->query("DELETE FROM Comments WHERE CommentID=$commIDToDel;");
        $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Komentarz usunięty</p>";
        header("Location: commentsToPost.php?postID=$postID");
        die();
    }
    if(isset($_POST['addComment'])){
        unset($_SESSION['commentEdit']);
        $_SESSION['commentAdding'] = $_POST['postID'];
        header("Location: addComments.php?postID=$postID");
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