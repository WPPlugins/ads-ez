<?php

require_once('../../EZ.php');

if (!EZ::isLoggedIn()) {
  http_response_code(400);
  die("Please login before accessing sales info!");
}

require_once '../OptionTable.php';
OptionTable::handle('options_meta');
