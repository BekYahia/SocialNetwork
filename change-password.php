<?php
$title = "Change Password | SN";
include_once("classes/DB.php");
include_once("classes/Login.php");
include_once('assets/templates/header.php');
include_once('assets/templates/navbar.php');

if(!Login::isLoggedin()) {

    if(isset($_GET['token'])) {

        $token = filter_var($_GET['token'] ,FILTER_SANITIZE_STRING);
        $getUser = DB::query("SELECT User_ID FROM password_tokens WHERE Token = ?", array(md5($token)));

        if($getUser) { // now you have a valid token
        ?>
            <form class="login">
                <h2>Reset Password</h2>
                <div class="form-group">
                    <input type="password" class="form-control" name="newpassword" placeholder="New Password" autofocus />
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="newpasswordrepeat" placeholder="Repeat New Password" />
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-sn" name="resetpassword" value="Reset Password" />
                    <div class="alert alert-success">Password Changed Successfully!<br /><a href="#">Go Profile</a></div>
                </div>
            </form>
            <script type="text/javascript">
                $(function () {
                    "use strict";
                    $('input[name=resetpassword]').click(function () {
                        var resetButton = $(this);
                        $.ajax({
                            url: "api/?resetForgottenPassword",
                            type: "POST",
                            data : {
                                newpassword: $("input[name=newpassword]").val(),
                                newpasswordrepeat: $("input[name=newpasswordrepeat]").val(),
                                userid: <?= $getUser[0]['User_ID'] ?>
                            },
                            success: function () {
                                resetButton.next().fadeIn();
                            },
                            error: function (r) {
                                resetButton.addClass('shake');
                                setTimeout(function() {
                                    resetButton.removeClass('shake');
                                }, 600);
                                $('form').trigger('reset');
                                console.log(r);
                            }
                        });
                    });
                });
            </script>

        <?php
        } else {
            header("location:index.php");
            die("invalid token");
        }

    } else {
        header("location:index.php");
        die("not loggedin");
    }

} else {

    ?>


    <form class="login">
        <h2>Change Password</h2>
        <div class="form-group">
            <input type="password" class="form-control" name="oldpassword" placeholder="Old Password" autofocus />
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="newpassword" placeholder="New Password" />
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="newpasswordrepeat" placeholder="Repeat New Password" />
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-sn" name="changepassword" value="Change Password" />
            <div class="alert alert-success changepassword-alert">Password Changed Successfully!</div>
        </div>
    </form>
    <script type="text/javascript">
        $("input[name=changepassword]").click(function () {
            var changeButton = $(this);
            $.ajax({
                url: "api/?changepassword",
                type: "POST",
                data: {
                    oldpassword: $("input[name=oldpassword]").val(),
                    newpassword: $("input[name=newpassword]").val(),
                    newpasswordrepeat: $("input[name=newpasswordrepeat]").val()
                },
                success: function (r) {
                    changeButton.next().fadeIn();
                    $("form").trigger('reset');
                    // logut from all devieces
                    setTimeout(function () {
                        $.ajax({
                            url: "api/?logout",
                            type: "POST",
                            data : {
                                alldevices: 'TRUE'
                            },
                            success: function () {
                                location.reload();
                            },
                            error: function (r) {
                                console.log(r);
                            }
                        });
                    }, 600);
                },
                error: function (r) {
                    changeButton.addClass('shake');
                    setTimeout(function() {
                        changeButton.removeClass('shake');
                    }, 600);
                    $('form').trigger('reset');
                    console.log(r);
                }
            });
        });
    </script>
    <?php
}
include_once('assets/templates/footer.php');
