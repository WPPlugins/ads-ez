<?php

require_once 'DbHelper.php';
require_once 'lib/Logger.php';
$GLOBALS['log'] = $log = new Logger();

// Suppress errors on AJAX requests
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
  error_reporting(E_ERROR | E_PARSE);
// CORS headers
  header("access-control-allow-origin: *", true);
  header("access-control-allow-methods: GET, POST, PUT, DELETE, OPTIONS", true);
}

if (!class_exists("EZ")) {

  require 'EZPro.php';

  class EZ extends EZPro {

    static function getAllAds() {
      global $db;
      $banners = $db->getData('banners');
      $bannerTargets = array();
      foreach ($banners as $k => $b) {
        $bannerTargets[$b['file']] = $b;
      }
      return $bannerTargets;
    }

    static function getCatId($name) { // Frontend version of getId with caching
      $key = "active-categories";
      $activeCategories = self::getTransient($key);
      if (!$activeCategories) {
        global $db;
        $rows = $db->getData('categories', array('id', 'name'), array('active' => 1));
        foreach ($rows as $r) {
          $activeCategories[$r['id']] = $r['name'];
        }
        self::setTransient($key, $activeCategories, self::$cacheTimeout);
      }
      $id = array_keys($activeCategories, $name);
      return $id[0];
    }

    static function getCatName($id) { // Frontend version of getId with caching
      $key = "active-categories";
      $activeCategories = self::getTransient($key);
      if (!$activeCategories) {
        global $db;
        $rows = $db->getData('categories', array('id', 'name'), array('active' => 1));
        foreach ($rows as $r) {
          $activeCategories[$r['id']] = $r['name'];
        }
        self::setTransient($key, $activeCategories, self::$cacheTimeout);
      }
      $name = '';
      if (!empty($activeCategories[$id])) {
        $name = $activeCategories[$id];
      }
      return $name;
    }

    static function getAdBySize($size = "300x250", $type = "", $activeOnly = false) {
      return self::getAdByCategory("", $type, $size, $activeOnly);
    }

    static function getAdByCategory($category = "", $type = "", $size = "300x250", $activeOnly = false) {
      $key = "banners-$category-$size";
      $banners = self::getTransient($key);
      if (!$banners) {
        global $db;
        $when = array('size' => $size);
        if ($activeOnly) {
          $when['active'] = 1;
        }
        if (!empty($category) && self::catNameIsActive($category)) {
          $catId = self::getCatId($category);
          $when['category'] = $catId;
        }
        $banners = $db->getData('banners', '*', $when);
        self::setTransient($key, $banners, self::$cacheTimeout);
      }
      $key = "htmlAds-$category-$size";
      $htmlAds = self::getTransient($key);
      if (!$htmlAds) {
        global $db;
        $when = array('size' => $size);
        if ($activeOnly) {
          $when['active'] = 1;
        }
        if (!empty($category) && self::catNameIsActive($category)) {
          $catId = self::getCatId($category);
          $when['category'] = $catId;
        }
        $htmlAds = $db->getData('htmlAds', '*', $when);
        self::setTransient($key, $htmlAds, self::$cacheTimeout);
      }
      if ($type == 'banners') {
        $ads = $banners;
      }
      else if ($type == 'htmlAds') {
        $ads = $htmlAds;
      }
      else {
        $ads = array_merge($banners, $htmlAds);
      }
      if (empty($ads)) {
        // @trigger_error("No banners found for " . print_r($when, true), E_USER_ERROR);
      }
      else {
        $rand = array_rand($ads);
        return $ads[$rand];
      }
    }

    static function getTargets() {
      $key = 'targets';
      $targets = self::getTransient($key);
      if (!$targets) {
        global $db;
        $targets = $db->getColData('banners', 'target');
        self::setTransient($key, $targets, self::$cacheTimeout);
      }
      return $targets;
    }

    static function adsURL() {
      if (function_exists('plugins_url')) {
        $adsURL = plugins_url("/", __FILE__);
        return $adsURL;
      }
      else {
        $docRoot = realpath($_SERVER['DOCUMENT_ROOT']);
        $ezppRoot = __DIR__;
        $self = str_replace($docRoot, '', $ezppRoot);
        $url = self::getBaseUrl() . $self . '/';
        return $url;
      }
    }

    // AJAX CRUD implementation. Create.
    static function create($table) { // creates a new DB record
      if (!EZ::isLoggedIn()) {
        http_response_code(400);
        die("Please login before modifying $table!");
      }
      global $db;
      if (!$db->tableExists($table)) {
        http_response_code(400);
        die("Wrong table name: $table!");
      }
      $row = $_REQUEST;
      if (!empty($row['pk'])) {
        http_response_code(400);
        die("Primary key supplied for new record");
      }
      unset($row['id']);
      if (empty($row)) {
        http_response_code(400);
        die("Empty data");
      }
      switch ($table) {
        case 'banners':
          $file = $row['file'];
          if ($row['target'] == 'Empty' || empty($row['target'])) {
            http_response_code(400);
            die("Empty target for $file!");
          }
          if ($row['category'] == 'Empty' || empty($row['category'])) {
            http_response_code(400);
            die("Empty category for $file!");
          }
          if ($row['title'] == 'Empty') {
            $row['title'] = "";
          }
          $row['md5'] = md5($row['file']);
          $id = self::getId('categories', array('name' => $row['category']));
          $row['category'] = $id;
          $row['size'] = "{$row['width']}x{$row['height']}";
          break;
        case 'htmlAds':
          if ($row['name'] == 'Empty' || empty($row['name'])) {
            http_response_code(400);
            die("Empty name!");
          }
          if ($row['category'] == 'Empty' || empty($row['category'])) {
            http_response_code(400);
            die("Empty category!");
          }
          if ($row['html'] == 'Empty' || empty($row['html'])) {
            http_response_code(400);
            die("Empty HTML code!");
          }
          if ($row['height'] == 'Empty' || empty($row['height'])) {
            http_response_code(400);
            die("Height not specified!");
          }
          if ($row['width'] == 'Empty' || empty($row['width'])) {
            http_response_code(400);
            die("Width not specified!");
          }
          $id = self::getId('categories', array('name' => $row['category']));
          $row['category'] = $id;
          $row['size'] = "{$row['width']}x{$row['height']}";
          break;
        case 'categories':
          if ($row['name'] == 'Empty' || empty($row['name'])) {
            http_response_code(400);
            die("Empty name!");
          }
          break;
        case 'openx':
          break;
        default:
          http_response_code(400);
          die("Unknown table accessed: $table");
      }
      if (isset($row['active']) && trim($row['active']) == 'Active') {
        $row['active'] = 1;
      }
      else {
        $row['active'] = 0;
      }
      $lastInsertId = $db->getInsertId();
      if (!$db->putRowData($table, $row)) {
        http_response_code(400);
        die("Database Insert Error in $table!");
      }
      $newInserId = $db->getInsertId();
      if ($lastInsertId == $newInserId) {
        http_response_code(400);
        die("Database Insert Error in $table, duplicate unique key!");
      }
      http_response_code(200);
      return $newInserId;
    }

    // AJAX CRUD implementation. Update.
    static function update($table, $meta = false) { // updates an existing DB record
      if (!EZ::isLoggedIn()) {
        http_response_code(400);
        die("Please login before modifying $table!");
      }
      global $db;
      if (!$db->tableExists($table)) {
        http_response_code(400);
        die("Wrong table name: $table!");
      }
      $row = array();
      extract($_POST, EXTR_PREFIX_ALL, 'posted');
      if (empty($posted_pk)) {
        http_response_code(400);
        die("Empty primary key");
      }
      if (empty($posted_name)) {
        http_response_code(400);
        die("Empty name ($posted_name) in data");
      }
      if (!isset($posted_value)) { // Checkbox, unchecked
        $posted_value = 0;
      }
      if (is_array($posted_value)) { // Checkbox (from checklist), checked
        $posted_value = 1;
      }
      if (!empty($posted_validator)) { // a server-side validator is specified
        $fun = "validate_$posted_validator";
        if (method_exists('EZ', $fun)) {
          $valid = self::$fun($posted_value);
        }
        else {
          http_response_code(400);
          die("Unknown validator ($posted_validator) specified");
        }
        if ($valid !== true) {
          http_response_code(400);
          die("$valid");
        }
      }
      if ($meta) {
        $row[$posted_pk] = $posted_value;
        $status = $db->putMetaData($table, $row);
      }
      else if ($table == 'htmlAds' && !empty($posted_html)) { // HTML Ad html code
        $row['id'] = $posted_pk;
        $row['html'] = $posted_html;
        $status = $db->putRowData($table, $row);
      }
      else {
        $row['id'] = $posted_pk;
        $row[$posted_name] = $posted_value;
        $status = $db->putRowData($table, $row);
        self::updateSize($table, $posted_pk);
      }
      if (!$status) {
        http_response_code(400);
        die("Database Insert Error in $table!");
      }
      http_response_code(200);
      echo $posted_pk;
      exit();
    }

  }

}

EZ::$slug = EZ::$wpslug = 'ads-ez';
EZ::$class = "AdsEZ";
EZ::$name = "Ads EZ";
EZ::$isPro = file_exists(__DIR__ . '/admin/options-advanced.php');

// construct DB object after defining EZ
$GLOBALS['appPrefix'] = $appPrefix = 'ads_';
$GLOBALS['db'] = $db = new DbHelper();

if (!EZ::isInWP()) {
  require_once 'admin/lang.php';
}

EZ::$options = EZ::getOptions(); // to prime the static variable and the cache
if (!empty(EZ::$options['salt'])) {
  EZ::$salt = EZ::$options['salt'];
}
if (!empty(EZ::$options['cache_timeout']) && property_exists('EZ', 'cacheTimeout')) {
  EZ::$cacheTimeout = EZ::$options['cache_timeout'];
}

// For 4.3.0 <= PHP <= 5.4.0
if (!function_exists('http_response_code')) {

  function http_response_code($newcode = NULL) {
    static $code = 200;
    if ($newcode !== NULL) {
      if (!headers_sent()) {
        header('X-PHP-Response-Code: ' . $newcode, true, $newcode);
        $code = $newcode;
      }
    }
    return $code;
  }

}
