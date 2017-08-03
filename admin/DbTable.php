<?php

// To render DB tables (like products in EZ PayPal or links in EZ Text Links.

class DbTable {

  var $table, $rows, $columns;
  var $showActions = true, $needCreate = false, $needEdit = false;
  var $useDataTable = false, $useServerSide = false;
  var $isOptionTable = false;
  var $ajaxHandler;
  static $renderedJS = false;

  function __construct($table, $columns) {
    global $db;
    $this->table = $table;
    $this->rows = $db->getData($table, '*', 'id', 'asc');
    $this->columns = $columns;
  }

  function render($tableWidth = 12) {
    if ($this->useDataTable) {
      $dataTable = 'data-table';
    }
    else {
      $dataTable = '';
    }
    ?>
    <div class="col-md-<?php echo $tableWidth; ?>">
      <table class="table table-striped table-bordered responsive <?php echo $dataTable; ?>">
        <thead>
          <tr>
            <?php
            static::_printHeader();
            ?>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($this->rows as $row) {
            static::_printRow($row);
          }
          if ($this->needCreate) {
            static::_printCreate();
          }
          ?>
        </tbody>
      </table>
    </div>
    <?php
    if ($tableWidth == 12) {
      ?>
      <div class="clearfix"></div>
      <?php
    }
    static::_printJS();
  }

  private function _printHeader() {
    foreach ($this->columns as $col) {
      $col->printHeader();
    }
    if ($this->showActions) {
      echo "<th>Action</th>";
    }
  }

  private function _printRow($row, $create = false) {
    if ($create) {
      echo "<tr class='warning'>";
    }
    else {
      echo "<tr>";
    }
    $pk = $action = '';
    if (!$create) {
      $pk = $row['id'];
      $action = 'update';
    }
    foreach ($this->columns as $col) {
      if (!empty($row[$col->dbCol])) {
        $value = $row[$col->dbCol];
      }
      else {
        $value = '';
      }
      if (!empty($col->tdClass)) {
        $tdClass = "class='$this->tdClass'";
      }
      else {
        $tdClass = "";
      }
      echo "<td $tdClass>";
      echo $col->sprintCell($pk, $value, $action);
      echo "</td>";
    }
    if ($this->showActions) {
      echo '<td class="center-text" style="min-width:100px">';
      if ($create) {
        echo "<a class='btn-sm btn-warning create' href='#' title='Create a new row' data-toggle='tooltip'><i class='glyphicon glyphicon-plus icon-white'></i> </a>";
      }
      else {
        echo "<a class='btn-sm btn-primary view' href='#' title='View row details' data-toggle='tooltip' data-pk='$pk'><i class='glyphicon glyphicon-zoom-in icon-white'></i> </a>";
        if ($this->needEdit) {
          echo "&nbsp;<a class='btn-sm btn-info edit' href='#' title='Edit this row' data-toggle='tooltip' data-pk='$pk'><i class='glyphicon glyphicon-pencil icon-white'></i> </a>";
        }
      }
      echo "</td>";
    }
    echo "</tr>";
  }

  private function _printCreate() {
    self::_printRow(array(), true);
  }

  private function _printJS() {
    if (self::$renderedJS) {
      return;
    }
    self::$renderedJS = true;
    if (empty($this->ajaxHandler)) {
      throw new Exception("Please set ajaxHandler!");
    }
    $ajaxCols = array();
    foreach ($this->columns as $col) {
      $ajaxCols[] = "'$col->dbCol'";
    }
    $ajaxCols = implode(", ", $ajaxCols);
    ?>
    <script>
      var xeditHandler = '<?php echo $this->ajaxHandler; ?>';
      $(".view").click(function () {
        $.ajax({url: xeditHandler,
          type: 'GET',
          data: {
            action: 'view',
            pk: $(this).attr('data-pk')
          },
          success: function (a) {
            bootbox.alert(a);
          },
          error: function (a) {
            showError(a.responseText);
          }
        });
      });
      $(".edit").click(function () {
        $.ajax({url: xeditHandler,
          type: 'GET',
          data: {
            action: 'edit',
            pk: $(this).attr('data-pk')
          },
          success: function (a) {
            bootbox.alert(a, function () {
              ezReload();
            });
            initXedit();
          },
          error: function (a) {
            showError(a.responseText);
          }
        });
      });
      $(".create").click(function () {
        var data = {};
        $.each([<?php echo $ajaxCols; ?>], function (i, val) {
          var newValue = $("a[data-pk=''][data-name='" + val + "']").text();
          if (newValue !== 'Empty') {
            data[val] = newValue;
          }
        });
        data['action'] = 'create';
        $.ajax({url: xeditHandler,
          type: 'POST',
          data: data,
          success: function () {
            ezReload();
          },
          error: function (a) {
            showError(a.responseText);
          }
        });
      });
    </script>
    <?php
  }

  // AJAX handler
  static function handle($table, $column = "", $title = "", $columns = array()) {
    if (!empty($_REQUEST['action'])) {
      $action = $_REQUEST['action'];
    }
    else {
      $action = '';
    }
    unset($_REQUEST['file']);
    switch ($action) {
      case 'create':
        echo self::create($table);
        break;
      case 'update':
        self::update($table);
        break;
      case 'view':
        self::view($table, $column, $title);
        break;
      case 'edit':
        self::edit($table, $column, $columns);
        break;
      default:
        break;
    }
    exit;
  }

  // View a row in a table
  static function view($table, $dbCol, $format = "") {
    if (empty($_REQUEST['pk'])) {
      http_response_code(400);
      die("No primary key supplied for sales record");
    }
    $pk = $_REQUEST['pk'];
    global $db;
    if (!$db->tableExists($table)) {
      http_response_code(400);
      die("Wrong table name: $table!");
    }
    $rows = $db->getData($table, '*', array('id' => "$pk"));
    if (!empty($rows)) {
      $row = $rows[0];
    }
    if (empty($format)) {
      $title = ucwords(str_replace("_", " ", $table)) . ": Details for " . $row[$dbCol];
    }
    else {
      $title = sprintf($format, $row[$dbCol]);
    }
    http_response_code(200);
    ?>
    <h3><?php echo $title; ?> </h3>
    <table class="table table-striped table-bordered responsive">
      <tbody>
        <?php
        foreach ($row as $key => $val) {
          $attr = ucwords(str_replace("_", " ", $key));
          echo "<tr><td>$attr</td><td>$val</td></tr>";
        }
        ?>
      </tbody>
    </table>
    <?php
  }

  // View a row in a table
  static function edit($table, $dbCol, $columns) {
    if (empty($_REQUEST['pk'])) {
      http_response_code(400);
      die("No primary key supplied for sales record");
    }
    $pk = $_REQUEST['pk'];
    global $db;
    if (!$db->tableExists($table)) {
      http_response_code(400);
      die("Wrong table name: $table!");
    }
    $rows = $db->getData($table, '*', array('id' => "$pk"));
    if (!empty($rows)) {
      $row = $rows[0];
    }
    $title = ucwords(str_replace("_", " ", $table)) . ": Editing $dbCol = {$row[$dbCol]}";
    http_response_code(200);
    ?>
    <h3><?php echo $title; ?> </h3>
    <table class="table table-striped table-bordered responsive">
      <tbody>
        <?php
        foreach ($row as $key => $val) {
          $attr = ucwords(str_replace("_", " ", $key));
          if (!empty($columns[$key])) {
            $xValue = $columns[$key]->sprintCell($pk, $val, 'update');
          }
          else {
            $xValue = "<a href='#' class='xedit' data-name='$key' data-type='text' data-pk='$pk' data-title='$attr' data-action='update' data-value='$val'>$val</a>";
          }
          echo "<tr><td>$attr</td><td>$xValue</td></tr>";
        }
        ?>
      </tbody>
    </table>
    <?php
  }

  // Validators
  static function validate_url($url) {
    $format = "Use the format http[s]://[www].site.com[/file[?p=v]]";
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
      $text = "$format";
      return $text;
    }
    $pattern = '#^(http(?:s)?\:\/\/[a-zA-Z0-9\-]+(?:\.[a-zA-Z0-9\-]+)*\.[a-zA-Z]{2,6}(?:\/?|(?:\/[\w\-]+)*)(?:\/?|\/\w+\.[a-zA-Z]{2,4}(?:\?[\w]+\=[\w\-]+)?)?(?:\&[\w]+\=[\w\-]+)*)$#';
    if (!preg_match($pattern, $url)) {
      $text = "$format";
      return $text;
    }
    if (!self::urlExists($url)) {
      $text = "URL not accessible";
      return $text;
    }
    return true;
  }

  static function validate_email($s) {
    if (!filter_var($s, FILTER_VALIDATE_EMAIL)) {
      return "Bad email address";
    }
    return true;
  }

  static function validate_notNull($s) {
    $s = trim($s);
    if (empty($s)) {
      return "Null value not allowed";
    }
    return true;
  }

  static function validate_number($s) {
    if (!is_numeric($s)) {
      return "Need a number here";
    }
    return true;
  }

  static function validate_alnum($s) {
    $aValid = array('_', '-');
    $s = str_replace($aValid, '', $s);
    if (!ctype_alnum($s)) {
      return "Please use only letters, numbers, - and _";
    }
    return true;
  }

  static function updateMetaData($table, $pk, $name, $value) {
    global $db;
    $row = array();
    switch ($table) {
      case 'options_meta':
        $row[$pk] = $value;
        $status = $db->putMetaData($table, $row);
        break;
      case 'subscribe_meta': // fake table name
        $table = 'product_meta';
        if (in_array($name, array('pt1', 'pt2', 'pt3'))) {
          $multiRow = array();
          $n = substr($name, -1);
          list($p, $t) = self::decodePT1($value, $n);
          $multiRow[] = array("name" => "p$n", "value" => $p, 'product_id' => $pk);
          $multiRow[] = array("name" => "t$n", "value" => $t, 'product_id' => $pk);
          $status = $db->putData($table, $multiRow);
        }
        else {
          $row['name'] = $name;
          $row['value'] = $value;
          $row['product_id'] = $pk;
          $status = $db->putMetaData($table, $row);
        }
        break;
      case 'product_meta': // Special because both name and value are editable
        $row['id'] = $pk;
        $row[$name] = $value;
        $status = $db->putRowData($table, $row);
        break;
      case 'templates':
        $row['name'] = $name;
        $row['value'] = $value;
        $row['category_id'] = $pk;
        $status = $db->putMetaData($table, $row);
        break;
      default:
        http_response_code(400);
        die("Unknown table accessed: $table");
    }
    return $status;
  }

  // AJAX CRUD implementation. Create.
  static function create($table) { // creates a new DB record
    if (!EZ::isLoggedIn()) {
      http_response_code(400);
      die("Please login before modifying $table!");
    }
    global $db;
    if (!$db->tableExists($table) && $table != 'subscribe_meta') {
      http_response_code(400);
      die("Wrong table name: $table!");
    }
    $row = $_REQUEST;
    if (!empty($row['pk'])) {
      http_response_code(400);
      die("Primary key supplied for new record");
    }
    unset($row['id']);
    if (empty($row)) {
      http_response_code(400);
      die("Empty data");
    }
    switch ($table) {
      case 'links':
      case 'link_products':
        if (!empty($row['category_id'])) {
          $row['category_id'] = EZ::getCatId($row['category_id']);
        }
        if (!empty($row['status'])) {
          $row['status_date'] = self::mkDateString(time());
        }
        break;
      case 'categories':
        if ($row['name'] == 'Empty' || empty($row['name'])) {
          http_response_code(400);
          die("Empty name!");
        }
        break;
      case 'product_meta':
        break;
      default:
        http_response_code(400);
        die("Unknown table accessed: $table");
    }
    if (isset($row['active']) &&
            (trim($row['active']) == 'Active') || trim($row['active']) == 'Yes') {
      $row['active'] = 1;
    }
    else {
      $row['active'] = 0;
    }
    $lastInsertId = $db->getInsertId();
    if (!$db->putRowData($table, $row)) {
      http_response_code(400);
      die("Database Insert Error in $table!");
    }
    $newInserId = $db->getInsertId();
    if ($lastInsertId == $newInserId) {
      http_response_code(400);
      die("Database Insert Error in $table, duplicate unique key!");
    }
    http_response_code(200);
    return $newInserId;
  }

  // AJAX CRUD implementation. Delete.
  static function read($table) {
    // Not used. Only for completenss.
    if (!EZ::isLoggedIn()) {
      http_response_code(400);
      die("Please login before reading anything from $table!");
    }
    global $db;
    if (!$db->tableExists($table)) {
      http_response_code(400);
      die("Wrong table name: $table!");
    }
    $posted_pk = '';
    extract($_POST, EXTR_PREFIX_ALL, 'posted');
    if (empty($posted_pk)) {
      http_response_code(400);
      die("Empty primary key to read!");
    }
    http_response_code(200);
    return $db->getDataEx($table, '*', array('id' => 'pk'));
  }

  // AJAX CRUD implementation. Update.
  static function update($table, $meta = false) { // updates an existing DB record
    if (!EZ::isLoggedIn()) {
      http_response_code(400);
      die("Please login before modifying $table!");
    }
    global $db;
    if (!$db->tableExists($table) && $table != 'subscribe_meta') {
      http_response_code(400);
      die("Wrong table name: $table!");
    }
    $row = array();
    $posted_pk = $posted_name = $posted_value = $posted_validator = '';
    extract($_POST, EXTR_PREFIX_ALL, 'posted');
    if (empty($posted_pk)) {
      http_response_code(400);
      die("Empty primary key");
    }
    if (empty($posted_name)) {
      http_response_code(400);
      die("Empty name ($posted_name) in data");
    }
    if (!isset($posted_value)) { // Checkbox, unchecked
      $posted_value = 0;
    }
    if (is_array($posted_value)) { // Checkbox (from checklist), checked
      $posted_value = 1;
    }
    if (!empty($posted_validator)) { // a server-side validator is specified
      $fun = "validate_$posted_validator";
      if (method_exists('DbTable', $fun)) {
        $valid = self::$fun($posted_value);
      }
      else {
        http_response_code(400);
        die("Unknown validator ($posted_validator) specified");
      }
      if ($valid !== true) {
        http_response_code(400);
        die("$valid");
      }
    }
    if ($meta) {
      $status = self::updateMetaData($table, $posted_pk, $posted_name, $posted_value);
    }
    else {
      $row['id'] = $posted_pk;
      $row[$posted_name] = $posted_value;
      $status = $db->putRowData($table, $row);
    }
    if (!$status) {
      http_response_code(400);
      die("Database Insert Error in $table!");
    }
    http_response_code(200);
    exit();
  }

  // AJAX CRUD implementation. Delete.
  static function delete($table) {
    if (!EZ::isLoggedIn()) {
      http_response_code(400);
      die("Please login before deleting anything from $table!");
    }
    global $db;
    if (!$db->tableExists($table)) {
      http_response_code(400);
      die("Wrong table name: $table!");
    }
    $posted_pk = '';
    extract($_POST, EXTR_PREFIX_ALL, 'posted');
    if (empty($posted_pk)) {
      http_response_code(400);
      die("Empty primary key to delete!");
    }
    $table = $db->prefix($table);
    $sql = "DELETE FROM $table WHERE `id` = $posted_pk";
    $db->query($sql);
    http_response_code(200);
  }

  static function getId($table, $when) {
    global $db;
    $row = $db->getData($table, 'id', $when);
    return $row[0]['id'];
  }

}

class DbColumn {

  var $dbCol, $heading, $width, $type, $align, $source, $validator;
  var $xClass, $tdClass, $noEdit;

  function __construct($dbCol) {
    $this->dbCol = $dbCol;
    $this->heading = ucwords(str_replace("_", " ", $this->dbCol));
    $this->type = "text";
  }

  function printHeader() {
    if (!empty($this->tdClass)) {
      $tdClass = "class='$this->tdClass'";
    }
    else {
      $tdClass = "";
    }
    if (!empty($this->width)) {
      if (empty($this->minWidth)) {
        $style = "style='width:$this->width'";
      }
      else {
        $style = "style='width:$this->width;min-width:$this->minWidth'";
      }
    }
    echo "<th $tdClass $style>$this->heading</th>";
  }

  function sprintCell($pk, $value, $action) {
    $xedit = 'xedit';
    $dataValue = $dataValidator = $dataSource = $dataAction = "";
    if (!empty($value)) {
      $dataValue = "data-value='$value'";
    }
    if (!empty($action)) {
      $dataAction = "data-action='$action'";
    }
    if (!empty($this->validator)) {
      $dataValidator = "data-validator='$this->validator'";
    }
    $dataType = "data-type='$this->type'";
    switch ($this->type) {
      case 'html':
        $value = stripslashes($value);
        $value = str_replace("'", '"', $value);
        $value = htmlentities($value);
        $dataType = "data-type='textarea'";
        $dataValue = "data-value='$value'";
        break;
      case 'category':
        $dataType = "data-type='select'";
        $dataValue = "data-value='$value'";
        if (!empty($value)) {
          $value = EZ::getCatName($value);
        }
        $dataSource = 'data-source="' . EZ::mkCatSource() . '"';
        break;
      case "select":
        $dataSource = 'data-source="' . EZ::mkSelectSource($this->source) . '"';
        break;
      case "checklist":
        if (empty($value)) {
          $state = 'danger';
          $dataValue = "data-value=''";
        }
        else {
          $state = 'success';
        }
        $xedit = "xedit-checkbox btn-sm btn-$state";
        break;
      default:
    }
    if (empty($this->heading)) {
      $title = ucwords(str_replace("_", " ", $this->dbCol));
    }
    else {
      $title = $this->heading;
    }
    if (empty($this->noEdit)) {
      return "<a href='#' class='$xedit' data-name='$this->dbCol' $dataType data-pk='$pk' data-title='$title' $dataSource $dataValidator $dataAction $dataValue>$value</a>";
    }
    else {
      return "$value";
    }
  }

}
