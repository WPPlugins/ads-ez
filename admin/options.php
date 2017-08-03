<?php
require 'header.php';
?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Configuration</a>
    </li>
  </ul>
</div>

<?php
require_once 'OptionTable.php';
require_once('options-default.php');
openBox("Configuration Options", "th-list", 10, "The table below is editable. You can click on the option values and enter new values.  Hover over the <i class='glyphicon glyphicon-question-sign blue'></i> <b>Help</b> button on the row for quick info on what that option does.");
$optionTable = new OptionTable($options);
$optionTable->render();
closeBox();
require 'footer.php';
