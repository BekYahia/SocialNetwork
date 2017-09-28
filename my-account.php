<?php
/*
===============
== Suggest to deprecate; actually Profile.php?username={myUsername} is quite enough.
*/
include_once('classes/DB.php');
include_once('classes/Login.php');
include_once('classes/Image.php');


if (Login::isLoggedin()) {
    $loggedinUserID     = Login::isLoggedin();
    $loggedinUser   = DB::query("SELECT Username FROM users WHERE UserID = '{$loggedinUserID}'")[0];
} else {
    header("location:index.php");
    die;
}

if(isset($_POST['profileimg'])) {
    Image::uploadImage($loggedinUser['Username']);
}

?>
<h2>My Acoount</h2>
<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
    <input type="file" name="img" />
    <input type="submit" value="Upload Image" name="profileimg" />
</form>
