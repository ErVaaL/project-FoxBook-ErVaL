<?php require_once "fordatabaseconnection.php";session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Dodawanie posta</title>
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
    $postIDs= getAllPosts();
    $usersArray = getArrayOfUsersIDs();
    if(@$_SESSION['loggedIn'] != null && @in_array($_SESSION['loggedIn'],$usersArray)){
        if(isset($_SESSION['postEdit']) && in_array($_SESSION['postEdit'],$postIDs)){
        $post = getPostObject($_SESSION['postEdit']);
        echo "<form method='post' class='center'>
        </br>
        <label for='postTitle'>Nagłówek posta </label></br></br>
        <input type='text' name='postTitle' id='postTitle' placeholder='Wpisz nagłówek' class='form-inputs' value='$post->postTitle'></br></br>
        <label for='postContents'>Treść</label></br></br>
        <textarea name='postContents' id='postContents' placeholder='Tutaj wpisz treść' style='width: 500px;height: 200px;resize: none'>" .$post->postContents. "</textarea></br></br>
        <input type='submit' name='editPost' id='editPost' value='Opublikuj' class='form-submit-button'>&nbsp;<input type='submit' name='goBack' id='goBack' value='Powrót' class='form-submit-button'>
    </form>";
            if(isset($_POST['goBack'])){
                unset($_SESSION['postEdit']);
                header("Location: foxbookhomepage.php");
            }
            $pdo = usePDO();
            if (isset($_POST['editPost'])) {
            $id = $_SESSION['postEdit'];
            if (trim($_POST['postTitle']) != "" && strlen($_POST['postTitle']) <= 50) {
                if (strlen($_POST['postContents']) <= 500 && trim($_POST['postContents']) != "") {
                    $addPost = $pdo->prepare("UPDATE posts SET PostTitle=?,PostContents=?,PostEdited=1 WHERE PostID=$id;");
                    $title = htmlspecialchars($_POST['postTitle']);
                    $content = htmlspecialchars($_POST['postContents']);
                    $addPost->execute(array($title, $content));
                    unset($_SESSION['postEdit']);
                    $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Post został zmieniony!</p>";
                    header("Location: foxbookhomepage.php");
                } else echo "<p class='wrong'>Musisz dodać treść posta o maksymalnej ilości 500 znaków</p>";
            } else {
                echo "<p class='wrong'>Musisz podać tytuł o maksymalnej długości 50 znaków</p>";
            }
        }
    }else{ ?>
            <form method="post" class="center">
                </br>
                <label for="postTitle">Nagłówek posta </label></br></br>
                <input type="text" name="postTitle" id="postTitle" placeholder="Wpisz nagłówek" class="form-inputs"></br></br>
                <label for="postContents">Treść</label></br></br>
                <textarea name="postContents" id="postContents" placeholder="Tutaj wpisz treść" style="width: 500px;height: 200px;resize: none"></textarea></br></br>
                <input type="submit" name="publishPost" id="publishPost" value="Opublikuj" class="form-submit-button">&nbsp;<input type='submit' name='goBack' id='goBack' value='Powrót' class='form-submit-button'>
            </form>
            <?php
            if(isset($_POST['goBack'])){
                unset($_SESSION['postEdit']);
                header("Location: foxbookhomepage.php");
            }
            $pdo = usePDO();
            if(isset($_POST['publishPost'])){
                if(trim($_POST['postTitle']) != "" && strlen($_POST['postTitle']) <= 50){
                    if(strlen($_POST['postContents']) <= 500 && trim($_POST['postContents']) != ""){
                        $addPost = $pdo->prepare("INSERT INTO posts(PostSender,PostTitle,PostContents,PostDateOfPublication,PostEdited) VALUES (?,?,?,?,0);");
                        $title = htmlspecialchars($_POST['postTitle']);
                        $content = htmlspecialchars($_POST['postContents']);
                        $userID = $_SESSION['loggedIn'];
                        date_default_timezone_set("Europe/Warsaw");
                        $date = date("Y-m-d H:i:s");
                        $addPost->execute(array($userID,$title,$content,$date));
                        $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Post został dodany!</p>";
                        header("Location: foxbookhomepage.php");
                    }else echo "<p class='wrong'>Musisz dodać treść posta o maksymalnej ilości 500 znaków</p>";
                }else{
                    echo "<p class='wrong'>Musisz podać tytuł o maksymalnej długości 50 znaków</p>";
                }
            }
        }
    }else echo "</br><h2 class='center'>By dodać post, musisz być zalogowany/a</h2></br>
<form method='post' action='foxbookhomepage.php' class='center'><input type='submit' value='Powrót do strony głównej' class='form-submit-button'></form>";
    ?>
</div>
<script>
    if( window.history.replaceState){
        window.history.replaceState(null, null, window.location.href);
    }
</script>
</body>
</html>