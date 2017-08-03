<?php

// TODO: Wrap most of these functions in a static class EzLang
// to avoid possible name collisions and general encapsulation

if (class_exists('AbstractInstaller') || class_exists('EZ')) {

  if (!function_exists('__')) {

    function __($s) {
      return $s;
    }

    function _e($s) {
      echo $s;
    }

  }

  function getLangs() {
    $locales = array('en_US');
    return $locales;
  }

}
