<?php
require_once "UserClass.php";
require_once "Notification.php";
require_once "FriendRequest.php";
require_once "ProfileClass.php";
require_once "Posts.php";
require_once "Message.php";
require_once "Group.php";
require_once "Group-request.php";
require_once "Alert.php";
require_once "Event.php";
require_once "Report.php";

function usePDO()
{
    return $pdo = new PDO("mysql:host=localhost;dbname=bazadoprojektu","root");
}
$pdo = usePDO();
try{
    $tableUserCreateSQL = file_get_contents("textfiles/databasetablecreation.txt");
    $addAdminUser = $pdo->prepare("INSERT INTO user(userID,userFirstName,userLastName,userEmail,userDateOfBirth,userPassword,userUsername,userProfile) VALUES (?,?,?,?,?,?,?,?); INSERT INTO adminusers(UserID) VALUES (?);");
    if($pdo->query($tableUserCreateSQL)){
        echo "<p>Tabela została stworzona</p>";
    }
    $tryUserAdmin = $pdo->query("SELECT userID FROM user WHERE userID=1");
    if(!$tryUserAdmin->fetch()){
        $addAdminUser->execute(array(1,"-","-","-","-",password_hash("admin",PASSWORD_DEFAULT),"admin","-",1));
        echo "<p>Konto administratora zostało dodane</p>";
    }
} catch (PDOException $e){}
function getArrayOfUsersIDs()
{
    $pdo = usePDO();
    $userGetSQL = $pdo->query("SELECT userID FROM user;");
    $new_array = array();
    foreach($userGetSQL->fetchAll(3) as $arr){
        if(!empty($arr)){
            foreach($arr as $a){
                array_push($new_array, $a);
            }
        }
    }
    return $new_array;
}
function getArrayOfUsers()
{
    $pdo = usePDO();
    $userGetSQL = $pdo->query("SELECT userUsername,userEmail,userDateOfBirth,userPassword,userFirstName,userLastName FROM user;");
   $constrArrays = $userGetSQL->fetchAll(PDO::FETCH_ASSOC);
   $objArr = array();
   foreach ($constrArrays as $array){
       $objArr[] = new User($array["userUsername"],$array["userEmail"],$array["userDateOfBirth"],$array["userPassword"],$array["userFirstName"],$array["userLastName"]);
   }
   return $objArr;
}
function getProfileID()
{
    $pdo = usePDO();
    $userId = $_SESSION['loggedIn'];
    if($userId != "") {
        $arr = $pdo->query("SELECT profile.ProfileID FROM user INNER JOIN profile ON profile.ProfileID=user.userProfile WHERE userID = $userId;");
        return @$arr->fetch(3)[0];
    }
}
function getProfileIDs()
{
    $pdo = usePDO();
    $arr = $pdo->query("SELECT ProfileID FROM profile");
    $new_array = array();
    foreach($arr->fetchAll(3) as $array) {
        if (!empty($array)) {
            foreach ($array as $a) {
                array_push($new_array, $a);
            }
        }
    }
    return $new_array;
}
function getUserObject()
{
    $pdo = usePDO();
    $userId = $_SESSION['loggedIn'];
    $userGetSQL = $pdo->query("SELECT userUsername,userEmail,userDateOfBirth,userPassword,userFirstName,userLastName FROM user WHERE userID = $userId;");
    $arr = $userGetSQL->fetch(PDO::FETCH_ASSOC);
    $objArr = new User($arr["userUsername"], $arr["userEmail"], $arr["userDateOfBirth"], $arr["userPassword"], $arr["userFirstName"], $arr["userLastName"]);
    return $objArr;
}
function getProfileObject($profID)
{
    $pdo = usePDO();
    $arr = $pdo->query("SELECT profileShowBirthDate,profileShowAddress,profileSeeProfile,profileAddress FROM profile WHERE ProfileID = $profID;");
    $profile = $arr->fetch(3);
    return new Profile($profile[0],$profile[1],$profile[2],$profile[3]);
}
function getProfileDescription($profID)
{
    $pdo = usePDO();
    $arr = $pdo->query("SELECT profileDescription FROM profile WHERE ProfileID = $profID;");
    return $arr->fetch(PDO::FETCH_NUM)[0];
}
function setProfileDescription($description)
{
    $pdo = usePDO();
    $profileID = getProfileID();
    $updateDesc = $pdo->prepare("UPDATE profile SET ProfileDescription=? WHERE ProfileID=?");
    $updateDesc->execute(array($description,$profileID));
}
function setUserObject($userName,$userLastName,$userBirthDate,$userPassword)
{
    $pdo = usePDO();
    $userId = $_SESSION['loggedIn'];
    try{
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();
        $updateUser = $pdo->prepare("UPDATE user SET userFirstName=?,userLastName=?, userDateOfBirth=?,userPassword=? WHERE user.userID=?;");
        $updateUser->execute(array($userName,$userLastName,$userBirthDate,$userPassword,$userId));
        $pdo->commit();
    }catch (Exception $error){
        $pdo->rollBack();
        echo "Nie udało się zmienić danych: ".$error->getMessage();
    }
}
function setProfileObject($profileShowBirth,$profileShowAddress,$profileAddress,$seeProfile)
{
    $pdo = usePDO();
    $profileID = $_GET['profileID'];
    $updateUser = $pdo->prepare("UPDATE profile SET profileShowBirthDate=?,profileShowAddress=?,profileAddress=?,profileSeeProfile=? WHERE profile.ProfileID = ?;");
    $updateUser->execute(array($profileShowBirth,$profileShowAddress,$profileAddress,$seeProfile    ,$profileID));
}
function getProfileOwnerID()
{
    $pdo = usePDO();
    $profileID = $_GET['profileID'];
    $getProfileOwner = $pdo->prepare("SELECT userID FROM user INNER JOIN profile ON profile.ProfileID=user.userProfile WHERE profile.ProfileID=?");
        $getProfileOwner->execute(array($profileID));
        return @$getProfileOwner->fetch(3)[0];
}
function getUserObjectFromID($id)
{
    $pdo = usePDO();
    $userId = $id;
    $userGetSQL = $pdo->query("SELECT userUsername,userEmail,userDateOfBirth,userPassword,userFirstName,userLastName FROM user WHERE userID = $userId;");
    if($userGetSQL){
        $arr = $userGetSQL->fetch(PDO::FETCH_ASSOC);
        $objArr = new User($arr["userUsername"], $arr["userEmail"], $arr["userDateOfBirth"], $arr["userPassword"], $arr["userFirstName"], $arr["userLastName"]);
        return $objArr;
    }else return false;
}
function getUserFriends($id){
    $pdo = usePDO();
    if($id != ""){
        $query = $pdo->query("SELECT userFriends FROM user WHERE userID=$id;");
        return explode(",",$query->fetch(3)[0]);
    }

}
function checkFriendRequestSides($senderID,$recieverID)
{
    $pdo = usePDO();
    if($senderID != ""){
        $check1 = $pdo->query("SELECT notificationID FROM notification WHERE (FromFirstUserID=$senderID OR FromFirstUserID=$recieverID) AND (ToSecondUserID=$recieverID OR ToSecondUserID=$senderID) AND NotificationType='friend-request' AND NotificationStatus!=4;");
        return (@$check1->fetch(3)[0] == "") ? true : false;
    }
}
function getNotificationOfUserID($userID)
{
    $pdo = usePDO();
    $notifications = array();
    $notificationsArray = $pdo->query("SELECT * FROM notification WHERE NotificationStatus!=4 AND ToSecondUserID=$userID;")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($notificationsArray as $array){
        if($array['NotificationType'] === "message"){
            $notifications[] = new Message($array['notificationID'],
                $array['FromFirstUserID'],
                $array['ToSecondUserID'],
                $array['NotificationDate'],
                $array['NotificationMessage'],
                $array['NotificationStatus']);
        }
        if($array['NotificationType'] === "friend-request"){
            $notifications[] = new FriendRequest(
                $array['notificationID'],
                $array['FromFirstUserID'],
                $array['ToSecondUserID'],
                $array['NotificationDate'],
                $array['NotificationStatus'],
                $array['NotificationMessage']
            );
        }
        if($array['NotificationType'] === "group-request"){
            $notifications[] = new GroupRequest(
                $array['notificationID'],
                $array['FromFirstUserID'],
                $array['ToSecondUserID'],
                $array['NotificationDate'],
                $array['NotificationStatus'],
                $array['NotificationMessage']
            );
        }
        if($array['NotificationType'] === "alert"){
            $notifications[] = new Alert(
                $array['notificationID'],
                $array['FromFirstUserID'],
                $array['ToSecondUserID'],
                $array['NotificationDate'],
                $array['NotificationStatus'],
                $array['NotificationMessage']
            );
        }
    }
    return $notifications;
}
function getPostObject($postID)
{
    $pdo = usePDO();
    $getObjectFragments = $pdo->query("SELECT PostSender,PostTitle,PostContents,PostDateOfPublication,PostLikes,PostEdited FROM posts WHERE PostID=$postID");
    $fragments = $getObjectFragments->fetch(PDO::FETCH_NUM);
    return new Posts($fragments[0],$fragments[1],$fragments[2],strtotime($fragments[3]),$fragments[4],$fragments[5]);
}
function getAllPosts()
{
    $pdo = usePDO();
    $getAllPostsID = $pdo->query("SELECT PostID FROM posts;");
    $array = array_reverse($getAllPostsID->fetchAll(3));
    $postsArray = array();
        foreach($array as $arr) {
            if (!empty($arr)) {
                foreach ($arr as $a) {
                    $postsArray[] = $a;
                }
            }
        }
    return $postsArray;
}
function getUserProfileIDFromID($id)
{
    $pdo = usePDO();
    $getProfileID = $pdo->query("SELECT userProfile FROM user WHERE userID=$id;");
    return $getProfileID->fetch(3)[0];
}
function addLike($postID){
    $pdo = usePDO();
    $userID = $_SESSION['loggedIn'];
    $pdo->query("INSERT INTO likes(PostLiked,LikedBy) VALUES ($postID,$userID);");
    if($likes = $pdo->query("SELECT COUNT(*) FROM Likes WHERE PostLiked=$postID;")){
        $likes  = $likes->fetch(3)[0];
    }else $likes = 1;
    $pdo->query("UPDATE posts SET PostLikes = $likes WHERE PostID = $postID;");
}
function removeLike($postID){
    $pdo = usePDO();
    $userID = $_SESSION['loggedIn'];
    $pdo->query("DELETE FROM likes WHERE PostLiked=$postID AND LikedBy=$userID;");
    if($likes = $pdo->query("SELECT COUNT(*) FROM Likes WHERE PostLiked=$postID;")){
        $likes  = $likes->fetch(3)[0];
    }else $likes = 0;
    $pdo->query("UPDATE posts SET PostLikes = $likes WHERE PostID = $postID;");
}
function getLikesOfPost($postID)
{
    $pdo = usePDO();
    $queryOfLikes = $pdo->query("SELECT LikedBy FROM likes WHERE PostLiked = $postID;");
    $likesArray = array();
    foreach($queryOfLikes->fetchAll(PDO::FETCH_ASSOC) as $arr) {
        if (!empty($arr)) {
            foreach ($arr as $a) {
                $likesArray[] = $a;
            }
        }
    }
    return $likesArray;
}
function getCommentContents($commID)
{
    $pdo = usePDO();
    $queryResault = $pdo->query("SELECT CommentContents FROM comments WHERE CommentID=$commID;");
    return $queryResault->fetch(3)[0];
}
function getPostComments($postID)
{
    $pdo = usePDO();
    $comments = $pdo->query("SELECT * FROM comments WHERE CommentedPost = $postID");
    return $comments->fetchAll(PDO::FETCH_ASSOC);
}
function getArrayOfAdminUsers()
{
    $pdo = usePDO();
    $adminQuery  = $pdo->query("SELECT userID FROM adminusers;");
    $adminArray = array();
    foreach($adminQuery->fetchAll(PDO::FETCH_ASSOC) as $arr) {
        if (!empty($arr)) {
            foreach ($arr as $a) {
                $adminArray[] = $a;
            }
        }
    }
    return $adminArray;
}
function getBlockedUsers()
{
    $pdo = usePDO();
    $blockedQuery  = $pdo->query("SELECT BlockedUser FROM blockedusers;");
    $blockedArray = array();
    foreach($blockedQuery->fetchAll(PDO::FETCH_ASSOC) as $arr) {
        if (!empty($arr)) {
            foreach ($arr as $a) {
                $blockedArray[] = $a;
            }
        }
    }
    return $blockedArray;
}
function addToNotificationsTable($userID,$toUserID,$notificationMessage)
{
    $pdo = usePDO();
    $addQuery = $pdo->prepare("INSERT INTO notification(FromFirstUserID,ToSecondUserID,NotificationType,NotificationMessage,NotificationStatus,NotificationDate) 
        VALUES (?,?,'friend-request',?,1,?);");
    date_default_timezone_set("Europe/Warsaw");
    $date = date("Y-m-d H:i:s");
    $addQuery->execute(array($userID,$toUserID,$notificationMessage,$date));
    $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Zaproszenie zostało wysłane!</p>";
}

function sendMessage($userID,$toUserID,$message)
{
    $pdo = usePDO();
    $sendMessage = $pdo->prepare("INSERT INTO notification(FromFirstUserID,ToSecondUserID,NotificationType,NotificationMessage,NotificationStatus,NotificationDate) 
        VALUES (?,?,'message',?,1,?);");
    date_default_timezone_set("Europe/Warsaw");
    $date = date("Y-m-d H:i:s");
    $sendMessage->execute(array($userID,$toUserID,$message,$date));
    $_SESSION['message'] = "<p style='float: left; font-size: medium; color: greenyellow'>Wiadomość wysłana!</p>";
}

function changeMessageRead($messageID)
{
    $pdo = usePDO();
    $pdo->query("UPDATE notification SET NotificationStatus=2 WHERE notificationID=$messageID;");
}

function getReadMessagesFromSomeone($userID)
{
    $pdo = usePDO();
    $messagesQuery = $pdo->query("SELECT * FROM notification WHERE ToSecondUserID=$userID AND NotificationStatus=2 AND NotificationType='message';");
    $objArray = array();
    foreach ($messagesQuery->fetchAll(PDO::FETCH_ASSOC) as $array){
        $objArray[] = new Message($array['notificationID'],
            $array['FromFirstUserID'],
            $array['ToSecondUserID'],
            $array['NotificationDate'],
            $array['NotificationMessage'],
            $array['NotificationStatus']);
    }
    return $objArray;
}
function getReadMessagesFromYou($userID)
{
    $pdo = usePDO();
    $messagesQuery = $pdo->query("SELECT * FROM notification WHERE FromFirstUserID=$userID AND NotificationStatus=2 AND NotificationType='message';");
    $objArray = array();
    foreach ($messagesQuery->fetchAll(PDO::FETCH_ASSOC) as $array){
        $objArray[] = new Message($array['notificationID'],
            $array['FromFirstUserID'],
            $array['ToSecondUserID'],
            $array['NotificationDate'],
            $array['NotificationMessage'],
            $array['NotificationStatus']);
    }
    return $objArray;
}

function getUserGroups($userID)
{
    $pdo = usePDO();
    $getGroupsQuery = $pdo->query("SELECT usergroups.groupID,usergroups.groupCreator,usergroups.groupName,usergroups.groupDateOfCreation FROM usergroups INNER JOIN groupmembers ON usergroups.groupID=groupmembers.groupID WHERE groupmembers.groupMember=$userID;");
    $groups = array();
    foreach($getGroupsQuery->fetchAll(PDO::FETCH_ASSOC) as $arr) {
         $groups[] = new Group($arr['groupID'],
         $arr['groupCreator'],
         $arr['groupName'],
         $arr['groupDateOfCreation']);
        }
    return $groups;
}

function getAllGroupsNames()
{
    $pdo = usePDO();
    $getGroupsQuery = $pdo->query("SELECT usergroups.groupName FROM usergroups;");
    $groupNames = array();
    foreach($getGroupsQuery->fetchAll(PDO::FETCH_ASSOC) as $arr) {
        $groupNames[] = $arr['groupName'];
    }
    return $groupNames;
}

function createGroup($creator,$groupName)
{
    $pdo = usePDO();
    $addGroup = $pdo->prepare("INSERT INTO usergroups(groupCreator,groupName,groupDateOfCreation) VALUES (?,?,?);");
    date_default_timezone_set("Europe/Warsaw");
    $date = date("Y-m-d H:i:s");
    $addGroup->execute(array($creator,$groupName,$date));
    $groupID = $pdo->lastInsertId();
    $addToGroup = $pdo->prepare("INSERT INTO groupmembers(groupID,groupMember) VALUES (?,?);");
    $addToGroup->execute(array($groupID,$creator));
}

function getGroupFromID($groupID)
{
    $pdo = usePDO();
    $array = $pdo->query("SELECT * FROM usergroups WHERE groupID=$groupID;")->fetch(PDO::FETCH_ASSOC);
    return new Group($array['groupID'],
    $array['groupCreator'],
    $array['groupName'],
    $array['groupDateOfCreation']);
}
function getGroupMembers($groupID)
{
    $pdo = usePDO();
    $members = $pdo->query("SELECT groupMember FROM groupmembers WHERE groupID = $groupID;");
    $membersArray = array();
        foreach($members->fetchAll(PDO::FETCH_ASSOC) as $arr) {
            if (!empty($arr)) {
                foreach ($arr as $a) {
                    $membersArray[] = $a;
                }
            }
        }
    return $membersArray;
}
function leaveGroup($groupID,$userID)
{
    $pdo = usePDO();
    $leaveGroup = $pdo->prepare("DELETE FROM groupmembers WHERE groupID=? AND groupMember=?");
    $leaveGroup->execute(array($groupID,$userID));
}
function sendNotification($fromUserID,$toUserID,$message)
{
    $pdo = usePDO();
    $sendNotification = $pdo->prepare("INSERT INTO notification(FromFirstUserID,ToSecondUserID,NotificationType,NotificationMessage,NotificationDate,NotificationStatus) VALUES (?,?,'alert',?,?,?);");
    date_default_timezone_set("Europe/Warsaw");
    $date = date("Y-m-d H:i:s");
    $sendNotification->execute(array($fromUserID,$toUserID,$message,$date,1));
}
function deleteGroup($groupID)
{
    $group = getGroupFromID($groupID);
    print_r($group);
    $pdo = usePDO();
    $usersOfGroup = getGroupMembers($groupID);
    foreach ($usersOfGroup as $key){
        sendNotification($group->groupCreatorID,$key,"Grupa \"$group->groupName\" została usunięta, nie jesteś już jej członkiem");
    }
    $pdo->query("DELETE FROM usergroups WHERE groupID=$groupID;");
}
function acceptAlert($notificationID)
{
 $pdo = usePDO();
 $updateAlert = $pdo->prepare("UPDATE notification SET NotificationStatus=4 WHERE notificationID=?;");
 $updateAlert->execute(array($notificationID));
}
function sendGroupInvitation($groupID,$newUserID)
{
    $pdo = usePDO();
        $group = getGroupFromID($groupID);
        $invitation = $pdo->prepare("INSERT INTO notification(FromFirstUserID,
                         ToSecondUserID,
                         NotificationType,
                         NotificationMessage,
                         NotificationStatus,
                         NotificationDate) VALUES (?,?,'group-request',?,1,?);");
        date_default_timezone_set("Europe/Warsaw");
        $date = date("Y-m-d H:i:s");
        $invitation->execute(array($groupID,$newUserID,"Zaproszenie do grupy: $group->groupName",$date,));
}
function createEvent($groupID,$creatorID,$eventName,$eventDateOfExpiary,$eventDescription)
{
$pdo = usePDO();
$createEvent = $pdo->prepare("INSERT INTO events(eventGroupID,eventCreator,eventName,eventDateOfExpiary,eventDescription) VALUES (?,?,?,?,?);");
$createEvent->execute(array($groupID,$creatorID,$eventName,$eventDateOfExpiary,$eventDescription));
}
function getGroupEvents($groupID)
{
    $pdo = usePDO();
    $eventQuery = $pdo->query("SELECT * FROM events WHERE eventGroupID=$groupID;");
    $eventsArray = array();
        foreach ($eventQuery->fetchAll(PDO::FETCH_ASSOC) as $array){
            $eventsArray[] = new Event($array['eventID'],
            $array['eventGroupID'],
            $array['eventCreator'],
            $array['eventName'],
            $array['eventDateOfExpiary'],
            $array['eventDescription']);
    }
    return $eventsArray;
}
function getEventParticipants($eventID)
{
$pdo = usePDO();
$participantsQuery = $pdo->query("SELECT userJoined FROM eventjoined WHERE eventID=$eventID;");
    $participants = array();
    foreach($participantsQuery->fetchAll(PDO::FETCH_ASSOC) as $arr) {
        if (!empty($arr)) {
            foreach ($arr as $a) {
                $participants[] = $a;
            }
        }
    }
    return $participants;
}
function joinEvent($eventID)
{
    $pdo = usePDO();
    $userID = $_SESSION['loggedIn'];
    $pdo->query("INSERT INTO eventjoined(eventID,userJoined) VALUES ($eventID,$userID);");
}
function leaveEvent($eventID)
{
    $pdo = usePDO();
    $userID = $_SESSION['loggedIn'];
    echo $userID;
    $pdo->query("DELETE FROM eventjoined WHERE eventID=$eventID AND userJoined=$userID;");
}
function getAllEvents()
{
    $pdo = usePDO();
    $getAllEvents = $pdo->query("SELECT * FROM events;");
    $eventsArray = array();
    foreach ($getAllEvents->fetchAll(PDO::FETCH_ASSOC) as $array){
        $eventsArray[] = new Event($array['eventID'],
            $array['eventGroupID'],
            $array['eventCreator'],
            $array['eventName'],
            $array['eventDateOfExpiary'],
            $array['eventDescription']);
    }
    return $eventsArray;
}
function setEventPassed($eventID)
{
    $pdo = usePDO();
    $pdo->query("UPDATE events SET eventStatus=2 WHERE eventID=$eventID");
}
function setEventActive($eventID)
{
    $pdo = usePDO();
    $pdo->query("UPDATE events SET eventStatus=null WHERE eventID=$eventID");
}
function getEventStatus($eventID)
{
    $pdo = usePDO();
    return @$pdo->query("SELECT eventStatus FROM events WHERE eventID=$eventID;")->fetch(3)[0];
}
function eventEdit($eventID,$eventName,$eventDateOfExpiary,$eventDescription)
{
    $pdo = usePDO();
    $updateEvent = $pdo->prepare("UPDATE events SET eventName=?,eventDateOfExpiary=?,eventDescription=? WHERE eventID=$eventID;");
    $updateEvent->execute(array($eventName,$eventDateOfExpiary,$eventDescription));
}
function getEventObjectFromID($eventID)
{
    $pdo = usePDO();
    $eventQuery = $pdo->query("SELECT * FROM events WHERE eventID=$eventID;");
    $array = $eventQuery->fetch(PDO::FETCH_ASSOC);
    return new Event($array['eventID'],
        $array['eventGroupID'],
        $array['eventCreator'],
        $array['eventName'],
        $array['eventDateOfExpiary'],
        $array['eventDescription']);
}
function deleteEvent($eventID)
{
    $pdo = usePDO();
    $eventObj = getEventObjectFromID($eventID);
    $eventParticipants = getEventParticipants($eventID);
    foreach ($eventParticipants as $value){
        sendNotification($eventObj->eventCreator,$value,"Wydarzenie zostało anulowane");
    }
    $pdo->query("DELETE FROM eventjoined WHERE eventID=$eventID;");
    $pdo->query("DELETE FROM events WHERE eventID=$eventID");
}
function kickUserFromEvent($eventID,$userID)
{
    $pdo = usePDO();
    $removeUser = $pdo->prepare("DELETE FROM eventjoined WHERE eventID=? AND userJoined=?;");
    $removeUser->execute(array($eventID,$userID));
}
function getAllReports()
{
    $pdo = usePDO();
    $reportsQuerry = $pdo->query("SELECT * FROM reports WHERE ReportStatus IS NULL OR ReportStatus=1;");
    $reports = array();
    foreach ($reportsQuerry->fetchAll(PDO::FETCH_ASSOC) as $array){
     $reports[] = new Report($array['reportID'],
     $array['ReportedUser'],
     $array['ReportingUser'],
     $array['ReportDate'],
     $array['ReportType'],
     $array['ReportDescription']);
    }
    return $reports;
}
function getReportObjectFromID($reportID)
{
    $pdo = usePDO();
    $reportsQuerry = $pdo->query("SELECT * FROM reports WHERE reportID=$reportID");
    $array = $reportsQuerry->fetch(PDO::FETCH_ASSOC);
    return new Report($array['reportID'],
        $array['ReportedUser'],
        $array['ReportingUser'],
        $array['ReportDate'],
        $array['ReportType'],
        $array['ReportDescription']);
}
function blockUser($userID,$reason)
{
    $pdo = usePDO();
    $blockQuery = $pdo->prepare("INSERT INTO blockedusers(BlockedUser, BlockReason) VALUES (?,?);");
    $blockQuery->execute(array($userID,$reason));
}
function changeReportStatus($reportID,$status)
{
    $pdo = usePDO();
    $report = getReportObjectFromID($reportID);
    $changeReport = $pdo->prepare("UPDATE reports SET ReportStatus=? WHERE reportID=$reportID;");
    $changeReport->execute(array($status));
}
function reportUser($userID,$reportingUser,$reportDate,$reportType,$reportDescription)
{
    $pdo = usePDO();
    $reportUser = $pdo->prepare("INSERT INTO reports(reporteduser,ReportingUser , reportdate, reporttype, reportdescription) VALUES (?,?,?,?,?);");
    $reportUser->execute(array($userID,$reportingUser,$reportDate,$reportType,$reportDescription));
}

function checkIfUserReportedUser($reportingUser,$reportedUser)
{
    $pdo = usePDO();
    if($pdo->query("SELECT COUNT(*) FROM reports WHERE ReportingUser=$reportingUser AND ReportedUser=$reportedUser AND ReportStatus!=4;")->fetch(3)[0] == 0){
        return true;
    }else return false;
}

include_once "eventHandler.php";
?>