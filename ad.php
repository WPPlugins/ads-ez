<?php
require('adServer.php');
if (!empty($_GET['cat'])) {
  $cat = $_GET['cat'];
}
else {
  $cat = "";
}
if (!empty($_GET['type'])) {
  $type = $_GET['type'];
}
else {
  $type = "";
}
if (!empty($_GET['size'])) {
  $size = $_GET['size'];
}
else {
  $size = "300x250";
}
displayAd($cat, $type, $size);
