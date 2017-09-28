<?php

include_once('classes/DB.php');
include_once('classes/Login.php');
include_once('assets/templates/header.php');
include_once('assets/templates/navbar.php');
if (!Login::isLoggedin()) {
    header("location:login.php");
    die;
}
?>


<form class="login">
    <h2>Logout</h2>
    <div class="form-group">
        <input type="checkbox" name="alldevices" id="alldevices" value="FALSE" /> <label for="alldevices">Logout From ALL Devices?</label>
    </div>
    <input type="submit" name="logout" class="btn btn-sn" value="Logout" />
</form>
<script type="text/javascript">
    $(function () {
        "use strict";

        $('input[name=logout]').click(function () {
            $(':checkbox:checked').val('TRUE');
            $.ajax({
                url: "api/?logout",
                type: "POST",
                data : {
                    alldevices: $('input[name=alldevices]').val()
                },
                success: function (r) {
                    location.reload();
                },
                error: function (r) {
                    console.log(r);
                }
            });
        });
    });

</script>
<?php include_once('assets/templates/footer.php'); ?>
