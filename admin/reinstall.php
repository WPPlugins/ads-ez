<?php
require_once 'Installer.php';
require_once 'lang.php';
require 'header.php';
?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Reinstall Your Application</a>
    </li>
  </ul>
</div>

<?php
insertAlerts(12);
openBox("Reinstalling Your Application", "repeat", 12);
?>
<p>
  If you have errors related to the database (such as table not found or other SQL exceptions), you may be able to recover by rerunning the setup routines. The setup is designed to be robust against reruns, and it should be safe to click on the button below.
</p>
<p class="center-text">
  <a class="btn btn-warning center-text" href="reinstall.php?confirm" data-toggle='tooltip' title='Rerun the application setup to get the Database setup'><i class="glyphicon glyphicon-refresh icon-white"></i>&nbsp; Reinstall</a>
</p>
<?php
closeBox();

if (isset($_REQUEST['confirm'])) {
  EZ::flashWarning("Reinstalling your application. Please stand by...", true);

  if (EZ::isInWP()) {
    $GLOBALS['isInstallingWP'] = true;
  }

  $installer = new Installer();
  $installer->configure();

  if ($installer->verifyCfg()) {
    $ret = $installer->install();
    extract($ret);
  }
  if (!is_array($success)) {
    $success = array($success);
  }
  if (!is_array($errors)) {
    $errors = array($errors);
  }
  if (!is_array($message)) {
    $message = array($message);
  }

  $sMsg = $eMsg = "";
  foreach ($success as $s) {
    if (!empty($s)) {
      $sMsg .= "Succeeded in executing $s SQL statements.\n";
    }
  }
  if (!empty($sMsg)) {
    EZ::showSuccess($sMsg, true);
  }
  foreach ($errors as $e) {
    if (!empty($e)) {
      $eMsg .= "Error in executing $e SQL statements.\n";
    }
  }
  foreach ($message as $m) {
    if (!empty($m)) {
      $eMsg .= "Error message: $m\n";
    }
  }
  if (!empty($eMsg)) {
    EZ::showError($eMsg);
  }
}
require 'promo.php';
require 'footer.php';
