<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Dashboard</a>
    </li>
  </ul>
</div>
<?php
insertAlerts(12);
?>
<div id="quickStart">
  <?php
  openBox("Quick Start and Tour", "home", 12);
  require 'tour.php';
  closeBox();
  ?>
</div>
<div id="features" style="display: none">
  <?php
  openBox("Features and Benefits", "thumbs-up", 12);
  include('intro.php');
  closeBox();
  ?>
</div>
<?php
if (!empty($_COOKIE['ez-last-request'])) {
  $msg = "The requested resource ({$_COOKIE['ez-last-request']}) is not found on this server.";
  EZ::flashError($msg, true);
}
require 'promo.php';
require 'footer.php';
