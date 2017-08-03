<?php

require_once('../../EZ.php');
require_once '../Updater.php';

$updater = new Updater('ads-ez');
$updater->name = "Ads EZ";
$updater->toVerify = array('ads-ez.php', 'wp-ads-ez.php', 'ad.php', 'adServer.php');

$updater->handle();

