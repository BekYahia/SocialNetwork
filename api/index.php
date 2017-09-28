<?php

include_once("../classes/DB.php");
include_once("../classes/Login.php");
include_once("../classes/Post.php");
// if (!Login::isLoggedin()) {
//     die('you kidding?');
// }
$loggedinUserID = Login::isLoggedin();
if($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(isset($_GET['auth'])) {
        $username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
        $password = $_POST["password"];

        $formErrors = array();

        if (empty($username) || empty($password)) {
            $formErrors[] = "Username and Password are required!";
            http_response_code(400);
        }

        if (empty($formErrors)) {

            // check username
            if(DB::query("SELECT Username, Password FROM users WHERE Username = '{$username}'")) {

                // then verify password
                if (password_verify(md5($password), DB::query("SELECT Password FROM users WHERE Username = '{$username}'")[0]['Password'])) {

                    $crypto_strong = TRUE;
                    $token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));

                    //echo $token;

                    $userid = DB::query("SELECT UserID FROM users WHERE Username = '{$username}'")[0]['UserID'];
                    DB::query("INSERT INTO login_tokens (Token, User_ID) VALUES(:token, :user_id)", array(":token" => md5($token), ":user_id" => $userid));
                    setcookie("SNID", $token, time() + 60 * 60 * 24 * 3, "/", NULL, NULL, TRUE);
                    echo "success login";
                    header('REFRESH:1;URL=../index.php');

                } else {
                    $formErrors[] = "Username and Password aren't Matched";
                    http_response_code(401);
                }

            } else {
                $formErrors[] = "Username not Register";
                http_response_code(401);
            }
        }
        ########
        if (!empty($formErrors)) {
            foreach($formErrors as $error) {
                echo $error;
            }
        }

    } elseif(isset($_GET['addUser'])) {
        $username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        $password = $_POST["password"];

        $formErrors = array();

        if (strlen($username) <= 3 || strlen($username) >= 32) {
            $formErrors[] = "Fill out Username <strong>(3 - 32)chars</strong>";
            http_response_code(400);
        }
        if(empty($email)) {
            $formErrors[] = "Fill out Email";
            http_response_code(400);
        }
        if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) != TRUE) {
            $formErrors[] = "Invalid Email!";
            http_response_code(400);
        }
        if (strlen($password) <= 3) {
            $formErrors[] = "Fill out Password <strong>More than 4 chars</strong>";
            http_response_code(400);
        }

        if (empty($formErrors)) {

            // i need user have unique username and email
            if (DB::query("SELECT Username FROM users WHERE Username = ?", array($username))) {
                $formErrors[] = "Username already taken!";
                http_response_code(401);
            }
            if (DB::query("SELECT Email FROM users WHERE Email = ?", array($email))) {
                $formErrors[] = "Email already taken!";
                http_response_code(401);
            }

            if(empty($formErrors)) {

                DB::query("INSERT INTO users (Username, Email, Password) VALUES(:username, :email, :password)", array(":username" => $username, ":email" => $email, ":password" => password_hash(md5($password), PASSWORD_BCRYPT)));
                echo "Register Successfly! now <a href='login.php'>Login</a>";
            }
        }
        ########
        if (!empty($formErrors)) {
            foreach($formErrors as $error) {
                echo $error.'<br />';
            }
        }

    } elseif (isset($_GET['likes']) && isset($_GET["postid"])) {
            $postid = is_numeric($_GET['postid']) ? intval($_GET['postid']) : "";

            if(DB::query("SELECT PostID FROM posts WHERE PostID = ?", array($postid))) { # now you have a valid post id for like or commect
                $liker = $loggedinUserID;
                if(!DB::query("SELECT Post_ID FROM post_likes WHERE Post_ID = ? AND User_ID = ?", array($postid, $liker))) {
                    DB::query("UPDATE posts SET Likes = Likes + 1 WHERE PostID = '{$postid}'");
                    DB::query("INSERT INTO post_likes (Post_ID, User_ID) VALUES (:post_id, :user_id)", array(":post_id" => $postid, ":user_id" => $liker));// $followerid = LoggedInUser AS in Following Model
                    //notify
                    $notify = DB::query("SELECT posts.User_ID AS Receiver, post_likes.User_ID AS Sender FROM posts, post_likes WHERE posts.PostID = post_likes.Post_ID AND PostID = '{$postid}'");
                    DB::query("INSERT INTO notifications (Sender, Receiver, Type) VALUES (:sender, :receiver, 2)", array(":sender" => $notify[0]['Sender'], ":receiver" => $notify[0]['Receiver']));
                } else {
                    DB::query("UPDATE posts SET Likes = Likes - 1 WHERE PostID = '{$postid}'");
                    DB::query("DELETE FROM post_likes WHERE Post_ID = ? AND User_ID = ?", array($postid, $liker));// $followerid = LoggedInUser AS in Following Model
                    /*
                    == 1) Think about how to remove notification when dislike
                    == 2) make notification as link releated to specific post; i guess that you'll need a postid, and put it as attr in the element
                    */
                }
                $likes =  DB::query("SELECT Likes FROM posts WHERE PostID = {$postid}")[0]['Likes'];
                if(DB::query("SELECT * FROM post_likes WHERE Post_ID = '{$postid}' AND User_ID = '{$loggedinUserID}'")) {
                    $userStatus = 'youIn';
                } else {
                    $userStatus = 'youOut';
                }
                $response_likes  = "{";
                $response_likes .= '"Likes":'.$likes.',';
                $response_likes .= '"userStatus":"'.$userStatus.'"';
                $response_likes .= "}";
                echo $response_likes;

            } else {
                echo "invalid postid";
                http_response_code(409);
            }
    } elseif (isset($_GET['comments']) && isset($_GET["postid"]) && isset($_GET['addcomment'])) {

        $postid = is_numeric($_GET['postid']) ? intval($_GET['postid']) : "";

        if(DB::query("SELECT PostID FROM posts WHERE PostID = ?", array($postid))) { # now you have a valid post id for like or commect
            $commentbody = htmlspecialchars_decode(str_replace("\\", "",$_POST['commentbody']));
            Post::commentPost($commentbody, $loggedinUserID, $postid);
        } else {
            echo "invalid postid";
            http_response_code(409);
        }
    } elseif(isset($_GET['follow']) && isset($_GET['id'])) {
        if(Login::isLoggedin()) {
            $userid = is_numeric($_GET['id']) ? intval($_GET['id']) : "";

            if(DB::query("SELECT UserID FROM users WHERE UserID = ?", array($userid))) {

                // Start ...
                if(DB::query("SELECT FollowerID FROM followers WHERE FollowerID = ? AND User_ID = ?", array($loggedinUserID, $userid))) {
                    //unfollow
                    if ($loggedinUserID != $userid) {
                        DB::query("DELETE FROM followers WHERE FollowerID = ? AND User_ID = ?", array($loggedinUserID, $userid));
                        //$isFollowing = FALSE;
                        echo '{ "isFollowing" : "FALSE" }';
                        if($loggedinUserID == 3) { // 3 is id of the Verified account
                            DB::query("UPDATE users SET Verified = 0 WHERE UserID = '{$userid}'");
                        }
                    }
                } else {
                    //follow
                    if($loggedinUserID != $userid) { //you can't follow yourself
                        DB::query("INSERT INTO followers (FollowerID, User_ID) VALUES(:followerid, :user_id)", array(":followerid" => $loggedinUserID, ":user_id" => $userid));
                        //$isFollowing = TRUE;
                        echo '{ "isFollowing" : "TRUE" }';
                        if($loggedinUserID == 3) { // 3 is id of the Verified account
                            DB::query("UPDATE users SET Verified = 1 WHERE UserID = '{$userid}'");
                        }
                    }

                }


            } else {
                echo "invalid userid";
                http_response_code(409);
            }
        } else {
            // header to login page
        }
    } elseif(isset($_GET['addpost'])) {
        $postbody = htmlspecialchars_decode(str_replace("\\", "",$_POST['postbody'])); // i don't wanna [\] cuz it confuse JSON syntax.
        Post::createPost($postbody, $loggedinUserID);

        // retrieve Posts
        //$posts  = "[";
        $posts = DB::query("SELECT posts.*, users.Username FROM posts, users WHERE posts.User_ID = users.UserID AND User_ID = '{$loggedinUserID}' ORDER BY PostID DESC");
        $response_posts  = "[";
        foreach($posts as $post) {
            $inOrOut = DB::query("SELECT * FROM post_likes WHERE Post_ID = '{$post['PostID']}' AND User_ID = '{$loggedinUserID}'") ? 'youIn': 'youOut';
            $commentsNumber = DB::query("SELECT count(CommentID) FROM comments WHERE Post_ID = {$post['PostID']}")[0]['count(CommentID)'];
            $response_posts .= "{";
            $response_posts .= ' "inOrOut"          : "'.$inOrOut.'", ';
            $response_posts .= ' "commentsNumber"   : '.$commentsNumber.', ';
            $response_posts .= ' "PostID"           : '.$post['PostID'].', ';
            $response_posts .= ' "PostBody"         : "'.str_replace("\n", "<br />", $post['PostBody']).'", '; // i user [nl2br] first but it doesn't work;
            $response_posts .= ' "PostTime"         : "'.date('M d, Y \(H:i\) ', strtotime($post['PostTime'])).'", ';
            $response_posts .= ' "Likes"            : '.$post['Likes'].', ';
            $response_posts .= ' "Username"         : "'.$post['Username'].'" ';
            $response_posts .= "},";
        }
        $response_posts = rtrim($response_posts, ',');
        $response_posts  .= "]";
        echo $response_posts;

    } elseif (isset($_GET['bio'])) {

        $bio = htmlspecialchars_decode(str_replace("\\", "",$_POST['bio']));
        if(!empty($bio)) {
            DB::query("UPDATE users SET Bio = '{$bio}' WHERE UserID = {$loggedinUserID}");
            //echo '{ "Bio" :  "'.$bio.'" }';
        } else {
            http_response_code(409);
            echo "fill out Bio";
        }

    } elseif(isset($_GET['changepassword'])) {
        $oldpassword = $_POST["oldpassword"];
        $newpassword = $_POST["newpassword"];
        $newpasswordrepeat = $_POST["newpasswordrepeat"];

        $formErrors = array();

        if(empty($oldpassword) || empty($newpassword) || empty($newpasswordrepeat)) {
            http_response_code(409);
            $formErrors[] = "All Fields required.";
        }
        if(empty($formErrors)) {
            // verify old password
            if(password_verify(md5($oldpassword), DB::query("SELECT Password FROM users WHERE UserID = ?", array(Login::isLoggedin()))[0]['Password'])) {

                    // new passwords matchde
                    if($newpassword === $newpasswordrepeat) {

                        // validate new password
                        if (strlen($newpassword) > 3) {

                            DB::query("UPDATE users SET Password = ? WHERE UserID = ?", array(password_hash(md5($newpassword), PASSWORD_BCRYPT), Login::isLoggedin()));
                            //echo "Password Changed Successfly";

                        } else {
                            http_response_code(401);
                            $formErrors[] = "Fill out new Password <strong>More than 4 chars</strong>";
                        }

                    } else {
                        http_response_code(401);
                        $formErrors[] = "new passwords don't match";
                    }

            } else {
                http_response_code(401);
                $formErrors[] = "incorrect old password";
            }
        }
        ########
        if (!empty($formErrors)) {
            foreach($formErrors as $error) {
                echo $error.'<br />';
            }
        }
    } elseif(isset($_GET['forgotPassword_Email'])) {
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

        $formErrors = array();

        if(!empty($email)) {
            if(filter_var($email, FILTER_VALIDATE_EMAIL) == TRUE) {

                $getUser = DB::query("SELECT UserID FROM users WHERE Email = '{$email}'");
                if($getUser) {

                    $crypto_strong = TRUE;
                    $token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));
                    DB::query("INSERT INTO password_tokens (Token, User_ID) VALUES(:token, :user_id)", array(":token" => md5($token), ":user_id" => $getUser[0]['UserID']));
                    /*
                    ** Use php mailer to send a pure $token not the hashed one to this email
                    ** the link maybe link forgot-password.php?token=?pure_token
                    *****
                    ** ** PHPMAILER
                    */
                    /*then remove*/ echo $token;
                } else {
                    $formErrors[] = "this email isn't in our database";
                    http_response_code(409);
                }

            } else {
                $formErrors[] = "Invalid Email!";
                http_response_code(409);
            }
        } else {
            $formErrors[] = "Fill out Email";
            http_response_code(400);
        }
        ########
        if (!empty($formErrors)) {
            foreach($formErrors as $error) {
                echo $error.'<br />';
            }
        }
    } elseif(isset($_GET['resetForgottenPassword'])) {

        $newpassword = $_POST["newpassword"];
        $newpasswordrepeat = $_POST["newpasswordrepeat"];
        $userid = $_POST['userid'];

        $formErrors = array();
        if(empty($newpassword) || empty($newpasswordrepeat)) {
            $formErrors[] = "Fields are required.";
            http_response_code(409);
        } else {
            // new passwords matchde
            if($newpassword === $newpasswordrepeat) {

                // validate new password
                if (strlen($newpassword) > 3) {
                    DB::query("UPDATE users SET Password = ? WHERE UserID = ?", array(password_hash(md5($newpassword), PASSWORD_BCRYPT), $userid));
                    DB::query("DELETE FROM password_tokens WHERE User_ID = '{$userid}'");

                } else {
                    $formErrors[] = "Fill out new Password <strong>More than 4 chars</strong>";
                    http_response_code(401);
                }

            } else {
                $formErrors[] = "new passwords don't match";
                http_response_code(401);
            }
        }

        ########
        if (!empty($formErrors)) {
            foreach($formErrors as $error) {
                echo $error.'<br />';
            }
        }
    } elseif(isset($_GET['logout'])) {
         //echo $_POST["alldevices"];
        if($_POST["alldevices"] == 'TRUE') {
            if(isset($_COOKIE['SNID'])) {
                DB::query("DELETE FROM login_tokens WHERE User_ID = ?", array(Login::isLoggedin()));
            }
        } else {
            if(isset($_COOKIE['SNID'])) {
                DB::query("DELETE FROM login_tokens WHERE Token = ?", array(md5($_COOKIE['SNID'])));
            }
        }
        setcookie("SNID", 'ava', time() - 3600, "/");

    } /*add POST action*/ else {
        http_response_code(500);
        echo "invalid un auth GET!";
    }

} elseif($_SERVER['REQUEST_METHOD'] === 'GET') {
    if(isset($_GET['comments']) && isset($_GET["postid"])) {
        $postid = is_numeric($_GET['postid']) ? intval($_GET['postid']) : "";

        if(DB::query("SELECT PostID FROM posts WHERE PostID = ?", array($postid))) { # now you have a valid post id for like or commect
            $comments = DB::query("SELECT comments.*, users.Username FROM comments, users WHERE comments.User_ID = users.UserID AND Post_ID = {$postid}");
            if(!empty($comments)) {
                $response_comments = "[";
                foreach($comments as $comment) {
                    $response_comments .= "{";
                    $response_comments .= ' "CommentBody"   : "'.str_replace("\n", "<br />", $comment['CommentBody']).'", ';
                    $response_comments .= ' "Username"      : "'.$comment['Username'].'", ';
                    $response_comments .= ' "CommentTime"   : "'.$comment['CommentTime'].'" ';
                    $response_comments .= "},";
                }
                $response_comments = rtrim($response_comments, ',');
                $response_comments .= "]";
                echo $response_comments;
            } else {
                echo "No Comments Yet.";
            }
        } else {
            echo "invalid postid";
            http_response_code(409);
        }
    }
} else {
    http_response_code(405);
}
