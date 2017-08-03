<?php

require_once('../EZ.php');

$error_message = "";

if (empty($_GET['error']) || $_SERVER['REQUEST_METHOD'] == "POST") {
  if (isset($_POST['login'])) {
    EZ::login();
    die();
  }
  if (isset($_REQUEST['logout'])) {
    EZ::logout();
  }
  $error_message = '<div class="alert alert-info">Please login with your Username and Password.</div>';
}
else if ($_GET['error'] == "1") {
  $error_message = '<div class="alert alert-danger">Your username and password are incorrect!</div>';
}
elseif ($_GET['error'] == "2") {
  $error_message = '<div class="alert alert-danger">Your username and password cannot be empty!</div>';
}
elseif ($_GET['error'] == "3") {
  $error_message = '<div class="alert alert-warning">Please login to access the admin panel. <a style="font-size:1.5em;float:right" href="pub.php" title="Find out more" data-toggle="tooltip"><i class="glyphicon glyphicon-question-sign blue"></i></a></div>';
}

if (EZ::isLoggedIn()) {
  header("location:profile.php");
}
else {
  $no_visible_elements = true;
  require_once('header.php');
  ?>
  <div class="row">
    <div class="well col-md-5 center login-box">
      <?php echo $error_message; ?>
      <form class="form-horizontal" method="post">
        <fieldset>
          <div class="input-group input-group-lg">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user red"></i></span>
            <input name="myusername" type="text" class="form-control" placeholder="Username">
          </div>
          <div class="clearfix"></div><br>

          <div class="input-group input-group-lg">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock red"></i></span>
            <input name="mypassword" type="password" class="form-control" placeholder="Password">
          </div>
          <div class="clearfix"></div>

          <p class="center col-md-5">
            <button type="submit" name="login" class="btn btn-primary">Login</button>
          </p>
        </fieldset>
      </form>
    </div>
    <!--/span-->
  </div><!--/row-->
  <?php
}
require_once('footer.php');
