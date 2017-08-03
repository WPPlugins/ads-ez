<?php

require_once 'EZCom.php';

class EZPro extends EZCom {

  static $cacheTimeout;
  
  static function setTransient($key, $val, $timeout = 0) {
    return false;
  }

  static function getTransient($key) {
    return false;
  }

  static function rmTransient($key) {
    return false;
  }

}
