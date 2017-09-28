<?php
$title = "Message | SN";
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
echo "<div class='container messages'>";
$iFollow    = DB::query("SELECT users.Username, users.UserID FROM users, followers WHERE followers.FollowerID = '{$loggedinUserID}' AND users.UserID = followers.User_ID");
$myFollowers = DB::query("SELECT users.Username, users.UserID FROM users, followers WHERE followers.User_ID = '{$loggedinUserID}' AND users.UserID = followers.FollowerID");

$action = isset($_GET['action']) ? $_GET['action'] : "index";

if ($action == "index") {

    echo "<h3>People i follow</h3>";
        if($iFollow) {
            foreach($iFollow as $if) {
                echo "<a href='?action=user&id={$if['UserID']}'>{$if['Username']}</a><hr>";
            }
        } else {
            echo "you dont follow any one";
        }
    echo "<h3>People Following me</h3>";
    if($myFollowers) {
        foreach($myFollowers as $mf) {
            echo "<a href='?action=user&id={$mf['UserID']}'>{$mf['Username']}</a><hr>";
        }
    } else {
        echo "no one follow you";
    }

} elseif($action == "user") {

    $userid = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
    if(DB::query("SELECT Username FROM users WHERE UserID = '{$userid}'")) { // now u have a valid userid
        if(isset($_POST['send'])) {
            /*
            === filter_var vs htmlspecialchars_decode & str_replace
            */
            //$messagebody = filter_var($_POST['messagebody'], FILTER_SANITIZE_STRING);
            $messagebody = htmlspecialchars_decode(str_replace( "\\", "", $_POST['messagebody']));
            if(!empty($messagebody)) {
                DB::query("INSERT INTO messages (Sender, Receiver, MessageBody) VALUES (:sender, :receiver, :messagebody)", array(":sender" => $loggedinUserID, ":receiver" => $userid, ":messagebody" => $messagebody));
                echo "you send a message!";
            }
        }

        if (DB::query("SELECT * FROM messages WHERE (Sender = {$loggedinUserID} AND Receiver = {$userid}) OR (Sender = {$userid} AND Receiver = {$loggedinUserID})")) { // check if had a message or not
            $messages = DB::query("SELECT * FROM messages WHERE (Sender = {$loggedinUserID} AND Receiver = {$userid}) OR (Sender = {$userid} AND Receiver = {$loggedinUserID})");
                foreach($messages as $message) {
                    echo "<p>". nl2br($message['MessageBody']) . "</p><hr>";
                }
        } else {
            echo "<p>no messages yet.</p>";
        }
    ?>
            ==================
            <form action="?action=user&id=<?= $userid ?>" method="POST">
                <textarea name="messagebody"></textarea>
                <input type="submit" value="Send" name="send" />
            </form>
    <?php
    } else {
        die("no user found!");
    }

} else {
    die("invalid url");
}
echo "</div>";
include_once('assets/templates/footer.php');
