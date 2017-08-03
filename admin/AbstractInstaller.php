<?php
if (!session_id()) {
  session_start();
}
if (class_exists("AbstractInstaller")) {
  echo "Problem: class AbstractInstaller exists! \nCannot safely continue.\n";
  exit;
}
else {
  require_once('../DbHelper.php');

  abstract class AbstractInstaller {

    var $tables = array();
    var $name, $logo, $help;
    var $db, $needMigration = false, $message = "";
    var $hideSetup = "", $hideAdmin = "hidden", $hideIndex = "";
    var $helpBtn = "<a class='setup-help' style='font-size:1.5em;float:right' href='#' title='Click for help' data-toggle='tooltip' data-content=''><i class='glyphicon glyphicon-question-sign blue'></i></a>";
    static $isUpdating, $isInstallingWP;

    function __construct() {
      $GLOBALS['no_visible_elements'] = true;
      self::$isUpdating = isset($_REQUEST['update']);
      self::$isInstallingWP = !empty($GLOBALS['isInstallingWP']);
      if (self::$isInstallingWP || self::$isUpdating) {
        $this->install();
        if (self::$isUpdating) {
          header("Location: index.php");
          exit();
        }
        return;
      }
    }

    function backupTables() {
      $db = $this->db;
      if (self::$isUpdating) {
        $this->needMigration = false;
      }
      else {
        $this->needMigration = $db->saveTables($this->tables);
      }
    }

    static function randString($len = 32) { // Duplicated in EZCom
      $chars = 'abcdefghijklmnopqrstuvwxyz';
      $chars .= strtoupper($chars) . '0123456789';
      $charLen = strlen($chars) - 1;
      $string = '';
      for ($i = 0; $i < $len; $i++) {
        $pos = rand(0, $charLen);
        $string .= $chars[$pos];
      }
      return $string;
    }

    function putDefaultOptions($options) { // Duplicated in EZCom
      $db = $this->db;
      $row = array();
      foreach ($options as $k => $o) {
        if ($k == 'salt') {
          $row[$k] = self::randString();
        }
        else {
          $row[$k] = $o['value'];
        }
      }
      $rowDB = $db->getMetaData('options_meta');
      $row = array_merge($row, $rowDB);
      $db->putMetaData('options_meta', $row);
    }

    function mkDB() {
      if (!($this->db instanceof DbHelper)) {
        $this->db = new DbHelper();
      }
      return $this->db;
    }

    function install() {
      $db = $this->mkDB();
      $this->backupTables();
      $ret = $db->importSQL('setup.sql');
      $ret2 = $db->importSQL('setup-pro.sql');
      if ($this->needMigration) {
        $dbBak = new DbHelper();
        $dbBak->dbPrefix = "bak_" . $db->dbPrefix;
        $this->migrate($dbBak);
      }
      $options = array();
      require_once('options-default.php');
      $options['app_name'] = array('value' => $this->name);
      $options['app_logo'] = array('value' => $this->logo);
      $this->putDefaultOptions($options);
      if (file_exists("options-advanced.php")) {
        include_once('options-advanced.php');
        $this->putDefaultOptions($options);
      }
      $this->message = "<div class='alert alert-info'>$this->helpBtn Congratulations! You have configured your DB details.<br /> Please setup an admin account now.</div>"
              . "<script>$(document).ready(function(){setTimeout(function(){ window.location = 'adminSetup.php'; }, 2000);});</script>";
      // post install setup
      $this->setup();
      if (!empty($ret2)) {
        $ret = array_merge_recursive($ret, $ret2);
      }
      return $ret;
    }

    function import($dbBak) {
      $db = $this->db;
      $tables = $dbBak->getTableNames(true);
      foreach ($tables as $table) {
        $tableStub = str_replace($dbBak->dbPrefix, '', $table);
        if (in_array($tableStub, $this->tables) && $db->tableExists($tableStub)) {
          $data = $dbBak->getData($table);
          foreach ($data as $row) {
            $db->putRowData($tableStub, $row);
          }
        }
      }
    }

    function verifyCfg() {
      if (self::$isInstallingWP) {
        return true;
      }
      $cfgIsValid = DbHelper::cfgIsValid();
      $cfgDir = dirname(__DIR__);
      $this->message = "<div class='alert alert-info'>$this->helpBtn Please enter your database details.</div>";
      if (!empty($_GET['error'])) {
        switch ($_GET['error']) {
          case 1:
            $this->message = "<div class='alert alert-danger'>$this->helpBtn Error connecting to the database. Check your DB details below.</div>";
            break;
          case 2:
            if (!empty($_GET['cfg'])) {
              $cfg = "<p>Or create the config file on your server and insert the following content in it.</p><p><pre class='alert-danger'>" . htmlspecialchars(urldecode($_GET['cfg'])) . "</pre>Config file is <code>$cfgDir/dbCfg.php</code></p>";
            }
            else {
              $cfg = '';
            }
            $this->message = "<div class='qalert alert-danger' style='text-align:left;padding:10px;margin:10px;margin-bottom:20px;'>$this->helpBtn<p><strong>Error:</strong>  Permission denied! Unable to open config file (<code>dbCfg.php</code>) for writing.</p><p>Try creating the file on your server and making it writable. On Unix, the commands are </p><p>&nbsp<code>cd $cfgDir </code><br />&nbsp<code>touch dbCfg.php</code> <br/>&nbsp;<code>chmod 777 dbCfg.php</code></p>$cfg</div>";
            break;
          case 3:
            $this->message = "<div class='alert alert-info' style='text-align:left'>$this->helpBtn Config file (<code>$cfgDir/dbCfg.php</code>) successfully written. <br />For your security, please write-protect it using commands equivalent to<br />&nbsp<code>cd $cfgDir </code><br />&nbsp<code>chmod 644 dbCfg.php</code><br />Please proceed to the <a href='index.php'>Admin Interface</a> to set up your products.</div>";
            break;
          case 4:
            $cfgIsValid = true;
            break;
          default:
        }
      }
      if ($cfgIsValid) { // valid config. don't display for security reasons
        $this->hideSetup = "hidden";
        $this->hideAdmin = "";
        // Wait for the DB to accept config (Needed on Arvixe, CentOS)
        while (!DbHelper::cfgIsValid()) {
          sleep(5);
        }
        return true;
      }
      else {
        $this->hideSetup = "";
        $this->hideAdmin = "hidden";
      }
      return false;
    }

    function printCfgform() {
      if (self::$isInstallingWP) {
        return true;
      }
      $dbHost = $dbName = $dbUsr = $dbPwd = $dbEmail = $dbPrefix = "";
      if (!empty($_SESSION['posted'])) {
        $posted = $_SESSION['posted'];
        extract($posted);
      }
      require_once('header.php');
      ?>

      <div class="row">
        <div class="col-md5 center">
          <h3 class="col-md5">Database Setup</h3><br /><br />
        </div>
      </div>

      <div class="row">
        <div class="well col-md-5 center setup-box">
          <?php echo $this->message; ?>
          <div id='counter' style='display:none' class='alert alert-warning'>Thank you! Verifying... This may take a while. <span class='counter' style='font-weight:bold'>0</span> seconds.</div>
          <?php
          if (empty($this->hideSetup)) {
            ?>
            <form class="form-horizontal <?php echo $this->hideSetup; ?>" action="index.php" method="post">
              <fieldset>
                <div class="input-group input-group-lg">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-cloud-upload blue"></i></span>
                  <input name="dbHost" type="text" class="form-control" placeholder="Database Host" value="<?php echo $dbHost; ?>">
                </div>
                <div class="clearfix"></div><br>

                <div class="input-group input-group-lg">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-hdd blue"></i></span>
                  <input name="dbName" class="form-control" placeholder="Database Name" value="<?php echo $dbName; ?>">
                </div>
                <div class="clearfix"></div><br>

                <div class="input-group input-group-lg">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-list blue"></i></span>
                  <input name="dbPrefix" class="form-control" placeholder="Database Prefix" value="<?php echo $dbPrefix; ?>">
                </div>
                <div class="clearfix"></div><br>

                <div class="input-group input-group-lg">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-log-in blue"></i></span>
                  <input name="dbUsr" class="form-control" placeholder="Database User Name" value="<?php echo $dbUsr; ?>">
                </div>
                <div class="clearfix"></div><br>

                <div class="input-group input-group-lg">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-lock blue"></i></span>
                  <input name="dbPwd" class="form-control" placeholder="Database Pasword" value="<?php echo $dbPwd; ?>">
                </div>
                <div class="clearfix"></div><br>

                <div class="input-group input-group-lg">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-envelope blue"></i></span>
                  <input name="dbEmail" class="form-control" placeholder="Your Email" value="<?php echo $dbEmail; ?>">
                </div>
                <div class="clearfix"></div><br>

                <p class="center col-md-5">
                  <button id="dbSetup" type="submit" name="dbSetup" class="btn btn-primary">Install</button>
                </p>
              </fieldset>
            </form>
            <?php
          }
          ?>
          <p class="center col-md-5">
            <a class="btn btn-primary <?php echo $this->hideAdmin; ?>" href='adminSetup.php'>Admin Setup</a>
          </p>
        </div>
        <!--/span-->
      </div><!--/row-->

      <div id="helpText" class="hidden">
        <h4>Your application hasn't been installed. Please provide the database details to set it up.</h4>
        <?php echo $this->help; ?>
        <p>
          <i class="glyphicon glyphicon-cloud-upload blue"></i> <b>Database Host</b>: If your database is hosted on a different server, please provide its name. Usually, MySQL databases are hosted at the same server as your Webserver, in which case, you can use <code>localhost</code> as your database server.
        </p>
        <p>
          <i class="glyphicon glyphicon-hdd blue"></i> <b>Database Name</b>: If you are using a dedicated database created using your CPanel or other hosting provider interface, please provide its name. It usually has the form <code>username_dbname</code>. If you have limits on the number of databases you can create on your server, you can reuse an existing database. If not, it is best to create a dedicated one for this application.
        </p>
        <p>
          <i class="glyphicon glyphicon-list blue"></i> <b>Database Prefix</b>: <i>Optional</i>: Use a prefix for all the database tables so that you can easily identify them. A prefix like <code>ez_</code> is a decent one, but to enhance your security, you may want to choose a different one.
        </p>
        <p>
          <i class="glyphicon glyphicon-log-in blue"></i> <b>Database User Name</b>: Your username to log on to the database server. If you created your database and db users on a cPanel, you'd know the user name. It is typically the same as the database name itself. Please contact your system admin if in doubt.
        </p>
        <p>
          <i class="glyphicon glyphicon-lock blue"></i> <b>Database Password</b>: Your database password. You can set it on your cPanel or equivalent. Please contact your system admin if in doubt.
        </p>
        <p>
          <i class="glyphicon glyphicon-envelope blue"></i> <b>Your Email</b>: <i>Optional</i>. This email ID will be used to send database error messages from this application. Later on, you will set up other email addresses where application or support messages may be directed. However, if the DB cannot be connected to, those email IDs cannot be accessed, and diagnostic messages cannot be sent. So, this is the only email address the program will have access to. If you don't want emails about DB errors, leave it empty or give a fake email ID like <code>nobody@nowhere.com</code>.
        </p>
        <p>Once all the required values are given, this application will try to generate a DB configuration file for you. If it fails to do so because of file permission errors, it will ask you to correct the issues with clear instructions.</p>
      </div>

      <?php
      $this->printCfgJS();
      require_once('footer.php');
    }

    function printCfgJS() {
      ?>
      <script>
        $(document).ready(function () {
          $('.setup-help').click(function (e) {
            e.preventDefault();
            bootbox.alert($("#helpText").html());
          });
          $('#dbSetup').click(function () {
            var current = 0;
            $('#counter').show();
            setInterval(function () {
              ++current;
              $('.counter').text(current);
            }, 1000);
          });
        });
      </script>
      <?php
    }

    function getAdminError($updating) {
      if (!$updating) {
        if ($this->isAdminSetup()) {
          return 7;
        }
      }
      if ($_SERVER['REQUEST_METHOD'] != "POST" || !isset($_POST['login'])) {
        return -1;
      }
      if (empty($_POST['newpassword0']) && empty($_POST['email'])) {
        return 3;
      }
      if (empty($_POST['newpassword0'])) {
        return 4;
      }
      if (empty($_POST['email'])) {
        return 5;
      }
      if ($_POST['newpassword0'] != $_POST['newpassword1']) {
        return 6;
      }
      if ($updating) { // verifies the current password field
        require_once '../EZ.php';
        $row = EZ::authenticate();
        if (!is_array($row)) {
          return $row;
        }
      }
      return 0;
    }

    function isAdminSetup() {
      if (self::$isInstallingWP) {
        return true;
      }
      $db = $this->mkDB();
      $table = 'administrator';
      if ($db->tableExists($table)) {
        $row = $db->getData($table);
        if (!empty($row)) { // already set up.
          return true;
        }
      }
      else {
        header('location: dbSetup.php');
        exit;
      }
      return false;
    }

    function verifyAdmin($current = array()) {
      if (self::$isInstallingWP) {
        return true;
      }
      $init = empty($current);
      if ($init) {
        // clear previous logins
        session_unset();
        session_destroy();
        session_write_close();
        setcookie(session_name(), '', 0, '/');
        session_regenerate_id(true);
      }
      $updating = !$init;
      $error = $this->getAdminError($updating);
      if ($error == 0) {
        $data = array();
        $data['id'] = 1;
        $data['username'] = $_POST['newusername'];
        if (!empty($_POST['newpassword0'])) {
          $data['password'] = EZ::md5($_POST['newpassword0']);
        }
        if (!empty($_POST['email'])) {
          $data['email'] = $_POST['email'];
        }
        $db = $this->mkDB();
        $db->putRowData('administrator', $data);
      }
      if ($updating) {
        $error += 10;
      }
      $this->hideAdmin = "";
      $this->hideIndex = "hidden";
      switch ($error) {
        case 0:
          $this->message = "<div class='alert alert-info'>$this->helpBtn User authenticated and Profile created.</div>";
          break;
        case 1:
          $this->message = "<div class='alert alert-danger'>$this->helpBtn Your username and password are incorrect!</div>";
          break;
        case 3:
          $this->message = "<div class='alert alert-danger'>$this->helpBtn Nothing to update! New password and new email are empty.</div>";
          break;
        case 4:
          $this->message = "<div class='alert alert-warning'>$this->helpBtn Password not updated because it is empty. Email is updated.</div>";
          break;
        case 5:
          $this->message = "<div class='alert alert-warning'>$this->helpBtn Email not updated because it is empty. Password is updated.</div>";
          break;
        case 6:
          $this->message = "<div class='alert alert-danger'>$this->helpBtn New passwords do not match.</div>";
          break;
        case 7:
          $this->message = "<div class='alert alert-info'>$this->helpBtn Congratulations! You have fully configured your application.<br /> Please go to the admin interface.</div>"
                  . "<script>$(document).ready(function(){setTimeout(function(){ window.location = 'index.php'; }, 2000);});</script>";
          $this->hideAdmin = "hidden";
          $this->hideIndex = "";
          return true;
        case 10:
          $this->message = '<div class="alert alert-info">User authenticated and Profile updated.</div>';
          break;
        case 11:
          $this->message = '<div class="alert alert-danger">Your current password is incorrect!</div>';
          break;
        case 13:
          $this->message = '<div class="alert alert-danger">Nothing to update! New password and new email are empty.</div>';
          break;
        case 14:
          $this->message = '<div class="alert alert-warning">Password not updated because it is empty. Email is updated.</div>';
          break;
        case 15:
          $this->message = '<div class="alert alert-warning">Email not updated because it is empty. Password is updated.</div>';
          break;
        case 16:
          $this->message = '<div class="alert alert-danger">New passwords do not match.</div>';
          break;
        default:
          if ($init) {
            $this->message = "<div class='alert alert-info'>$this->helpBtn Please create an admin account.</div>";
          }
          else {
            $this->message = '<div class="alert alert-info">For your security, verify your current password<br/> again before updating your profile.</div>';
          }
          break;
      }
      return false;
    }

    function printAdminForm($current = array()) {
      if (self::$isInstallingWP) {
        return;
      }
      $updating = !empty($current);
      if (!$updating) {
        if ($this->isAdminSetup()) {
          $this->hideAdmin = "hidden";
          $this->hideIndex = "";
        }
        $username = $email = "";
        $btnText = "Create Admin";
      }
      else {
        $username = $current['username'];
        $email = $current['email'];
        $btnText = "Update";
        $this->hideAdmin = "";
        $this->hideIndex = "hidden";
      }

      require_once('header.php');
      ?>
      <div class="row">
        <div class="col-md5 center">
          <h3 class="col-md5">Admin Account Setup</h3><br /><br />
        </div>
        <!--/span-->
      </div><!--/row-->
      <div class="well col-md-5 center login-box">
        <?php echo $this->message; ?>
        <form class="form-horizontal <?php echo $this->hideAdmin; ?>" method="post" id="adminForm">
          <fieldset>
            <?php
            if ($updating) {
              ?>
              <div class="control-group">
                <div class="input-group input-group-lg">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-lock red"></i></span>
                  <input name="myusername" type="hidden" value="<?php echo $current['username']; ?>">
                  <input name="mypassword" type="password" class="form-control" placeholder="Current Password">
                </div>
              </div>
              <div class="clearfix"></div><br>
              <?php
            }
            ?>
            <div class="control-group">
              <div class="input-group input-group-lg">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input name="newusername" type="text" class="form-control" placeholder="Username" value='<?php echo $username; ?>'>
              </div>
            </div>
            <div class="clearfix"></div><br>

            <div class="control-group">
              <div class="input-group input-group-lg">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input name="newpassword0" type="password" class="form-control" placeholder="Password">
              </div>
            </div>
            <div class="clearfix"></div><br>

            <div class="control-group">
              <div class="input-group input-group-lg">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input name="newpassword1" type="password" class="form-control" placeholder="Password Again">
              </div>
            </div>
            <div class="clearfix"></div><br>

            <div class="control-group">
              <div class="input-group input-group-lg">
                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                <input name="email" type="text" class="form-control" placeholder="Email" value='<?php echo $email; ?>'>
              </div>
            </div>
            <div class="clearfix"></div>

            <p class="center col-md-5">
              <button type="submit" name="login" class="btn btn-primary"><?php echo $btnText; ?></button>
            </p>
          </fieldset>
        </form>
        <p class="center col-md-5">
          <a class="btn btn-primary <?php echo $this->hideIndex; ?>" href='index.php'>Admin Interface</a>
        </p>
      </div>

      <div id="helpText" class='hidden'>
        <h4>Your application has been installed, but needs an admin account for secure access.</h4><br />
        <p>
          <i class="glyphicon glyphicon-user blue"></i> <b>Username</b>: Select an admin user name. A name like <code>admin</code> is fine, but something less obvious would be more secure.
        </p>
        <p>
          <i class="glyphicon glyphicon-lock blue"></i> <b>Password</b>: Please type in a strong password (at least six characters long), and verify it.
        </p>
        <p>
          <i class="glyphicon glyphicon-envelope blue"></i> <b>Email</b>: <i>Optional</i>: Please provide an email address where you can receive password retrieval information, in case you forget your password.
        </p>
        <p>Once the admin account is set up, you are ready to use the application. This page will not be operational after you set up your admin account, which is a precaution against possible hacker attacks. To further improve your security, you may want to delete this file (<code><?php echo __FILE__; ?></code>) from your server.</p>
        <p>Note that this application allows only one admin account, because one is all that is needed. If you would like to modify the admin profile (password and email), you can do so from the admin interface.</p>
      </div>
      <?php
      $this->printAdminJS($updating);
      require_once 'footer.php';
    }

    function printAdminJS($updating) {
      ?>
      <script>
        $(document).ready(function () {
        $('.setup-help').click(function (e) {
        e.preventDefault();
        bootbox.alert($("#helpText").html());
      });
      $('#adminForm').bootstrapValidator({
        message: 'This value is not valid',
        group: '.control-group',
        fields: {
          myusername: {
            message: 'Username is not valid',
            validators: {
              notEmpty: {
                message: 'Username is required'
              },
              stringLength: {
                min: 5,
                max: 15,
                message: 'Username must be at least 5 and no more than 15 characters long'
              },
              regexp: {
                regexp: /^[a-zA-Z0-9_\.]+$/,
                message: 'Username can only consist of alphabetical, number, dot and underscore'
              }
            }
          },
          email: {
            validators: {
              notEmpty: {
                message: 'Email address is required'
              },
              emailAddress: {
                message: 'Not a valid email address'
              }
            }
          },
          newpassword0: {
            validators: {
<?php
if (!$updating) {
?>
            notEmpty: {
              message: 'Password is required'
            },
<?php
}
?>
            stringLength: {
              min: 6,
              max: 15,
              message: 'Password must be at least 6 characters long'
            },
            different: {
              field: 'myusername',
              message: 'Password should not be the same as username'
            }
          }
          },
          newpassword1: {
            validators: {
<?php
if (!$updating) {
?>
            notEmpty: {
              message: 'Password confirmation is required'
            },
<?php
}
?>
            identical: {
              field: 'newpassword0',
              message: 'Password mismatch'
            }
          }
        }
      }
    });
  });
</script>

      <?php
    }

    abstract function configure();

    abstract function migrate($dbBak);

    abstract function setup();
  }

}