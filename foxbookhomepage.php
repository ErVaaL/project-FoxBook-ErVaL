<?php require_once "fordatabaseconnection.php";session_start();ob_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>FoxBook</title>
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
        echo "<a href='foxbookhomepage.php' class='textLink' style='font-size: xxx-large;color: white'>FoxBook</a>";
        ?>
    </div>
    <div id="headerChooseMotive">
        <?php require "foxbookmotivescript.php";
        if(isset($_SESSION['message'])){
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        } ?>
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
    <?php
    $usersArray = getArrayOfUsersIDs();
    if(@$_SESSION['loggedIn'] != null && @in_array($_SESSION['loggedIn'],$usersArray)){
        if(!in_array($_SESSION['loggedIn'],getBlockedUsers())){
    ?>
    <form method="post" class="center" action="foxbookpostcreation.php"></br>
    <input type="submit" name="addPost" id="addPost" value="Dodaj nowy post" class="form-submit-button" style="width: 80%; height: 50px; font-size: xx-large; font-family: Verdana" >
    </form></br></br>
    <?php
        }else echo "</br><h2 class='center'>Zostałeś/aś zbanowany/a, Twoje możliwości są ograniczone</h2>";
    }
    ?>
    </br>
    <div id="postsSpace">
    <?php
    $pdo = usePDO();
    echo "</br>";
        $postsArray = getAllPosts();
        foreach ($postsArray as $key){
            $post = getPostObject($key);
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
                echo "<form method='post' style='float: right;'><input type='hidden' name='postID' value='$key'><input type='submit' name='editPost' id='editPost' value='Edytuj' class='form-submit-button'>&nbsp;<input type='submit' value='Usuń' name='deletePost' id='deletePost' class='form-submit-button'></form></br>";
            }
            if($post->postLikes != 0){
                echo "<p style='margin-left: 2%; color: dimgray'>$post->postLikes polubień</p>";
            }
            echo "<form method='post' style='float: right; margin-top: -3.5%;margin-right: 1%'><input type='hidden' name='postID' value='$key'><input type='submit' name='showComments' id='showComments' value='Pokaż komentarze' class='form-submit-button'></form>";
            if(@$_SESSION['loggedIn'] != null && @in_array($_SESSION['loggedIn'],$usersArray)){
                if(!in_array($_SESSION['loggedIn'],getBlockedUsers())){
                if(!in_array($_SESSION['loggedIn'], getLikesOfPost($key))){
                    echo "<form method='post'><input type='hidden' name='postID' value='$key'><input type='submit' name='likePost' id='likePost' value='Lubię to!' class='form-submit-button'></form></br>";
                }else{
                    echo "<form method='post'><input type='hidden' name='postID' value='$key'><input type='submit' name='removeLike' id='removeLike' value='Cofnij polubienie' class='form-submit-button'></form></br>";
                }
                echo "<form method='post' style=''><input type='hidden' name='postID' id='postID' value='$key'><input type='submit' name='addComment' id='addComment' class='form-submit-button' value='Dodaj komentarz'></form>";
             }
            }
           echo "</div></br>";
        }
        if(isset($_POST['likePost'])){
            addLike($_POST['postID']);
            header("Location: ".$_SERVER['PHP_SELF']);
        }else if(isset($_POST['removeLike'])){
            removeLike($_POST['postID']);
            header("Location: ".$_SERVER['PHP_SELF']);
        }
        if(isset($_POST['addComment'])){
            $postID = $_POST['postID'];
            $_SESSION['commentAdding'] = $_POST['postID'];
            header("Location: addComments.php?postID=$postID");
        }
        if(isset($_POST['showComments'])){
            $postID = $_POST['postID'];
            header("Location: commentsToPost.php?postID=$postID");
        }
    if(isset($_POST['deletePost'])){
        $key = $_POST['postID'];
        $pdo->query("DELETE FROM posts WHERE PostID=$key;");
        header("Location: foxbookhomepage.php");
    }else if(isset($_POST['editPost'])){
        $_SESSION['postEdit'] = $_POST['postID'];
        header("Location: foxbookpostcreation.php");
    }
    ?>
    </div>
    <div id="groupSpace">
        <?php
        if(@$_SESSION['loggedIn'] != null && @in_array($_SESSION['loggedIn'],$usersArray)){
            if(!in_array($_SESSION['loggedIn'],getBlockedUsers())) {
                include_once "htmlfiles/groupForm.html";
            }
        ?>
        <h2 class="center">Twoje grupy:</h2>
            <table id="groupsTable" class="center">
                <?php
                if(!empty(getUserGroups($_SESSION['loggedIn']))){
                    $groupsArray = getUserGroups($_SESSION['loggedIn']);
                    $callGroups = function ($object){
                        echo "<tr><th><a href='foxbookgrouppage.php?groupID=$object->groupID' class='textLink'>$object->groupName</a></th></tr>";
                    };
                    array_walk($groupsArray,$callGroups);
                }else{
                    echo "<h4 class='center'>Nie należysz jeszcze do żadnej grupy</h4>";
                }
                }?>

            </table>
    </div>
</div>
<script>
    if( window.history.replaceState){
        window.history.replaceState(null, null, window.location.href);
    }
</script>
</body>
</html>