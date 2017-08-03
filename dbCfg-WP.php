<?php

global $wpdb, $appPrefix;
if (!defined('DB_HOST') || empty($wpdb)) {
  die("Not in WP!");
}

$dbHost = DB_HOST;
$dbName = DB_NAME;
$dbUsr = DB_USER;
$dbPwd = DB_PASSWORD;
$dbEmail = "";

if (!empty($wpdb->base_prefix)) {
  $table_prefix = $wpdb->prefix;
}
else if (!empty($wpdb->prefix)) {
  $table_prefix = $wpdb->prefix;
}
else {
  $table_prefix = 'wp_';
}
$dbPrefix = $table_prefix . $appPrefix;
