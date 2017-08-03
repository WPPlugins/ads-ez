<?php

require_once '../EZ.php';
require_once 'Installer.php';

$installer = new Installer();

$installer->configure();
$installer->verifyAdmin();
$installer->printAdminForm();
