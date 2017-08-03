<?php

if (!class_exists("AdsEZ")) {

  require_once 'EzPlugin.php';

  class AdsEZ extends EzPlugin {

    public function __construct() {
      $this->name = 'Ads EZ';
      $this->adminLogo = $this->mainLogo = $this->key = "ads-ez";
      $this->slogan = "Your Own Ad Server";
      $this->class = __CLASS__;
      parent::__construct(__DIR__ . '/ads-ez.php');
    }

    static function install($dir = '', $mOptions = 'adsEz') {
      parent::install(__DIR__, 'adsEz');
    }

    static function uninstall($mOptions = 'adsEz') {
      parent::uninstall('adsEz');
    }

    function displayAd($atts, $content = '') {
      $dlim = "?";
      $query = "";
      $vars = array("cat" => "", "type" => "banner", "size" => "300x250");
      $vars = shortcode_atts($vars, $atts);
      foreach ($vars as $k => $v) {
        if (!empty($v)) {
          $query .= "$dlim$k=$v";
          $dlim = "&";
        }
      }
      list($w, $h) = explode("x", $vars['size']);
      $adLink = "<iframe src='$this->siteUrl/$this->keyEp/ad.ezp$query' frameborder='0 scrolling='no' width='$w' height='$h'></iframe>";
      return $adLink;
    }

  }

} //End Class AdsEZ
