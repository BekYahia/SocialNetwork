<?php

$title = "Create Account | SN";
include_once("classes/DB.php");
include_once("classes/Login.php");
include_once('assets/templates/header.php');
if(Login::isLoggedin()) {
    header("location:index.php");
    exit;
}
?>
<div class="login">
    <div class="login-icon">
        <i class="fa fa-send fa-fw fa-3x"></i>
    </div>
    <h2 class="icon-heading">Create Account</h2>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="form-group">
            <input type="text" class="form-control" name="username" placeholder="Username"/>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="email" placeholder="Email"/>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Password" />
        </div>
        <div class="form-group">
            <input type="submit"class="btn btn-block btn-lg btn-sn" value="Register" />
        </div>
        <a href="login.php">Already have account?</a>
        <div class="alert alert-success"></div>
    </form>
</div>
<script>
    $(function () {
        "use strict";
        var submit = $("input[type=submit]");
        submit.click(function() {
            var username = $("input[name=username]"),
                email    = $("input[name=email]"),
                password = $("input[name=password]");
            $.ajax({
               url: "api/?addUser",
                type: 'POST',
                data: {
                    username: username.val(),
                    email   : email.val(),
                    password: password.val()
                },
                success: function (data) {
                    $('div.alert-success').fadeIn().html(data);
                    //location.reload();
                },
                error: function(data) {
                    submit.addClass('shake');
                    setTimeout(function() {
                        submit.removeClass('shake');
                    }, 600);
                    $('form').trigger('reset');
                }
            });
        });
    });
</script>
<?php include_once("assets/templates/footer.php"); ?>
