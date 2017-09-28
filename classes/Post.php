<?php
class Post {

    public static function check_Mention_and_Topics($text, $topics_links = TRUE) {
        $text       = explode(" ", $text);
        $finalText  = "";
        $topics     = "";
        $notify     = array();

        foreach($text as $word) {

            if(substr($word, 0, 1) == "@") {
                $mentionedUser = substr($word, 1);
                if(DB::query("SELECT Username FROM users WHERE Username = '{$mentionedUser}'")) { // check if mentionedUser is in our databases;
                    $finalText .= "<a href='profile.php?username=$mentionedUser'>@". htmlspecialchars($mentionedUser) . "</a> ";
                    $notify[$mentionedUser] = array("type" => 1, "post" => htmlspecialchars(implode($text, " ")));
                } else {
                    $finalText .= htmlspecialchars($word); // return $word as text[@Username] not linked
                }

            } elseif(substr($word, 0, 1) == "#") {
                $topic = substr($word, 1);

                if ($topics_links == TRUE) {// prevent # from comment
                    $finalText .= "<a href='topics.php?topic=$topic'>#". htmlspecialchars($topic) . "</a> ";
                } else {
                    $finalText .= htmlspecialchars($word); // return $word as text[#bla_bla_bla] not linked
                }
                $topics .= htmlspecialchars($topic).",";

            } else {
                $finalText .= htmlspecialchars($word) . " ";
            }
        }
        return [$finalText, $topics, $notify]; // right trim to remove right space. (the post just 160 so, dont mess)
    }


public static function createPost($postbody, $loggedinUserID /*, $profileUserID*/) {
        /*if($loggedinUserID != $profileUserID) {
            die("Incorrect User");
        }*/
        if(strlen($postbody) > 170 || strlen($postbody) < 1) {
            http_response_code(409);
            die("Incorrect Length!");
        }

        $postbody_ = self::check_Mention_and_Topics($postbody)[0];
        $topics = rtrim(self::check_Mention_and_Topics($postbody)[1], ",");
        DB::query("INSERT INTO posts (PostBody, User_ID, Topics) VALUES(:postbody, :user_id, :topics)", array(":postbody" => $postbody_, ":user_id" => $loggedinUserID, ":topics" => $topics));

        if(!empty(self::check_Mention_and_Topics($postbody)[2])) {
            foreach(self::check_Mention_and_Topics($postbody)[2] as $r_username => $type) {
                $receiver = DB::query("SELECT UserID FROM users WHERE Username = '{$r_username}'")[0]['UserID'];
                DB::query("INSERT INTO notifications (Sender, Receiver, Type, Extra) VALUES (:sender, :receiver, :type, :extra)", array(":sender" => $loggedinUserID, ":receiver" => $receiver, ":type" => $type['type'], ":extra" => $type['post']));
            }
        }
    }

/*
    public static function likePost($postid, $liker) {
        if(!DB::query("SELECT Post_ID FROM post_likes WHERE Post_ID = ? AND User_ID = ?", array($postid, $liker))) {
            DB::query("UPDATE posts SET Likes = Likes + 1 WHERE PostID = '{$postid}'");
            DB::query("INSERT INTO post_likes (Post_ID, User_ID) VALUES (:post_id, :user_id)", array(":post_id" => $postid, ":user_id" => $liker));// $followerid = LoggedInUser AS in Following Model
            //notify
            $notify = DB::query("SELECT posts.User_ID AS Receiver, post_likes.User_ID AS Sender FROM posts, post_likes WHERE posts.PostID = post_likes.Post_ID AND PostID = '{$postid}'");
            DB::query("INSERT INTO notifications (Sender, Receiver, Type) VALUES (:sender, :receiver, 2)", array(":sender" => $notify[0]['Sender'], ":receiver" => $notify[0]['Receiver']));
        } else {
            DB::query("UPDATE posts SET Likes = Likes - 1 WHERE PostID = '{$postid}'");
            DB::query("DELETE FROM post_likes WHERE Post_ID = ? AND User_ID = ?", array($postid, $liker));// $followerid = LoggedInUser AS in Following Model
        }
    }*/


    public static function commentPost($commentbody, $loggedinUserID, $post_id) {
        if(strlen($commentbody) > 170 || strlen($commentbody) < 1) {
            http_response_code(409);
            die("Incorrect Length!");
        }
        $commentbody_ = self::check_Mention_and_Topics($commentbody, FALSE)[0];
        //$topics = self::check_Mention_and_Topics($commentbody, TRUE)[1]; // i dont want hashtag in comment
        DB::query("INSERT INTO comments (CommentBody, User_ID, Post_ID) VALUES (:commentbody, :user_id, :post_id)", array(":commentbody" => $commentbody_, ":user_id" => $loggedinUserID, ":post_id" => $post_id));
        echo "comment was published";
    }


    public static function displayPosts($select_posts_query, $QUERY_STRING_username, $loggedinUserID) {
        $posts = $select_posts_query;
        if(!empty($posts)) {
            foreach($posts as $post) {
                $inOrOut = DB::query("SELECT * FROM post_likes WHERE Post_ID = '{$post['PostID']}' AND User_ID = '{$loggedinUserID}'") ? 'youIn': 'youOut';
                $commentsNumber = DB::query("SELECT count(CommentID) FROM comments WHERE Post_ID = {$post['PostID']}")[0]['count(CommentID)'];
            ?>
                <blockquote>
                    <p><?= nl2br($post['PostBody'])?></p>
                    <small>Posted By: <strong><?= $post['Username'] ?></strong> On <strong><?= date('M d, Y \(H:i\) ', strtotime($post['PostTime'])) ?></strong></small>
                    <p class="buttons">
                        <span class="likes <?= $inOrOut ?>" data-id="<?= $post['PostID'] ?>">&nbsp;&nbsp;<i class="fa fa-heart"></i> <span class="num"><?= $post['Likes'] ?></span> Likes</span>
                        <span class="comments" data-id="<?= $post['PostID'] ?>">&nbsp;&nbsp;<i class="fa fa-comments"></i> <span class="num"><?= $commentsNumber ?></span> Comments</span>
                    </p>
                    <?php
                        //echo $loggedinUserID == $post['User_ID'] ? " <input type='submit' name='deletepost' value='delete post' />": "";
                    ?>
                    <div class='comments'></div>
                </blockquote>
                <hr>
            <?php
            }
        } else {
            echo "no post yet.";
        }
        return $posts;
    }
}
