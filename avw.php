<?php
require('adServer.php');
if (!empty($_GET['zoneid'])) {
  $zoneid = $_GET['zoneid'];
}
else {
  $zoneid = "";
}
avw($zoneid);
