<?php
require('adServer.php');
if (!empty($_GET['oaparams'])) {
  $oaParams = $_GET['oaparams'];
  $url = mkUrl($oaParams);
}
else if (!empty($_GET['url'])){
  $url = $_GET['url'];
}
else {
  $url = "";
}
ck($url);
