<?php
$title = 'Forgot Password | SN';
include_once("classes/DB.php");
include_once("classes/Login.php");
include_once('assets/templates/header.php');
if(Login::isLoggedin()) {
    die("Sorry! you're already loggedin.");
}
?>

<div class="login">
    <h2>Forgot Password</h2>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="form-group">
            <input type="text" class="form-control" name="email" placeholder="Type your Email"/>
        </div>
        <div class="form-group">
            <input type="submit"class="btn btn-block btn-lg btn-sn" value="Reset Password" />
        </div>
        <div class="alert alert-success">We Send You an Email Successfully!</div>
    </form>
</div>

<script>
    $(function () {
        "use strict";
        var submit = $("input[type=submit]");
        submit.click(function() {
            var email = $("input[name=email]");
            $.ajax({
               url: "api/?forgotPassword_Email",
                type: 'POST',
                data: {
                    email : email.val()
                },
                success: function (data) {
                    submit.parent().next('.alert').fadeIn();
                    console.log(data);
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
