<?php

require_once 'AdsEZ.php';

if (class_exists("AdsEZ")) {
  $adsEz = new AdsEZ();

  $adsEz->init();

  add_shortcode('adsez', array($adsEz, 'displayAd'));
  add_shortcode('adsEz', array($adsEz, 'displayAd'));
  add_shortcode('ads-ez', array($adsEz, 'displayAd'));

}

