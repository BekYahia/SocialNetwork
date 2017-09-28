<?php
$title = "Topics | SN";
include_once('classes/DB.php');
include_once('classes/Login.php');
include_once('classes/Post.php');
include_once('assets/templates/header.php');
include_once('assets/templates/navbar.php');
echo "<div class='container topics'>";
if(isset($_GET['topic'])) {

    $topic = $_GET['topic'];

    if(DB::query("SELECT * FROM posts WHERE Topics LIKE '%$topic%'")) {
        $posts = DB::query("SELECT * FROM posts WHERE Topics LIKE '%$topic%'");
        foreach($posts as $post) {
            echo "<div>{$post['PostBody']}</div>";
        }
    } else {
        die("no topic found");
    }

}
echo "</div>";
include_once('assets/templates/footer.php');
