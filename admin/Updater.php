<?php
if (class_exists("Updater")) {
  echo "Problem, class Updater exists! \nCannot safely continue.\n";
  exit;
}
else {

  class Updater {

    var $slug, $data, $localVersion, $remoteVersion;
    var $name, $price, $toVerify;
    static $updateText;

    const CDN = "http://api.wp-plus.org/";

    function __construct($slug) {
      $this->slug = $slug;
      $this->localVersion = $this->remoteVersion = -1;
      if (!class_exists('ZipArchive')) {
        $msg = "This application cannot update itself because your PHP does not have ZIP support. Please update your application using the following steps.";
        if (EZ::isInWP()) {
          $pluginLink = "<a href='" . admin_url('plugins.php') . "' class='popup'>Plugins Page</a>";
          $uploadLink = "<a href='" . admin_url('plugin-install.php?tab=upload') . "' class='popup'>Upload Page</a>";
          $workAround = "<ol><li>Deactivate and delete your on the $pluginLink. If you do not delete the plugin first, the next step will fail because WordPress will refuse to overwrite the existing plugin folder.</li><li>Install the plugin by uploading the zip file on the $uploadLink.</li><li>Activate the plugin and you are all set.</li></ol>";
        }
        else {
          $realPath = realpath('..');
          $workAround = "<ol><li>Save your <code>dbCfg.php</code> file (in <code>$realPath</code> on your server).</li><li>Unzip the downloaded zip file.</li><li>Use FTP or other means to manually upload the contents of your zip file (to <code>$realPath</code>) overwriting the existing files.</li><li>Restore your <code>dbCfg.php</code> file (in <code>$realPath</code>).</li></ol>";
        }
        $msg .= $workAround;
        echo '<script>$(document).ready(function() {' .
        "showError(\"$msg\");
        });
        </script>";
      }
    }

    function getLocalVersion() {
      if ($this->localVersion < 0) {
        $readme_file = "../readme.txt";
        $readme_text = file_get_contents($readme_file);
        $lines = explode("\n", $readme_text);
        $needle = "Stable tag:";
        foreach ($lines as $line) {
          if (strpos($line, $needle) > -1) {
            $this->localVersion = trim(str_replace($needle, "", $line));
            break;
          }
        }
      }
      return $this->localVersion;
    }

    function getRemoteVersion() {
      if ($this->remoteVersion < 0) {
        if (empty($this->data)) {
          $slug = $this->slug;
          $this->data = unserialize(gzinflate(file_get_contents(self::CDN . "/packages/$slug.dat")));
        }
        if (!empty($this->data)) {
          $this->remoteVersion = $this->data->version;
        }
      }
      return $this->remoteVersion;
    }

    function isOld() {
      $localVersion = (float) $this->getLocalVersion();
      $remoteVersion = (float) $this->getRemoteVersion();
      return $remoteVersion > $localVersion;
    }

    function getUpdateText() {
      $data = $this->data;
      if (empty(self::$updateText)) {
        $localVersion = $this->getLocalVersion();
        $remoteVersion = $this->getRemoteVersion();
        $name = $data->name;
        if ($this->isOld()) {
          $text = "You are using $name {$localVersion}. The current version is {$remoteVersion}. Click here to download the update.";
        }
        else {
          $text = "You are using $name {$localVersion}, which is the latest version available. Please visit this page periodically to check for updates.";
        }
        self::$updateText = $text;
      }
      return self::$updateText;
    }

    function getDiagnostics() {
      $diag = "PHP Version: " . phpversion() . "<br>\n";
      $diag .= $this->getUpdateText();
      global $wpdb;
      if (!empty($wpdb)) {
        $diag .= "<br>\nWPDB Object: ";
        foreach ($wpdb as $k => $v) {
          if (!empty($v) && !is_array($v)) {
            $diag .= "<br>  $k : $v";
          }
        }
      }
      return $diag;
    }

    static function zip($source, $destination) {

      $zip = new ZipArchive();
      if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
      }
      $source = str_replace('\\', '/', realpath($source));
      if (is_dir($source) === true) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file) {
          $file = str_replace('\\', '/', $file);
          // Ignore "." and ".." folders
          if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
            continue;
          }
          $file = realpath($file);
          if (is_dir($file) === true) {
            $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
          }
          else if (is_file($file) === true) {
            $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
          }
        }
      }
      else if (is_file($source) === true) {
        $zip->addFromString(basename($source), file_get_contents($source));
      }
      return $zip->close();
    }

    function backup() { // TODO: Test this method and use it from admin.php
      $zipFile = tempnam(sys_get_temp_dir(), 'zip');
      $toZip = realpath('../..');
      $base = basename($toZip);
      if (!self::zip($toZip, $zipFile)) {
        echo "Failed to write files to zip\n";
      }
      else {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $base . '.zip');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($zipFile));
        @ob_clean(); // Don't let it throw any notices, which will mess up the zip file
        flush();
        readfile($zipFile);
        @unlink($zipFile);
      }
    }

    function render() { // admin page renderer
      require_once 'Ftp.php';
      $ftp = new Ftp();
      ?>
      <style scoped>
        label{width:100px;}
      </style>
      <div>
        <ul class="breadcrumb">
          <li>
            <a href="#">Home</a>
          </li>
          <li>
            <a href="#">Update <?php echo $this->name; ?></a>
          </li>
        </ul>
      </div>
      <?php
      insertAlerts(11);
      openBox("Update or Upgrade Your Product", "plus", 11, "<p>It is easy to update your application. Once you have downloaded an update package, please use the Browse button in the <b>Upload Your Zip File</b> section. When you have selected the zip file to upload, the updater will take care of the rest.</p>"
              . "<p>If you have purchased a <a href='#' class='goPro'>Pro upgrade</a>, the process is identical. Just browse and upload the zip file."
              . "<p>In some installations, you may need to provide FTP details for the updater to work. If needed, you will be prompted for the credentials. Contact your hosting provider or system admin for details.</p>"
              . "<p><a class='showDiag btn-sm btn-primary'>Show Diagnostics</a></p>");
      ?>
      <div class="clearfix">&nbsp;</div>
      <?php
      if (EZ::$isPro || !EZ::isInWP()) {
        $localVersion = $this->getLocalVersion();
        $remoteVersion = $this->getRemoteVersion();
        $toolTip = $this->getUpdateText();
        if ($this->isOld()) {
          ?>
          <div class="col-md-3 col-sm-3 col-xs-6 update">
            <a data-toggle="tooltip" title="<?php echo $toolTip; ?>" class="well top-block update" href="#">
              <i class="glyphicon glyphicon-hand-up red"></i>
              <div><?php echo "$this->name V$localVersion"; ?></div>
              <div>Update to V<?php echo $remoteVersion; ?></div>
              <span class="notification red"><?php echo "V$remoteVersion"; ?></span>
            </a>
          </div>
          <?php
        }
        else {
          ?>
          <div class="col-md-3 col-sm-3 col-xs-6">
            <a data-toggle="tooltip" title="<?php echo $toolTip; ?>" class="well top-block" href="#">
              <i class="glyphicon glyphicon-thumbs-up red"></i>
              <div><?php echo "$this->name V$localVersion"; ?></div>
              <div>Your version is up-to-date</div>
            </a>
          </div>
          <?php
        }
      }
      else {
        $plgUrl = admin_url('plugins.php');
        ?>
        <div class="col-md-3 col-sm-3 col-xs-6">
          <a data-toggle="tooltip" title="Please keep your plugin regularly updated for security, both yours and that of your readers." class="well top-block endpoint" href="<?php echo $plgUrl; ?>" target="_parent">
            <i class="glyphicon glyphicon-hand-up red"></i>
            <div>Check for Updates</div>
            <div>Please visit your plugins page.</div>
          </a>
        </div>
        <?php
      }
      if (EZ::$isPro) {
        ?>
        <div class="col-md-3 col-sm-3 col-xs-6">
          <a data-toggle="tooltip" title="See other premium WordPress plugins and PHP programs by the same author." class="well top-block" href="http://www.thulasidas.com/render" target="_blank">
            <i class="glyphicon glyphicon-star green"></i>
            <div>Other Plugins and Programs</div>
            <div>From the author</div>
          </a>
        </div>
        <?php
      }
      else {
        ?>
        <div class="col-md-3 col-sm-3 col-xs-6 goPro">
          <a data-toggle="tooltip" title="Get the Pro version for only $<?php echo $this->price; ?>. Tons of extra features. Instant download." class="well top-block goPro" href="http://buy.thulasidas.com/<?php echo $this->slug; ?>">
            <i class="glyphicon glyphicon-shopping-cart red"></i>
            <div>Get <?php echo $this->name; ?> Pro</div>
            <div>$<?php echo $this->price; ?>. Instant Download</div>
            <span class="notification red">Pro</span>
          </a>
        </div>
        <?php
      }
      ?>
      <div class="col-md-3 col-sm-3 col-xs-6">
        <a data-toggle="tooltip" title="Check out the author's blog for more information about the author. It has links to his credentials as well." class="well top-block" href="http://www.thulasidas.com/" target="_blank">
          <i class="glyphicon glyphicon-user blue"></i>
          <div>Author Profile</div>
          <div>CV from Author's blog</div>
        </a>
      </div>
      <div class="col-md-3 col-sm-3 col-xs-6">
        <a data-toggle="tooltip" title="If you need support with this application, please visit our support portal and raise a ticket for $0.95. The Pro version (or any purchase from us) comes with free support for a short period." class="well top-block" href="http://support.thulasidas.com/" target="_blank">
          <i class="glyphicon glyphicon-envelope yellow"></i>
          <div>Contact</div>
          <div>Enquiries and Support</div>
        </a>
      </div>
      <div class="clearfix"></div>
      <hr>
      <div id="updateDiv">
        <h4>Upload Your Upgrade/Update</h4>
        <table class="table table-striped table-bordered responsive">
          <tbody>
            <tr>
              <td>Zip File</td>
              <td style='width:70%'><span id='file' class='red' data-name='file' data-type='file'  data-mode='inline' data-validator='notNull'><input id='fileinput' type='file' class='file' multiple data-show-preview='false' data-show-upload='false'> </span></td>
              <td class='center-text'><a style='font-size:1.5em' data-content='Browse to the zip file you have downloaded either for update, or Pro upgrade. Once selected, you will be asked to confirm the update, and the rest will be taken care of.' data-help='' data-toggle='popover' data-placement='left' data-trigger='hover' title='Your Digital Product' ><i class='glyphicon glyphicon-question-sign blue'></i></a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="clearfix"></div>
      <div class="center red" id="loading" style="display:none;font-size:1.3em;width:100%"><i class="fa fa-spinner fa-spin"></i> Working! Please wait...</div>
      <hr>
      <?php
      echo $ftp->printForm();
      closeBox();
      ?>
      <div id="diag" style="display:none">
        <pre>
          <h3>Diagnostics</h3>
          <p><a class='showDiag btn-sm btn-primary'>Hide Diagnostics</a></p><br><?php echo $this->getDiagnostics(); ?>
        </pre>
      </div>
      <script>
        var xeditHandler = 'ajax/options.php';
        $(document).ready(function () {
          var file;
          function ajaxUpload(_file) {
            var data = new FormData();
            data.append('file', _file);
            $.ajax({
              url: 'ajax/update.php',
              type: 'POST',
              dataType: 'json',
              data: data,
              processData: false,
              contentType: false,
              success: function (response) {
                $("#loading").hide();
                showSuccess(response.success);
                flashWarning(response.warning);
                setTimeout(function () {
                  bootbox.confirm("<p>The database needs to be <a href='dbSetup.php?update'>setup again</a> to complete this update/upgrade, in order to create any new tables needed or to alter existing ones. If you are only reinstalling the app, you can skip this step.</p>", function (result) {
                    if (result) {
                      window.location.href = 'dbSetup.php?update';
                    }
                    else {
                      flashWarning("Database not updated. Please run the <a href='dbSetup.php?update'>setup again</a> now or your installation may be in an unpredictable state.");
                    }
                  });
                }, 5000);
              },
              error: function (a) {
                $("#loading").hide();
                $("#setupDiv").fadeIn();
                showError(a.responseText);
              }
            });
          }
          $("#fileinput").on('change', function (event) {
            file = event.target.files[0];
            if (file) {
              bootbox.confirm("<p>Are you sure you want to upload <code>" + file.name + "</code> to update/upgrade your <b><?php echo $this->name; ?></b> installaion? The update process is designed to be safe, but it will replace your existing files and may modify your database tables.</p><p class='red'> <em>Keeping a backup of your files and database is highly recommended.</em></p><p>Before updating, consider backing up:<ul><li><a href='ajax/update.php?backup'>Download a full backup</a> of your current app folder.</li><li><a href='ajax/db-tools.php?action=sqldump&gzip=true'>Download a compressed dump</a> of your database.</li></ul>Note that these backups may take a couple of minites to complete. Please be patient. Once done, be sure to check the downloaded files to verify that they are usable and complete.</p>", function (result) {
                if (result) {
                  $("#updateDiv").hide();
                  $("#loading").fadeIn();
                  ajaxUpload(file);
                }
                else {
                  flashWarning("File not uploaded. Browse again to upload a new file to update or upgrade your <b><?php echo $this->name; ?></b> installation.");
                  $("#loading").hide();
                  $("#updateDiv").fadeIn();
                }
              });
            }
          });
          $('.update').click(function (e) {
            e.preventDefault();
            var url = 'http://buy.thulasidas.com/update.php';
            var title = "Check for Updates";
            var w = 1024;
            var h = 728;
            return ezPopUp(url, title, w, h);
          });
          $('body').on('click', ".showDiag", function (e) {
            e.preventDefault();
            $("#diag").toggle();
          });
        });
      </script>

      <?php
    }

    function handle() { // AJAX handler
      if (!EZ::isLoggedIn()) {
        http_response_code(400);
        die("Please login before uploading files!");
      }

      if (!class_exists('ZipArchive')) {
        $error = "Seems like zip is not fully enabled in the PHP installation on your server. (<code>class ZipArchive</code> not found.) This updater cannot proceed without it.<br />You might be able to add zip support via your cPanel/WHM interface. Look for Module Installers, and try installing zip via PHP Pecl installer.";
        http_response_code(400);
        die($error);
      }
      ob_start();
      if (isset($_REQUEST['backup'])) {
        $this->backup();
        exit();
      }

      if (empty($_FILES)) {
        http_response_code(400);
        die("File upload error. No files reached the server!");
      }

      $ds = DIRECTORY_SEPARATOR;
      $target = realpath("..$ds..") . $ds;

      require_once 'Ftp.php';
      $ftp = new Ftp();
      if (Ftp::isNeeded($target)) {
        if (!$ftp->isReady) {
          $error = "Cannot overwrite the $this->name files! Here are your options to proceed."
                  . "<ol><li>Enter or edit the FTP credentials below, if available. Contact your server admin for details.</li>"
                  . "<li>Unpack the downloaded archive, remove the file <code>dbCfg.php</code>, and upload the rest to your server, overwriting the existing files.</li>"
                  . "<li>Make your installation updatable by using this Unix command or equivalent:<pre><code>chmod -R 777 $target</code></pre></li></ol>";
          http_response_code(400);
          die($error);
        }
      }

      $warning = '';
      $dirCount = 0;
      $zip = new ZipArchive;
      $tmpName = $_FILES['file']['tmp_name'];
      if ($zip->open($tmpName) !== TRUE) {
        $error = "Cannot open the uploaded zip file.";
        http_response_code(400);
        die($error);
      }

// ensure that it is the right archive
      $stat = $zip->statIndex(0);
      $root = $stat['name'];
      $isRight = strpos($root, "$this->slug/") === 0;
      if (!$isRight) {
        http_response_code(400);
        $error = "The uploaded archive does not look like an $this->name update. Root folder is <code>$root</code>.";
        die($error);
      }
      $toVerify = $this->toVerify;
      foreach ($toVerify as $d) {
        $idx = $zip->locateName($d, ZipArchive::FL_NODIR);
        if ($idx === false) {
          $idx = $zip->locateName($d);
        }
        if ($idx === false) {
          $error = "Cannot locate a critical file (<code>$d</code>) in the uploaded zip file.";
          http_response_code(400);
          die($error);
        }
      }
// files to remove from the archive -- not to overwrite on the user's DB server details
      $toDelete = array('dbCfg.php');
      foreach ($toDelete as $d) {
        $idx = $zip->locateName($d, ZipArchive::FL_NODIR);
        if ($idx === false) {
          $idx = $zip->locateName($d);
        }
        if ($idx === false) {
          $error = "Cannot locate a critical file (<code>$d</code>) in the uploaded zip file.";
          http_response_code(400);
          die($error);
        }
        if (!$zip->deleteIndex($idx)) {
          $error = "Cannot delete the empty file (<code>$d</code>) from the archive.";
          http_response_code(400);
          die($error);
        }
      }
      $zip->close();

      if ($zip->open($tmpName) !== TRUE) {
        $error = "Cannot reopen the uploaded zip file (after removing config).";
        http_response_code(400);
        die($error);
      }

      $zipRoot = $zip->getNameIndex(0);
      for ($i = 1; $i < $zip->numFiles; $i++) {
        $filename = $zip->getNameIndex($i);
        $sourceFile = "zip://$tmpName#$filename";
        $targetFile = str_replace($zipRoot, $target, $filename);
        if (is_dir($targetFile)) {
          ++$dirCount;
          continue;
        }
        $lastChar = substr($sourceFile, -1);
        if ($lastChar == $ds || $lastChar == '/') {
          if (!$ftp->mkdir($targetFile)) {
            $error = "Error creating the directory $targetFile";
            http_response_code(400);
            die($error);
          }
          continue;
        }
        $targetDir = dirname($targetFile);
        if (!is_dir($targetDir) && !@$ftp->mkdir($targetDir)) {
          $error = "Error creating the new folder $targetDir";
          http_response_code(400);
          die($error);
        }
        if (!$ftp->copy($sourceFile, $targetFile)) {
          $error = "Error copying $filename to $targetFile";
          http_response_code(400);
          die($error);
        }
      }
      if ($dirCount > 0) {
        $warning = "Ignoring $dirCount folders, which already exist on your server.<br />";
      }
      $zip->close();
      $success = "Congratulations, you have successfully updated $this->name.";

      ob_end_clean();
      http_response_code(200);
      header('Content-Type: application/json');
      echo json_encode(array('success' => $success, 'warning' => $warning));
      exit();
    }

  }

}