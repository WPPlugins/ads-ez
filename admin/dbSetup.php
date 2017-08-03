<?php

require_once 'Installer.php';
require_once 'lang.php';

$installer = new Installer();

$installer->configure();
if ($installer->verifyCfg()) {
  $installer->install();
}
$installer->printCfgForm();
