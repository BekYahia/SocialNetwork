<?php
    if(Login::isloggedin()) {
        $username = DB::query("SELECT Username FROM users WHERE UserID = ?", array(Login::isloggedin()))[0]['Username'];
    ?>
        <nav class="navbar navbar-default">
          <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a href="index.php" title="Social Network">
                  <div class="brand">
                      <div class="brand-icon">
                          <i class="fa fa-send fa-fw fa-3x"></i>
                      </div>
                  </div>
              </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                  <input type="text" class="form-control" placeholder="Search">
                </div>
                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
              </form>
              <ul class="nav navbar-nav navbar-right">
                  <li><a href="index.php">Timeline</a></li>
                  <li><a href="messages.php">Messages</a></li>
                  <li><a href="notifications.php">Notifications</a></li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $username ?> <span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="profile.php?username=<?= $username ?>">My Profile</a></li>
                    <li><a href="change-password.php">Change Password</a></li>
                    <li><a href="profile.php?username=<?= $username ?>&editBio">Edit Your Bio</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="logout.php">Logout</a></li>
                  </ul>
                </li>
              </ul>
            </div><!-- /.navbar-collapse -->
          </div><!-- /.container-fluid -->
        </nav>
    <?php
    } else {
    ?>
    <nav class="navbar navbar-default">
      <div class="container">
        <a href="login.php" class="btn btn-sn">Login</a>
    </div>
    </nav>
    <?php
    }

?>
