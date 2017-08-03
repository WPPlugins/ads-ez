<?php

if (!class_exists("EZCom")) {

  class EZCom {

    static $options = array();
    static $salt = "";
    static $isPro = false;
    static $slug, $wpslug;
    static $class;
    static $name;
    static $timeLookup = array(0 => 'Default', 60 => 'One Minute', 3600 => "One Hour", 86400 => "One Day", 604800 => "One Week", 2592000 => "One Month", 31104000 => "One Year");

    static function isInstalled() {
      global $db;
      $table = 'administrator';
      return $db->tableExists($table);
    }

    static function getCategories() {
      global $db;
      $rows = $db->getData('categories', array('id', 'name'), array('active' => 1));
      foreach ($rows as $r) {
        $categories[$r['id']] = $r['name'];
      }
      return $categories;
    }

    static function getCatId($name) {
      $categories = self::getCategories();
      $id = array_keys($categories, $name);
      if (!empty($id[0])) {
        return $id[0];
      }
      else {
        return 0;
      }
    }

    static function getCatName($id) {
      $categories = self::getCategories();
      $name = '';
      if (!empty($categories[$id])) {
        $name = $categories[$id];
      }
      return $name;
    }

    static function catNameIsActive($name) {
      $id = self::getCatId($name);
      return !empty($id);
    }

    static function catIdIsActive($id) {
      $name = self::getCatName($name);
      return !empty($name);
    }

    static function md5($password) {
      return md5($password . self::$salt);
    }

    static function authenticate() {
      global $db;
      if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (!empty($_POST['myusername']) && !empty($_POST['mypassword'])) {
          $myusername = $_POST['myusername'];
          $mypassword = $_POST['mypassword'];
          $mypassword = self::md5($mypassword);
          $result = $db->getDataEx('administrator', '*', array("username" => $myusername, "password" => $mypassword));
          $count = count($result);
          // If result matches $myusername and $mypassword, table row must be 1 row
          if ($count == "1") {
            $row = $result[0];
          }
          else {
            $row = 1;
          }
        }
        else {
          $row = 2;
        }
      }
      return $row;
    }

    static function login() {
      if (!session_id()) {
        session_start();
      }
      $row = self::authenticate();
      if (is_array($row)) {
        $_SESSION[self::$slug . '-admin'] = self::md5($row['username']);
        $_SESSION[self::$slug . '-password'] = self::md5($row['password']);
        self::putCookie();
        session_write_close();
        if (!empty($_REQUEST['back'])) {
          $goBack = $_REQUEST['back'];
          header("location: $goBack");
        }
        else {
          header("location: index.php");
        }
      }
      else {
        $error = $row;
        header("location: login.php?error=$error");
        exit();
      }
    }

    static function logout() {
      session_start();
      session_unset();
      session_destroy();
      session_write_close();
      setcookie(session_name(), '', 0, '/');
      session_regenerate_id(true);
      header("Location: login.php?error=3");
      exit();
    }

    static function isActive() {
      if (strpos(__FILE__, 'mu-plugins') !== false) {
        return true;
      }
      if (class_exists(self::$class)) {
        return true;
      }
      if (!function_exists('is_plugin_active')) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
      }
      $plgSlug = basename(__DIR__) . "/" . self::$slug . ".php";
      if (is_plugin_active($plgSlug)) {
        return true;
      }
      if (is_plugin_active_for_network($plgSlug)) {
        return true;
      }
      return false;
    }

    static function isInWP() {
      if (isset($_REQUEST['wp'])) {
        return true;
      }
      if (function_exists('is_user_logged_in')) {
        return true;
      }
      return false;
    }

    static function isLoggedInWP() {
      $isLoggedIn = false;
      if (function_exists('current_user_can')) {
        if (current_user_can('activate_plugins')) {
          $isLoggedIn = true;
        }
      }
      return $isLoggedIn;
    }

    static function putCookie() {
      if (empty(self::$options['persist_login']) ||
              self::$options['persist_login'] == self::$timeLookup[0]) {
        return;
      }
      global $db;
      $name = self::$slug . '-auth';
      $value = self::randString();
      $auth = array('cookie_value' => $value,
          'admin' => $_SESSION[self::$slug . '-admin'],
          'password' => $_SESSION[self::$slug . '-password']);
      $db->putMetaData('options_meta', array('login_cookie' => serialize($auth)));
      $timeLookup = array_flip(self::$timeLookup);
      $ttl = $timeLookup[self::$options['persist_login']];
      setcookie($name, $value, time() + $ttl);
    }

    static function getCookie() {
      if (empty(self::$options['persist_login']) ||
              self::$options['persist_login'] == self::$timeLookup[0]) {
        return;
      }
      global $db;
      $data = $db->getMetaData('options_meta', array('name' => 'login_cookie'));
      if (empty($data)) {
        return;
      }
      $auth = unserialize($data['login_cookie']);
      $_SESSION[self::$slug . '-admin'] = '';
      $_SESSION[self::$slug . '-password'] = '';
      $name = self::$slug . '-auth';
      if (empty($_COOKIE[$name])) {
        return;
      }
      $value = $_COOKIE[$name];
      if (!is_array($auth) ||
              empty($auth['cookie_value']) ||
              $auth['cookie_value'] != $value) {
        $db->putMetaData('options_meta', array('login_cookie' => false));
        return;
      }
      if (!empty($auth['admin']) && !empty($auth['password'])) {
        $_SESSION[self::$slug . '-admin'] = $auth['admin'];
        $_SESSION[self::$slug . '-password'] = $auth['password'];
      }
    }

    static function isLoggedIn() {
      if (!session_id()) {
        @session_start();
        session_write_close();
      }
      if (self::isLoggedInWP()) {
        return true;
      }
      else {
        if (self::isInWP()) {
          return false;
        }
      }
      self::getCookie();
      if (empty($_SESSION[self::$slug . '-admin'])) {
        return false;
      }
      if (empty($_SESSION[self::$slug . '-password'])) {
        return false;
      }
      global $db;
      $result = $db->getData('administrator', '*');
      $row = $result[0];
      $admin = self::md5($row['username']);
      $password = self::md5($row['password']);
      $isLoggedin = $_SESSION[self::$slug . '-admin'] == $admin &&
              $_SESSION[self::$slug . '-password'] == $password;
      if (!$isLoggedin) {
        self::logout();
      }
      return $isLoggedin;
    }

    static function __($s) {
      if (isset(self::$options[$s])) {
        return self::$options[$s];
      }
      else {
        return $s;
      }
    }

    static function mkDateString($intOrStr) {
      if (is_int($intOrStr)) {
        $dateStr = date('Y-m-d H:i:s', $intOrStr);
      }
      else {
        $dateStr = date('Y-m-d H:i:s', strtotime($intOrStr));
      }
      return $dateStr;
    }

    static function mkDateInt($intOrStr) {
      if (is_int($intOrStr)) {
        $dateInt = $intOrStr;
      }
      else {
        $dateInt = strtotime($intOrStr);
      }
      return $dateInt;
    }

    static function getBaseUrl() {
      if (isset($_SERVER['HTTPS']) and ( $_SERVER['HTTPS'] == "on")) {
        $http = "https://";
        $ssl = true;
      }
      else {
        $http = "http://";
        $ssl = false;
      }
      $port = $_SERVER['SERVER_PORT'];
      $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
      $url = $http . $_SERVER['SERVER_NAME'] . $port;
      return $url;
    }

    static function handleImageTagsHtml($html) {
      $matches = array();
      $pattern = '/{img:([^ ]*?)}/';
      preg_match_all($pattern, $html, $matches);
      $tags = $matches[0];
      $images = $matches[1];
      $img = array();
      $imgSrc = "assets/";
      if (!empty($images) && is_array($images)) {
        foreach ($images as $i) {
          $img[] = "<img src='$imgSrc/$i' alt='$i' />";
        }
      }
      $html = str_replace($tags, $img, $html);

      return $html;
    }

    static function sendMail($subject, $message, $to) {
      $options = self::getOptions();
      $from = $options['support_email'];
      $headers = sprintf('From: "%s" <%s>' . "\r\n" .
              "Reply-To: %s\r\n" .
              "X-Mailer: %s\r\n" .
              "X-Sender-IP: %s\r\n" .
              "Bcc: %s\r\n", $options['support_name'], $from, $from, self::ezppURL(), $_SERVER['REMOTE_ADDR'], $from);
      $params = "-$from";
      $result = false;
      if (function_exists('wp_mail')) {
        $result = wp_mail($to, $subject, $message, $headers);
      }
      if (!$result) { // fall back to php mail
        $result = mail($to, $subject, $message, $headers, $params);
      }
      if (!$result) {
        throw new Exception(__("Error sending PHP email", self::$slug));
      }
      return true;
    }

    static function urlExists($url) {
      $c = curl_init();
      curl_setopt($c, CURLOPT_URL, $url);
      curl_setopt($c, CURLOPT_HEADER, 1); //get the header
      curl_setopt($c, CURLOPT_NOBODY, 1); //and *only* get the header
      curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); //get the response as a string from curl_exec(), rather than echoing it
      curl_setopt($c, CURLOPT_FRESH_CONNECT, 1); //don't use a cached version of the url
      if (!curl_exec($c)) {
        return false;
      }
      else {
        return true;
      }
    }

    static function validate_url($url) {
      $format = "Use the format http[s]://[www].site.com[/file[?p=v]]";
      if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $text = "$format";
        return $text;
      }
      $pattern = '#^(http(?:s)?\:\/\/[a-zA-Z0-9\-]+(?:\.[a-zA-Z0-9\-]+)*\.[a-zA-Z]{2,6}(?:\/?|(?:\/[\w\-]+)*)(?:\/?|\/\w+\.[a-zA-Z]{2,4}(?:\?[\w]+\=[\w\-]+)?)?(?:\&[\w]+\=[\w\-]+)*)$#';
      if (!preg_match($pattern, $url)) {
        $text = "$format";
        return $text;
      }
      if (!self::urlExists($url)) {
        $text = "URL not accessible";
        return $text;
      }
      return true;
    }

    static function validate_email($s) {
      if (!filter_var($s, FILTER_VALIDATE_EMAIL)) {
        return "Bad email address";
      }
      return true;
    }

    static function validate_notNull($s) {
      $s = trim($s);
      if (empty($s)) {
        return "Null value not allowed";
      }
      return true;
    }

    static function validate_number($s) {
      if (!is_numeric($s)) {
        return "Need a number here";
      }
      return true;
    }

    static function validate_alnum($s) {
      $aValid = array('_', '-');
      $s = str_replace($aValid, '', $s);
      if (!ctype_alnum($s)) {
        return "Please use only letters, numbers, - and _";
      }
      return true;
    }

    static function validate_filename($s) {
      if (preg_match("[^/?*:;{}\\]+\\.[^/?*:;{}\\]+", $s)) {
        return "Illegal characters in the file name. Please try again.";
      }
      else {
        return true;
      }
    }

    static function updateMetaData($table, $pk, $name, $value) {
      global $db;
      $row = array();
      switch ($table) {
        case 'options_meta':
          $row[$pk] = $value;
          $status = $db->putMetaData($table, $row);
          break;
        case 'subscribe_meta': // fake table name
          $table = 'product_meta';
          if (in_array($name, array('pt1', 'pt2', 'pt3'))) {
            $multiRow = array();
            $n = substr($name, -1);
            list($p, $t) = static::decodePT1($value, $n);
            $multiRow[] = array("name" => "p$n", "value" => $p, 'product_id' => $pk);
            $multiRow[] = array("name" => "t$n", "value" => $t, 'product_id' => $pk);
            $status = $db->putData($table, $multiRow);
          }
          else {
            $row['name'] = $name;
            $row['value'] = $value;
            $row['product_id'] = $pk;
            $status = $db->putMetaData($table, $row);
          }
          break;
        case 'product_meta': // Special because both name and value are editable
          $row['id'] = $pk;
          $row[$name] = $value;
          $status = $db->putRowData($table, $row);
          break;
        case 'templates':
          $row['name'] = $name;
          $row['value'] = $value;
          $row['category_id'] = $pk;
          $status = $db->putMetaData($table, $row);
          break;
        default:
          http_response_code(400);
          die("Unknown table accessed: $table");
      }
      return $status;
    }

    // AJAX CRUD implementation.
    // create() and update() are application specific.
    static function read() {
      // not implemented because $db->getData() does a decent job of it
    }

    // AJAX CRUD implementation. Delete.
    static function delete($table) {
      if (!self::isLoggedIn()) {
        http_response_code(400);
        die("Please login before deleting anything from $table!");
      }
      global $db;
      if (!$db->tableExists($table)) {
        http_response_code(400);
        die("Wrong table name: $table!");
      }
      extract($_POST, EXTR_PREFIX_ALL, 'posted');
      if (empty($posted_pk)) {
        http_response_code(400);
        die("Empty primary key to delete!");
      }
      $table = $db->prefix($table);
      $sql = "DELETE FROM $table WHERE `id` = $posted_pk";
      $db->query($sql);
      http_response_code(200);
    }

    static function mkCatNames($showInactive = false) {
      global $db;
      $catNames = array();
      $categories = $db->getData('categories', '*');
      foreach ($categories as $cat) {
        extract($cat);
        if ($active || $showInactive) {
          $catNames[$id] = $name;
        }
        else {
          $catNames[$id] = 'Inactive';
        }
      }
      return $catNames;
    }

    static function mkCatSource($showInactive = false) {
      global $db;
      $catSource = "[";
      $categories = $db->getData('categories', '*');
      foreach ($categories as $cat) {
        extract($cat);
        if ($active || $showInactive) {
          $catSource .= "{value: '$id', text: '$name'},";
        }
      }
      $catSource .= "]";
      return $catSource;
    }

    static function mkSelectSource($options) {
      $source = "[";
      foreach ($options as $o) {
        $source .= "{value: '$o', text: '$o'},";
      }
      $source .= "]";
      return $source;
    }

    static function getId($table, $when) {
      global $db;
      $row = $db->getData($table, 'id', $when);
      return $row[0]['id'];
    }

    static function getOptions() {
      if (!empty(self::$options)) {
        return self::$options;
      }
      global $db;
      if ($db->tableExists('options_meta')) {
        self::$options = $db->getMetaData('options_meta');
      }
      return self::$options;
    }

    static function putDefaultOptions($options) {
      global $db;
      $row = array();
      foreach ($options as $k => $o) {
        $row[$k] = $o['value'];
      }
      $rowDB = $db->getMetaData('options_meta');
      $row = array_merge($row, $rowDB);
      $db->putMetaData('options_meta', $row);
    }

    static function renderOption($pk, $option) {
      $optionsDB = self::getOptions();
      if (isset($optionsDB[$pk])) {
        $value = $optionsDB[$pk];
        $option['value'] = $value;
      }
      return self::renderRow($pk, $option);
    }

    static function renderRow($pk, $option) {
      $value = "";
      $type = 'text';
      $help = $more_help = "";
      $dataValue = "";
      $dataTpl = "";
      $dataMode = "data-mode='inline'";
      $dataSource = "";
      $trClass = $reload = $class = "";
      $name = "";
      $validator = "";
      $tag = "a";
      $options = array();
      extract($option);
      if ($reload) {
        $class .= "xeditReload ";
      }
      if ($type == 'hidden') {
        $tr = '';
        return $tr;
      }
      if (!empty($trClass)) {
        $trClass = " class='$trClass'";
      }
      $dataType = "data-type='$type'";
      if (!empty($more_help)) {
        $clickHelp = "class='btn-help'";
      }
      else {
        $clickHelp = '';
      }
      $tr = "<tr$trClass><td>$name</td>";
      switch ($type) {
        case 'no-edit':
          $class .= "black";
          break;
        case 'checkbox' :
          $class .= "xedit-checkbox";
          $dataType = "data-type='checklist'";
          $dataValue = "data-value='$value'";
          if ($value) {
            $class .= ' btn-sm btn-success';
            $value = "";
          }
          else {
            $class .= ' btn-sm btn-danger';
            $value = "";
          }
          break;
        case 'category':
          $class .= "xedit";
          $dataType = "data-type='select'";
          $dataValue = "data-value='$value'";
          if (!empty($value)) {
            $value = self::getCatName($value);
          }
          $dataSource = 'data-source="' . self::mkCatSource() . '"';
          break;
        case 'select':
          $class .= "xedit";
          $dataType = "data-type='select'";
          $dataValue = "data-value='$value'";
          $dataSource = 'data-source="' . self::mkSelectSource($options) . '"';
          break;
        case 'file':
          $type = '';
          $dataTpl = '';
          $class .= 'red';
          if (!empty($value)) {
            $value = "<span class='success'>File is already uploaded. Use the Browse button below to update it.</span><br>";
          }
          $value .= "<input data-pk='$pk' id='fileinput' type='file' class='file' data-show-preview='false' data-show-upload='false'>";
          $tag = "span";
          break;
        case 'submit':
        case 'button':
          $class = "btn btn-primary btn-ez btn-$class";
          break;
        case 'dbselect':
        case 'dbeditableselect':
        case 'editableselect':
        case 'text':
        case 'textarea':
        default :
          $class .= "xedit";
          if ($dataTpl == 'none') {
            $dataTpl = '';
          }
          else {
            $dataTpl = "data-tpl='<input type=\"text\" style=\"width:450px\">'";
          }
          break;
      }
      if (!empty($validator)) {
        $valid = "data-validator='$validator'";
      }
      else {
        $valid = "";
      }
      if (empty($slug)) {
        $slug = "$pk-value";
      }
      if (!empty($button)) {
        $fun = "proc_$reveal";
        if (empty($url)) {
          $url = '#';
        }
        $options = self::getOptions();
        if (!empty($options[$reveal])) {
          $revealOption = $options[$reveal];
        }
        else {
          $revealOption = '';
        }
        if (method_exists("EZ", $fun)) {
          $dataReveal = @EZ::$fun($revealOption);
        }
        else {
          $dataReveal = "data-value='$revealOption' class='btn-sm btn-success reveal'";
        }
        $reveal = "</a><a href='$url' style='float:right' $dataReveal>$button";
      }
      else {
        $reveal = '';
      }
      $tr .= "\n<td style='width:70%'><$tag id='$slug' class='$class' data-name='$slug' data-pk='$pk' data-action='update' $dataType $dataTpl $dataMode $dataValue $dataSource $valid>$value $reveal</$tag></td>\n<td class='center-text'><a style='font-size:1.5em' data-content='$help' data-help='$more_help' data-toggle='popover' data-placement='left' data-trigger='hover' title='$name' $clickHelp><i class='glyphicon glyphicon-question-sign blue'></i></a></td></tr>\n";
      if ($type == 'hidden') {
        $tr = '';
      }
      return $tr;
    }

    static function randString($len = 32) {
      $chars = 'abcdefghijklmnopqrstuvwxyz';
      $chars .= strtoupper($chars) . '0123456789';
      $charLen = strlen($chars) - 1;
      $string = '';
      for ($i = 0; $i < $len; $i++) {
        $pos = rand(0, $charLen);
        $string .= $chars[$pos];
      }
      return $string;
    }

    static function flashMsg($msg, $class, $noflash = false, $noPre = false) {
      if ($noflash) {
        $fun = "show";
      }
      else {
        $fun = "flash";
      }
      $cleaned = str_replace(array("\n"), array('\n'), $msg);
      if (!empty($cleaned)) {
        $msg = htmlspecialchars($cleaned);
        if (!$noPre) {
          $msg = "<pre>$msg</pre>";
        }
        echo '<script>$(document).ready(function() {' .
        $fun . $class . '("' . $msg . '");
        });
        </script>';
      }
    }

    static function flashError($msg, $noPre = false) {
      self::flashMsg($msg, 'Error', false, $noPre);
    }

    static function showError($msg, $noPre = false) {
      self::flashMsg($msg, 'Error', true, $noPre);
    }

    static function flashWarning($msg, $noPre = false) {
      self::flashMsg($msg, 'Warning', false, $noPre);
    }

    static function showWarning($msg, $noPre = false) {
      self::flashMsg($msg, 'Warning', true, $noPre);
    }

    static function flashSuccess($msg, $noPre = false) {
      self::flashMsg($msg, 'Success', false, $noPre);
    }

    static function showSuccess($msg, $noPre = false) {
      self::flashMsg($msg, 'Success', true, $noPre);
    }

    static function flashInfo($msg, $noPre = false) {
      self::flashMsg($msg, 'Info', false, $noPre);
    }

    static function showInfo($msg, $noPre = false) {
      self::flashMsg($msg, 'Info', true, $noPre);
    }

    static function isTopMenu() {
      $options = self::getOptions();
      if (!empty($options['menu_placement'])) {
        $menuPlacement = $options['menu_placement'];
      }
      else {
        $menuPlacement = 'Auto';
      }
      if (self::isInWP()) { // loaded in iFrame?
        $inFrame = isset($_REQUEST['inframe']);
        if (!$inFrame) {
          $inFrame = !empty($_SERVER["HTTP_REFERER"]) && strpos($_SERVER["HTTP_REFERER"], 'wp-admin/options-general.php') !== false;
        }
      }
      else {
        $inFrame = false;
      }
      $topMenu = $menuPlacement == 'Top' ||
              ($menuPlacement == 'Auto' && $inFrame);
      return $topMenu;
    }

    static function toggleMenu($header) {
      if (self::isTopMenu()) {
        $search = array('<div class="col-sm-2 col-lg-2">',
            '<div class="sidebar-nav">',
            '<div class="nav-canvas">',
            '<ul class="nav nav-pills nav-stacked main-menu">',
            '<li class="accordion">',
            '<li class="accordion ">',
            '<li class="accordion dynamic-menu">',
            '<ul class="nav nav-pills nav-stacked">',
            '<a href="#">',
            '<div id="content" class="col-lg-10 col-sm-10">');
        $replace = array('<div>',
            '<div>',
            '<div>',
            '<ul class="nav nav-pills main-menu">',
            '<li class="dropdown">',
            '<li class="dropdown">',
            '<li class="dropdown">',
            '<ul class="dropdown-menu">',
            '<a href="#" data-toggle="dropdown">',
            '<div id="content" class="col-lg-12 col-sm-12">');
        $header = str_replace($search, $replace, $header);
      }
      return $header;
    }

    static function showService() {
      $select = rand(0, 4);
      echo "<div class='pull-right' style='margin-left:10px;'><a href='http://www.thulasidas.com/professional-php-services/' target='_blank' class='popup-long' title='Professional Services' data-content='The author of this plugin may be able to help you with your WordPress or plugin customization needs and other PHP related development. Find a plugin that almost, but not quite, does what you are looking for? Need any other professional PHP/jQuery dev services? Click here!' data-toggle='popover' data-trigger='hover' data-placement='left'><img src='img/svcs/300x250-0$select.jpg' style='border:0' alt='Professional Services from the Plugin Author' /></a></div>";
    }

    static function isUpdateAvailable() { // not ready yet
      return false;
    }

  }

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

// To prevent warning notices from date()
if (!ini_get('date.timezone')) {
  date_default_timezone_set('GMT');
}

// Extra local settings, if any.
// Visit http://support.thulasidas.com if you need help with this feature.
if (file_exists(__DIR__ . '../ez-local.php')) { // in root or plugin folder
  include_once __DIR__ . '../ez-local.php';
}
else if (defined('ABSPATH')) {
  if (file_exists(ABSPATH . "../ez-local.php")) { // in blogroot parent
    include_once ABSPATH . "../ez-local.php";
  }
  else if (file_exists(ABSPATH . "ez-local.php")) { // in blogroot
    include_once ABSPATH . "ez-local.php";
  }
}