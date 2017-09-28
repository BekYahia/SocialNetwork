<?php
include_once('classes/DB.php');
include_once('classes/Login.php');
include_once('classes/Post.php');
include_once('assets/templates/header.php');
include_once('assets/templates/navbar.php');
$showTimeline = FALSE;
if (Login::isLoggedin()) {
    $loggedinUserID = Login::isLoggedin();
    $showTimeline = TRUE;
} else {
    header('location:login.php');
    die;
}

if($showTimeline) {
    echo "<div class='container timeline'>";
    /*
    == get posts from people you follow
    == what if i get your own posts also in timeline page? i don't know if it make sense!
    */
    $select_posts_query = DB::query("SELECT posts.*, users.Username
                                    FROM posts, users, followers
                                    WHERE posts.User_ID = followers.User_ID
                                    AND followers.User_ID != '{$loggedinUserID}'
                                    AND users.UserID = posts.User_ID
                                    AND followers.FollowerID = '{$loggedinUserID}'
                                    ORDER BY PostTime DESC");
    $QUERY_STRING_username = "";
    echo "<div class='posts'>";
        Post::displayPosts($select_posts_query, $QUERY_STRING_username, $loggedinUserID);
    echo "</div>";
    ?>
    <script type="text/javascript">
        $(function () {
            "use strict";
            var like = $("span.likes");
            $(like).click(function () {
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
            $('span.comments').one('click', function () {
                var commentsDiv = $(this).parent().next('div.comments'),
                    spanComments = $(this),
                    num = $(this).find('span.num');
                $.ajax({
                    url : "api/?comments&postid="+spanComments.data('id'),
                    type: "GET",
                    data: "",
                    success: function (r) {
                        commentsDiv.fadeIn().append('<h5>Comments:</h5>');
                        if (r != 'No Comments Yet.') {
                            var comments = JSON.parse(r);
                            num.text(comments.length);
                            $.each(comments, function (i) {
                                commentsDiv.fadeIn().append('<p><small><strong>'+comments[i].Username+'</strong> Says:</small> '+comments[i].CommentBody+'</p>');
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
                                success: function (r) {
                                    // Start Show Comments
                                    $.ajax({
                                        url : "api/?comments&postid="+spanComments.data('id'),
                                        type: "GET",
                                        data: "",
                                        success: function (r) {
                                            commentsDiv.fadeIn().html('<h5>Comments:</h5>');
                                            if (r != 'No Comments Yet.') {
                                                var comments = JSON.parse(r);
                                                num.text(comments.length);
                                                $.each(comments, function (i) {
                                                    commentsDiv.fadeIn().append('<p><small><strong>'+comments[i].Username+'</strong> Says:</small> '+comments[i].CommentBody+'</p>');
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

    </script>
    <?php
    echo "</div>";
}
include_once('assets/templates/footer.php');
