<?php
$title = "Notification | SN";
include_once('classes/DB.php');
include_once('classes/Login.php');
include_once('classes/Post.php');
include_once('assets/templates/header.php');
include_once('assets/templates/navbar.php');

if (Login::isLoggedin()) {
    $loggedinUserID = Login::isLoggedin();
} else {
    header("location:index.php");
    die;
}
echo "<div class='container notifications'>";
if(DB::query("SELECT * FROM notifications WHERE Receiver = '{$loggedinUserID}'")) {
    $notifications = DB::query("SELECT * FROM notifications WHERE Receiver = '{$loggedinUserID}' ORDER BY NotificationID DESC");
    foreach($notifications as $n) {
        $senderName = DB::query("SELECT Username FROM users WHERE UserID = '{$n['Sender']}'")[0]['Username'];
        switch ($n['Type']) {
            case 1:
                echo "<p>$senderName mentioned you in a post! ~ {$n['Extra']} </p>";
                break;
            case 2:
                echo "<p>$senderName like your post! </p>";
                break;
            default:
                echo "you got a notification";
                break;
        }
    }
} else {
    echo "no notifiy yet";
}
echo "</div>";
include_once('assets/templates/footer.php');
