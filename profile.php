<?php
$title ="Profile | SN";
ob_start();
include_once('classes/DB.php');
include_once('classes/Login.php');
include_once('classes/Post.php');
include_once('assets/templates/header.php');
include_once('assets/templates/navbar.php');

if(isset($_GET['username'])) {
    $username = filter_var($_GET["username"], FILTER_SANITIZE_STRING);
    // check user exsist
    $getUser = DB::query("SELECT * FROM users WHERE Username = ?", array($username)); // get all data for the username in $_GET
    if($getUser) {  # now you have a valid user profile. enjoy coding...

        $isFollowing = FALSE;
        $userid = $getUser[0]['UserID'];
        $followerid = Login::isLoggedin();
        // if user already following the guy without submit follow
        if(DB::query("SELECT FollowerID FROM followers WHERE User_ID = '{$userid}' AND FollowerID = '{$followerid}'")) {
            $isFollowing = TRUE;
        }

        echo "<div class='container timeline profile'>";
        $bioName = (Login::isLoggedin() == $getUser[0]['UserID']) && isset($_GET['editBio']) ? "style=float:none;text-align:center": '';
        ?>

            <div class="profile-username">
                <div class="name" <?= $bioName ?> >
                    <h2><?= $username ?>'s Profile<?= $getUser[0]['Verified'] == 1 ? " <i class='fa fa-check-circle'></i>": ""; ?></h2>
                </div>
            <?php if(Login::isLoggedin()) { ?>
                <div class="follow">
                    <form action="<?= $_SERVER['PHP_SELF'] ?>?username=<?= $username ?>" method="POST">
                        <?php
                            if ($followerid != $userid) {
                                echo $isFollowing ? "<input type='submit' class='btn btn-sn' value='UnFollow' name='follow'/>": "<input type='submit' class='btn btn-sn' value='Follow' name='follow'/>";
                            }
                        ?>
                    </form>
                </div>
            <?php } ?>

            </div>
            <div class="row">
            <?php
                if ((Login::isLoggedin() == $getUser[0]['UserID']) && isset($_GET['editBio'])) {

                ?>
                    <div class="col-sm-8 col-sm-offset-2">
                        <div class="panel bio panel-danger">
                            <div class="panel-heading">
                                <h5 class="panel-title">Bio</h5>
                            </div>
                            <div class="panel-body">
                                <form>
                                    <div class="form-group">
                                        <textarea class="form-control" name="editbio" autofocus><?= $getUser[0]['Bio'] ?></textarea>
                                    </div>
                                    <input type="submit" class="btn btn-sn" name="bio" value="Save">
                                    <span class="bio-alert alert alert-success">Udated Successfuly! <a href="?username=<?= $getUser[0]['Username'] ?>">Go Profile</a></span>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php
                } else {
                ?>
                    <div class="col-sm-4">
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                <h5 class="panel-title">Bio</h5>
                            </div>
                            <div class="panel-body">
                                <?php
                                    $userBio = DB::query("SELECT Bio FROM users WHERE Username = '{$username}'")[0]['Bio'];
                                    echo empty($userBio) ? "no Bio Yet.": $userBio;
                                    echo (Login::isLoggedin() == $getUser[0]['UserID']) && empty($userBio) ? " <a href='?username=".$getUser[0]['Username']."&editBio'>Add Your Bio</a>": "";
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                    <?php if($followerid == $userid) { ?>
                            <form>
                                <div class="form-group">
                                    <textarea name="postbody" class="form-control" placeholder="What's in Your Mind ..."></textarea> <!-- max: 170 chars -->
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-sn" value="Publish" name="createpost" />
                                </div>
                            </form>
                            <hr />
                    <?php }
                        $QUERY_STRING_username = "username=".$username."&";
                        $select_posts_query = DB::query("SELECT posts.*, users.Username FROM posts, users WHERE posts.User_ID = users.UserID AND posts.User_ID = '{$userid}' ORDER BY PostID DESC");
                        echo "<div class='posts'>";
                        Post::displayPosts($select_posts_query, $QUERY_STRING_username, $followerid);
                        echo "</div>";
                    ?>
                    </div>
            <?php } ?>
            </div>


        <script type="text/javascript">

        <?php if (Login::isloggedin()) { ?>
            $(function () {
                "use strict";
                $("input[name=bio]").click(function () {
                    var bioButton = $(this);
                    $.ajax({
                        url: "api/?bio",
                        type: "POST",
                        data: {
                            bio: $("textarea[name=editbio]").val()
                        },
                        success: function () {
                            bioButton.next().fadeIn();
                        },
                        error: function (r) {
                            console.log(r);
                        }
                    });
                });

                $("input[name=follow]").click(function () {
                    var followButton = $(this);
                    $.ajax({
                        url: "api/?follow&id=<?= $userid ?>",
                        type: "POST",
                        data: "",
                        success: function(r) {
                            var res = JSON.parse(r);
                            if(res.isFollowing === 'TRUE') {
                                followButton.val('UnFollow');
                            } else {
                                followButton.val('Follow');
                            }

                        },
                        error: function (r) {
                            console.log(r);
                        }
                    });
                });

                $('input[name=createpost]').click(function () {
                    var createpostButton = $(this);
                    $.ajax({
                        url: "api/?addpost",
                        type: "POST",
                        data: {
                            postbody: $('textarea[name=postbody]').val()
                        },
                        success: function (r) {
                            $('textarea[name=postbody]').val('');
                            var posts = JSON.parse(r);
                            $('div.posts').html('');
                            $.each(posts, function (i) {
                                $('div.posts').append('<blockquote><p>'+posts[i].PostBody+'</p><small>Posted By: <strong>'+posts[i].Username+'</strong> On <strong>'+posts[i].PostTime+'</strong></small><p class="buttons"><span class="likes '+posts[i].inOrOut+'" data-id="'+posts[i].PostID+'">&nbsp;&nbsp;<i class="fa fa-heart"></i> <span class="num">'+posts[i].Likes+'</span> Likes</span><span class="comments" data-id="'+posts[i].PostID+'">&nbsp;&nbsp;<i class="fa fa-comments"></i> <span class="num">'+posts[i].commentsNumber+'</span> Comments</span></p><div class="comments"></div></blockquote><hr />');
                            });
                        },
                        error: function (r) {
                            createpostButton.addClass('shake');
                            setTimeout(function() {
                                createpostButton.removeClass('shake');
                            }, 600);
                        }

                    });
                });

                var like = $("span.likes");
                $('body').on('click', 'span.likes',function () {
                    var num = $(this).find('span.num');
                    $.ajax({
                        url : "api/?likes&postid="+$(this).data('id'),
                        data: "",
                        type: "POST",
                        success: function (r) {
                            var res = JSON.parse(r);
                            num.text(res.Likes).parent().removeClass('youIn youOut').addClass(res.userStatus).end().prev().addClass('icon-pop');
                            setTimeout(function () {
                                num.prev().removeClass('icon-pop');
                            }, 300);
                        },
                        error: function (r) {
                            console.log(r);
                        }
                    });
                });

                //comments
                $('div.posts').on('click', 'span.comments', function () {
                    /*
                    ==  think about how to prevent sending http request when click++; without using [one]
                    ==  a click++ from user seem like a litle a bit bad scenario to imagin, buuuuut i don't why ........
                    */
                    var commentsDiv = $(this).parent().next('div.comments'),
                        spanComments = $(this),
                        num = $(this).find('span.num');
                    $.ajax({
                        url : "api/?comments&postid="+spanComments.data('id'),
                        type: "GET",
                        data: "",
                        success: function (r) {
                            commentsDiv.html('');
                            commentsDiv.append('<h5>Comments:</h5>');
                            if (r != 'No Comments Yet.') {
                                var comments = JSON.parse(r);
                                num.text(comments.length);
                                $.each(comments, function (i) {
                                    commentsDiv.append('<p><small><strong>'+comments[i].Username+'</strong> Says:</small> '+comments[i].CommentBody+'</p>');
                                });
                            } else {
                                commentsDiv.append('<small>'+r+'</small>');
                            }
                            commentsDiv.append("<div class='form-group'><textarea class='form-control' name='commentbody' placeholder='type a comment ...'></textarea></div><div class='form-group'><input class='btn btn-sn' type='submit' value='Comment' name='addcomment' /></div>");

                            $('body').on('click', 'input[name=addcomment]', function () {
                                $.ajax({
                                    url : "api/?comments&addcomment&postid="+spanComments.data('id'),
                                    type: 'POST',
                                    data: {
                                        commentbody : $(this).parent().prev().find('textarea').val()
                                    },
                                    success: function () {
                                        // Start Show Comments
                                        $.ajax({
                                            url : "api/?comments&postid="+spanComments.data('id'),
                                            type: "GET",
                                            data: "",
                                            success: function (r) {
                                                commentsDiv.html('<h5>Comments:</h5>');
                                                if (r != 'No Comments Yet.') {
                                                    var comments = JSON.parse(r);
                                                    num.text(comments.length);
                                                    $.each(comments, function (i) {
                                                        commentsDiv.append('<p><small><strong>'+comments[i].Username+'</strong> Says:</small> '+comments[i].CommentBody+'</p>');
                                                    });
                                                } else {
                                                    commentsDiv.append('<small>'+r+'</small>');
                                                }
                                                commentsDiv.append("<div class='form-group'><textarea class='form-control' name='commentbody' placeholder='type a comment ...'></textarea></div><div class='form-group'><input class='btn btn-sn' type='submit' value='Comment' name='addcomment' /></div>");
                                            },
                                            error: function (r) {
                                                console.log(r);
                                            }
                                        }); // End Show Comments


                                    },
                                    error: function (r) {
                                        console.log(r);
                                    }
                                });
                            });


                        },
                        error: function (r) {
                            console.log(r);
                        }
                    });

                });
            });
        <?php } #end of check login?>
        </script>
        <?php
        echo "</div>";

    } else {
        header('REFRESH:2;URL=index.php');
        die("username not found");
    }

} else {
    header('REFRESH:2;URL=index.php');
    die("Invalid url");
}
include_once('assets/templates/footer.php');
ob_end_flush();
