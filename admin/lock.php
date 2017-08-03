<?php

require_once('../EZ.php');

if (EZ::isLoggedInWP()) {
  if (!EZ::isActive()) {
    $pluginsPage = admin_url('plugins.php');
    wp_die("<h3>Plugin Not Active</h3><strong>" . EZ::$name . "</strong> is not active.<br/ >Please activate it from your <a href='$pluginsPage' target='_parent'>plugin admin page</a> before accessing this page.");
  }
  return;
}
else if (EZ::isInWP()) { // If in plugin mode, use WP login
  header("location: " . wp_login_url($_SERVER['PHP_SELF']));
  exit();
}

// DB is setup?
foreach ($tablesRequired as $table) {
  if (!$db->tableExists($table)) {
    header('location: dbSetup.php?error=1');
    exit;
  }
}

// Admin is setup?
$table = 'administrator';
$row = $db->getData($table);
if (empty($row)) {
  header('location: adminSetup.php');
  exit;
}

// Logged in?
if (!EZ::isLoggedIn()) {
  header("Location: login.php?error=3&back={$_SERVER['REQUEST_URI']}");
  exit;
}
