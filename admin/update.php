<?php
require 'header.php';
require_once 'Updater.php';
$updater = new Updater('ads-ez');
$updater->name = "Ads EZ";
$updater->price = "15.95";
$updater->render();
require 'footer.php';
