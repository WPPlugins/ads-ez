<?php

require_once('EZ.php');

function updateStats($category = 0, $bannerId = 0, $htmlAdsId = 0) {
  $options = EZ::getOptions();
  if (!empty($options['enable_stats'])) {
    global $db;
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
      $ipAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
    }
    $data = array('banner_id' => $bannerId,
        'htmlAds_id' => $htmlAdsId,
        'category_id' => $category,
        'language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
        'cookie' => $_SERVER['HTTP_COOKIE'],
        'ip_address' => $ipAddress,
        'host_name' => $_SERVER['REMOTE_HOST'],
        'https' => !empty($_SERVER['HTTPS']),
        'domain' => $_SERVER[''],
        'referer' => $_SERVER['HTTP_REFERER'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT']);
    $db->putRowDataDelayed('stats_impressions', $data);
  }
}

function showBanner($banner) { // print the iFrame source
  // run the current afr.php, and generate output similar to that.
  $options = EZ::getOptions();
  $badgeTarget = $options['badge_target'];
  $badgeLong = $options['badge_long_text'];
  $badgeShort = $options['badge_short_text'];
  $badgeEnabled = $options['badge_enable'];
  if (!empty($options['cdn_enable']) && !empty($options['cdn_url'])) {
    $cdnUrl = $options['cdn_url'];
  }
  else {
    $cdnUrl = '';
  }
  if (!empty($cdnUrl) && substr($cdnUrl, -1) != '/') {
    $cdnUrl .= '/';
  }
  extract($banner); // defines target and title
  echo <<<EOF0
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
  <head>
  <title>Easy Ads for WordPress</title>
  <style type='text/css'>
    body {margin:0; height:100%; background-color:transparent; width:100%; text-align:center;}
    #ezwrapper {border: 0px; margin: 0; padding: 0; position: relative;}#ezfooter {position:absolute;bottom:0;border-width:1px;border-color:#000;height:12px;padding:1px;font-family:Arial;font-size:10px;font-weight:bold;color:#fff;background:#b00;} #ezfooter a:link, #ezfooter a:visited, #ezfooter a:hover {color:#fff;text-decoration:none;} </style>
  </head>
  <body>
    <div id="ezwrapper"><a href='ck.php?url=$target' target='_blank'><img src='$cdnUrl$file' width='$width' height='$height' alt='$title' title='$title' border='0' /></a>
EOF0;
  if ($badgeEnabled) {
    echo <<<EOF1
      <div id='ezfooter'><a href='$badgeTarget' id='ezlink' target='_blank' onmouseover='this.innerHTML="$badgeLong"' onmouseout='this.innerHTML="$badgeShort"'>$badgeShort</a></div>
EOF1;
  }
  echo <<<EOF3
    </div>
  </body>
</html>
EOF3;
  @updateStats($category, $id);
}

function showHtml($htmlAd) { // print the iFrame source
  // run the current afr.php, and generate output similar to that.
  $options = EZ::getOptions();
  $badgeTarget = $options['badge_target'];
  $badgeLong = $options['badge_long_text'];
  $badgeShort = $options['badge_short_text'];
  $badgeEnabled = $options['badge_enable'];
  extract($htmlAd); // defines target and title
  $html = stripslashes($html);
  echo <<<EOF0
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
  <head>
  <title>Ads EZ AdServer</title>
  <style type='text/css'>
    body {margin:0; height:100%; background-color:transparent; width:100%; text-align:center;}
    #ezwrapper {border: 0px; margin: 0; padding: 0; position: relative;}#ezfooter {position:absolute;bottom:0;border-width:1px;border-color:#000;height:12px;padding:1px;font-family:Arial;font-size:10px;font-weight:bold;color:#fff;background:#b00;} #ezfooter a:link, #ezfooter a:visited, #ezfooter a:hover {color:#fff;text-decoration:none;} </style>
  </head>
  <body>
    <div id="ezwrapper">$html
EOF0;
  if ($badgeEnabled) {
    echo <<<EOF1
      <div id='ezfooter'><a href='$badgeTarget' id='ezlink' target='_blank' onmouseover='this.innerHTML="$badgeLong"' onmouseout='this.innerHTML="$badgeShort"'>$badgeShort</a></div>
EOF1;
  }
  echo <<<EOF3
    </div>
  </body>
</html>
EOF3;
  @updateStats($category, 0, $id);
}

function showDefault($size) {
  $options = EZ::getOptions();
  $badgeEnabled = $options['badge_enable'];
  if ($badgeEnabled) {
    $badgeTarget = $options['badge_target'];
  }
  else {
    $badgeTarget = "#";
  }
  $badgeLong = $options['badge_long_text'];
  $badgeShort = $options['badge_short_text'];
  $fallBack = $options['fallback_ad'];
  list($width, $height) = explode("x", $size);
  $fontsize = $height * $width / 5000;
  $large = $fontsize * 1.2;
  $small = $fontsize / 1.1;
  $fontsize .= "pt";
  $large .= "pt";
  $small .= "pt";
  $height /=3.5;
  $height .= "px";
  $title = "Ads EZ";
  if (file_exists($fallBack)) {
    include($fallBack);
  }
  else {
    include('fallback.php');
  }
  @updateStats();
}

// iFrame source & impression counter
function afr($zoneid = 1) {
  $luZoneID = EZ::getOpenX();
  if (!empty($luZoneID[$zoneid])) {
    $size = $luZoneID[$zoneid];
    if (!empty($size)) {
      // pick an ad
      $ad = EZ::getAdBySize($size, "", true);
      showAd($ad, $size);
      return;
    }
  }
  else {
    // Unknown zoneid requested. Log/email the error
    @trigger_error("Unknown zoneid ($zoneid) requested", E_USER_ERROR);
    showDefault($size);
  }
}

// Click counter and URL forwarder
function validateUrl($url) {
  $targets = EZ::getTargets();
  if (in_array($url, $targets)) {
    return true;
  }
  return false;
}

function mkUrl($oaParams) {
  $url = '';
  $params = explode("__", $oaParams);
  foreach ($params as $p) {
    $pos = strpos($p, "oadest");
    if ($pos !== false) {
      $parts = explode("=", $p);
      $url = $parts[1];
    }
  }
  return $url;
}

function ck($url) {
  if (validateUrl($url)) {
    header("location:$url");
    // SQL delayed insert here for stats, if needed
    // delayedInsert(table_clicks, time(), $target)
  }
  else if (!empty($_GET['n'])) { // coming from an iFrame fallback, seldom happens
    $url = "http://www.thulasidas.com/render/";
    header("location:$url");
    // trigger_error("ck.php asked handle iFrame fallback. (n = {$_GET['n']}). Banner and its target may not match. Investigate", E_USER_ERROR);
  }
  else {
    $url = "http://www.thulasidas.com/render/";
    header("location:$url");
    // trigger_error("ck.php asked to forward to $url, not allowed", E_USER_ERROR);
  }
}

function avw($zoneid) { // coming from an iFrame fallback, seldom happens
  $luZoneID = EZ::getOpenX();
  if (!empty($luZoneID[$zoneid])) {
    $size = $luZoneID[$zoneid];
    if (!empty($size)) {
      // pick an ad
      $banner = EZ::getAdBySize($size, "banners", true);
      if (!empty($banner)) {
        $banner = $banner['file'];
        $ext = pathinfo($banner, PATHINFO_EXTENSION);
        if (!empty($ext)) {
          header("Content-Type: image/$ext");
          header('Content-Length: ' . filesize($banner));
          readfile($banner);
        }
        else {
          trigger_error("Empty mime type for banner $banner.", E_USER_ERROR);
        }
      }
    }
    else {
      // Unknown zoneid requested. Log/email the error
      trigger_error("Unknown zoneid ($zoneid) requested", E_USER_ERROR);
    }
  }
  else {
    // Unknown zoneid requested. Log/email the error
    trigger_error("Empty zoneid!", E_USER_ERROR);
  }
}

function showAd($ad, $size) {
  if (!empty($ad)) {
    if (isset($ad['file'])) {
      showBanner($ad);
      return;
    }
    else if (isset($ad['html'])) {
      showHtml($ad);
      return;
    }
  }
  // nothing shown?
  showDefault($size);
  return;
}

function displayAd($category = "", $type = "", $size = "300x250") {
  $ad = EZ::getAdByCategory($category, $type, $size, true);
  showAd($ad, $size);
}
