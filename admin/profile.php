<?php

require_once('header.php');
require_once '../EZ.php';
require_once 'Installer.php';

$installer = new Installer();

$installer->configure();


$current = $db->getRowData('administrator');
$installer->verifyAdmin($current);
$installer->printAdminForm($current);
