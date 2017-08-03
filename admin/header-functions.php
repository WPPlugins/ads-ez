<?php
if (function_exists('plugins_url')) {
  $GLOBALS['ezAdminUrl'] = $ezAdminUrl = plugins_url("/admin/", __DIR__);
}
else {
  $GLOBALS['ezAdminUrl'] = $ezAdminUrl = "";
}

function menuShown() {
  global $no_visible_elements;
  return empty($no_visible_elements) && empty($GLOBALS['no_visible_elements']);
}

function renderMenu($menu) {
  $menu0 = array();
  $menu0[] = array(
      'url' => 'index.php',
      'text' => 'Dashboard',
      'icon' => 'home',
  );
  if (!EZ::$isPro) {
    $menu0[] = array(
        'url' => '#',
        'id' => 'goPro',
        'text' => 'Go Pro!',
        'class' => 'red goPro',
        'aClass' => 'red goPro',
        'icon' => 'shopping-cart',
        'toggle' => 'popover',
        'trigger' => 'hover',
        'content' => "Get the Pro version of this app! Tons of extra features. Instant download.",
        'placement' => "right",
        'title' => "Upgrade to Pro"
    );
  }
  $menu = array_merge($menu0, $menu);
  $subMenu = array();
  $subMenu[] = array('text' => 'Configuration');
  $subMenu[] = array(
      'url' => 'options.php',
      'text' => 'Options',
      'icon' => 'cog'
  );
  $subMenu[] = array(
      'url' => 'advanced.php',
      'text' => 'Advanced Options',
      'class' => 'red',
      'icon' => 'cog'
  );
  $subMenu[] = array(
      'url' => 'reinstall.php',
      'text' => 'Rerun DB Setup',
      'icon' => 'repeat'
  );
  $menu[] = $subMenu;
  if (!EZ::isInWP()) {
    $subMenu = array();
    $subMenu[] = array('text' => 'Your Account');
    $subMenu[] = array(
        'url' => 'profile.php',
        'text' => 'Your Profile',
        'icon' => 'lock'
    );
    $subMenu[] = array(
        'url' => 'login.php?logout',
        'text' => 'Logout',
        'icon' => 'ban-circle'
    );
    $menu[] = $subMenu;
  }
  ob_start();
  ?>
  <!-- menu starts -->
  <div class="col-sm-2 col-lg-2">
    <div class="sidebar-nav">
      <div class="nav-canvas">
        <div class="nav-sm nav nav-stacked">

        </div>
        <ul class="nav nav-pills nav-stacked main-menu">
          <?php
          foreach ($menu as $item) {
            renderMenuItem($item);
          }
          ?>
        </ul>
      </div>
    </div>
  </div>
  <!-- menu ends -->

  <noscript>
  <div class="alert alert-block col-md-12">
    <h4 class="alert-heading">Warning!</h4>
    <p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a> enabled to use this site.</p>
  </div>
  </noscript>
  <?php
  $menuRendered = ob_get_clean();
  if (method_exists('EZ', 'toggleMenu')) {
    $menuRendered = EZ::toggleMenu($menuRendered);
  }
  echo $menuRendered;
}

function renderMenuItem($item) {
  if (class_exists('EZ') && !empty(EZ::$options['dynamic_menu'])) {
    $dynamic = 'dynamic-menu';
  }
  else {
    $dynamic = '';
  }
  if (is_array($item) && array_values($item) == $item) {
    ?>
    <li class="accordion <?php echo $dynamic; ?>">
      <?php
      $firstItem = array_shift($item);
      $icon = "class='glyphicon glyphicon-plus'";
      $text = $firstItem['text'];
      echo "<a href='#'><i $icon></i><span> $text</span></a>";
      ?>
      <ul class="nav nav-pills nav-stacked">
        <?php
        foreach ($item as $subMenu) {
          renderMenuItem($subMenu);
        }
        ?>
      </ul>
    </li>
    <?php
  }
  else {
    $aClass = $faIcon = $spin = $title = $class = $id = $icon = $text = $url = "";
    $toggle = $trigger = $content = $placement = "";
    extract($item);
    if (empty($id)) {
      $id = str_replace(".php", "", $url);
    }
    if (empty($icon)) {
      $icon = 'plus';
    }
    if (!empty($title)) {
      $title = "title='$title'";
    }
    if (!empty($aClass)) {
      $aClass = "class='$aClass'";
    }
    if (empty($spin) && empty($faIcon)) {
      $icon = "class='glyphicon glyphicon-$icon $class'";
    }
    else if (!empty($faIcon)) {
      $icon = "class='fa fa-$icon $class'";
    }
    else {
      $icon = "class='fa fa-spin fa-$icon $class'";
    }
    foreach (array('toggle', 'trigger', 'content', 'placement') as $data) {
      if (!empty($$data)) {
        $$data = "data-$data='{$$data}'";
      }
      else {
        $$data = "";
      }
    }
    $attr = "$toggle $trigger $content $placement $title $aClass";
    $attr = trim(str_replace("  ", " ", $attr));
    echo <<<EOLI
<li id="$id"><a href="$url" $attr><i $icon></i><span> $text</span></a></li>\n
EOLI;
  }
}

function insertAlerts($width = 10) {
  ?>
  <div style="display:none" class="alert alert-info col-lg-<?php echo $width; ?>" role="alert">
    <button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <span id="alertInfoText"></span>
  </div>
  <div style="display:none" class="alert alert-success col-lg-<?php echo $width; ?>" role="alert">
    <button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <span id="alertSuccessText"></span>
  </div>
  <div style="display:none" class="alert alert-warning col-lg-<?php echo $width; ?>" role="alert">
    <button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <span id="alertWarningText"></span>
  </div>
  <div style="display:none" class="alert alert-danger col-lg-<?php echo $width; ?>" role="alert">
    <button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <span id="alertErrorText"></span>
  </div>
  <?php
}

function openRow($help = "") {
  if (empty($help)) {
    $help = "You can roll-up or temporarily suppress this box. For more help, click on the friendly Help button near the top right corner of this page, if there is one.";
  }
  else {
    ?>
    <a href="#" class="btn btn-primary btn-help" style="float:right" data-content="<?php echo $help; ?>"><i class="glyphicon glyphicon-question-sign large"></i> Help</a>
  <?php }
  ?>
  <div class="row">
    <?php
    return $help;
  }

  function closeRow() {
    ?>
  </div><!-- row -->
  <?php
}

function openCell($title, $icon = "edit", $size = "12", $help = "") {
  if (empty($help)) {
    $help = "You can roll-up or temporarily suppress this box. For more help, click on the friendly Help button near the top right corner of this page, if there is one.";
  }
  ?>
  <div class="box col-md-<?php echo $size; ?>">
    <div class="box-inner">
      <div class="box-header well" data-original-title="">
        <h2>
          <i class="glyphicon glyphicon-<?php echo $icon; ?>"></i>
          <?php echo $title; ?>
        </h2>
        <div class="box-icon">
          <a href="#" class="btn btn-help btn-round btn-default"
             data-content="<?php echo $help; ?>">
            <i class="glyphicon glyphicon-question-sign"></i>
          </a>
          <a href="#" class="btn btn-minimize btn-round btn-default">
            <i class="glyphicon glyphicon-chevron-up"></i>
          </a>
          <a href="#" class="btn btn-close btn-round btn-default">
            <i class="glyphicon glyphicon-remove"></i>
          </a>
        </div>
      </div>
      <div class="box-content">
        <?php
      }

      function closeCell() {
        ?>
      </div>
    </div>
  </div><!-- box -->
  <?php
}

function openBox($title, $icon = "edit", $size = "12", $help = "") {
  $help = openRow($help);
  openCell($title, $icon, $size, $help);
}

function closeBox() {
  closeCell();
  closeRow();
}

function showScreenshot($id) {
  $img = "../screenshot-$id.png";
  $iSize = getimagesize($img);
  $width = $iSize[0] . 'px';
  echo "<img src='$img' alt='screenshot' class='col-sm-12' style='max-width:$width'>";
}

function renderHtmlHead($app) {
  if (class_exists('EZ') && EZ::$isPro && !empty(EZ::$options['theme'])) {
    $themeCSS = "css/bootstrap-" . strtolower(EZ::$options['theme']) . ".min.css";
  }
  else {
    $themeCSS = "css/bootstrap-default.min.css";
  }
  global $ezAdminUrl;
  ?>
  <head>
    <meta charset="utf-8">
    <title><?php echo $app->name; ?> - <?php echo $app->slogan; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo $app->name; ?> - <?php echo $app->slogan; ?>">
    <meta name="author" content="Manoj Thulasidas">

    <!-- The styles -->
    <link id="bs-css" href="<?php echo $ezAdminUrl . $themeCSS; ?>" rel="stylesheet">
    <link href="<?php echo $ezAdminUrl; ?>css/bootstrap-editable.css" rel="stylesheet">
    <link href="<?php echo $ezAdminUrl; ?>css/charisma-app.css" rel="stylesheet">
    <link href='<?php echo $ezAdminUrl; ?>css/bootstrap-tour.min.css' rel='stylesheet'>
    <link href='<?php echo $ezAdminUrl; ?>css/bootstrapValidator.css' rel='stylesheet'>
    <link href="<?php echo $ezAdminUrl; ?>css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo $ezAdminUrl; ?>css/fileinput.min.css" rel="stylesheet">
    <?php
    if (!empty($app->css)) {
      foreach ($app->css as $css) {
        echo "    <link href='{$ezAdminUrl}css/$css.css' rel='stylesheet'>\n";
      }
    }
    ?>
    <style type="text/css">
      .popover{width:600px;}
      <?php
      if (class_exists('EZ') && empty(EZ::$options['breadcrumbs'])) {
        ?>
        .breadcrumb {display:none;}
        <?php
      }
      ?>
    </style>
    <!-- jQuery -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- The fav icon -->
    <link rel="shortcut icon" href="img/favicon.ico">

  </head>
  <?php
}

function renderLogo($app) {
  global $ezAdminUrl;
  ?>
  <div class="row">
    <div class="col-md5 center">
      <h2 class="col-md5"><img alt="<?php echo $app->name; ?>" src="<?php echo $ezAdminUrl . 'img/' . $app->mainLogo; ?>.png" style="max-width: 250px;"/><br /><br />
        Welcome to <?php echo $app->name; ?><br><small><?php echo $app->slogan; ?></small></h2><br /><br />
    </div>
  </div>
  <?php
}

function renderHeader($app, $button = "") {
  global $ezAdminUrl;
  http_response_code(200);
  ?>
  <!DOCTYPE html>
  <html lang="en">
    <?php
    renderHtmlHead($app);
    ?>
    <body>
      <?php
      if (class_exists('EZ') && EZ::isInWP()) {
        echo " <script>var isInWP = true;</script>";
      }
      else {
        echo " <script>var isInWP = false;</script>";
      }
      ?>
      <script>
        parent.clearTimeout(parent.errorTimeout);
      </script>
      <?php if (menuShown()) { ?>
        <!-- topbar starts -->
        <div class="navbar navbar-default" role="navigation">

          <div class="navbar-inner">
            <a id="home" class="navbar-brand" href="index.php"> <img alt="<?php echo $app->name; ?>" src="<?php echo $ezAdminUrl . 'img/' . $app->adminLogo; ?>.png" class="hidden-xs"/>
              <span><?php echo $app->slogan; ?></span></a>
            <div class="btn-group pull-right">
              <?php
              echo $button;
              if (!EZ::isInWP()) {
                ?>
                <!-- user dropdown starts -->
                <button id="account" class="btn btn-default dropdown-toggle pull-right" data-toggle="dropdown">
                  <i class="glyphicon glyphicon-user"></i><span class="hidden-sm hidden-xs"> admin</span>
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <li><a href="profile.php">Profile</a></li>
                  <li class="divider"></li>
                  <li><a href="login.php?logout">Logout</a></li>
                </ul>
                <!-- user dropdown ends -->
                <?php
              }
              else {
                $standaloneURL = $app->endPoint . "/admin/index.php";
                ?>
                <a id="standAloneMode" href="<?php echo $standaloneURL; ?>" target="_blank" data-content="Open <?php echo $app->name; ?> Admin in a new window independent of WordPress admin interface. The standalone mode still uses WP authentication, and cannot be accessed unless logged in." data-toggle="popover" data-trigger="hover" data-placement="left"  title='Standalone Admin Screen'><span class="btn btn-info"><i class="glyphicon glyphicon-resize-full"></i> Standalone Mode</span></a>
                <?php
              }
              ?>
              <a id="update" href="update.php" data-content="If you would like to check for regular updates, or install a purchased module or Pro upgrade, visit the update page." data-toggle="popover" data-trigger="hover" data-placement="left" title='Update Page'><span class="btn btn-info"><i class="fa fa-cog fa-spin"></i> Updates
                  <?php
                  if (!EZ::$isPro) {
                    ?>
                    &nbsp;<span class="badge red">Pro</span>
                    <?php
                  }
                  ?>
                </span>
              </a>&nbsp;
              <?php
              if (!empty(EZ::$options['show_google_translate'])) {
                ?>
                <span id='GoogleTranslatorWidget' style='display:inline-block'>
                  <span id='google_translate_element'></span>
                </span>
                <?php
              }
              ?>
            </div>
          </div>
        </div>
        <!-- topbar ends -->
        <?php
      }
      if (version_compare(PHP_VERSION, "5.4.0", "<")) {
        $warning = EZ::$name . " requires PHP V5.4 or higher for some of its advanced features."
                . " You are using PHP Version " . PHP_VERSION
                . ". Please get your PHP upgraded on your server.";
        EZ::flashWarning($warning, true);
      }
    }

    function renderFooter($app, $button = "") {
      $ezAdminUrl = $GLOBALS['ezAdminUrl'];
      $showPPP = file_exists('img/paypal-partner.png');
      ?>
      <!-- content ends -->
    </div>
  </div><!-- .row-->
  <hr>

  <?php
  if (menuShown()) {
    ?>
    <footer class="row">
      <p class="col-md-4 col-sm-4 col-xs-12 copyright">&copy; <a href="http://www.thulasidas.com" target="_blank">Manoj Thulasidas</a> 2013 - <?php echo date('Y') ?></p>
      <?php
      if ($showPPP) {
        ?>
        <p class="col-md-4 col-sm-4 col-xs-12"><img class="col-md-4 col-sm-4 col-xs-4 center" src="<?php echo $ezAdminUrl; ?>img/paypal-partner.png" alt="Official PayPal Partner" title="EZ PayPal developer is an official PayPal partner" data-toggle="tooltip"/></p>
        <?php
      }
      ?>
      <p class="col-md-4 col-sm-4 col-xs-12 powered-by pull-right"><a class='popup-long' title='Learn more about this application' data-toggle='tooltip' target='_blank' href='http://www.thulasidas.com/<?php echo $app->key; ?>'><?php echo $app->name; ?></a> by <a title='Tips and tricks on blog optimization, server stability, advertising, and all things WordPress releated and beyond.' data-toggle='tooltip' target='_blank' href="http://ads-ez.com/">Ads EZ for Pro Bloggers</a></p>
    </footer>
    <?php
    if (!empty(EZ::$options['show_google_translate'])) {
      ?>
      <!-- Google translator -->
      <script type='text/javascript'>
        function googleTranslateElementInit() {
          new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
        }
      </script>
      <script type='text/javascript' src='//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit'></script>
      <!-- Exceptions to Google translator -->
      <script>
        $(document).ready(function () {
          $("code, pre").addClass('notranslate');
          $("a.xedit[data-type='text']").addClass('notranslate');
          $("a.xedit[data-type='textarea']").addClass('notranslate');
          $("a.xedit:not([data-type])").addClass('notranslate');
        });
      </script>
      <?php
    }
    if (EZ::isInWP()) {
      $target = basename($_SERVER['REQUEST_URI']);
      update_option("$app->key-last-src", $target);
    }
  }
  else if ($showPPP) {
    ?>
    <footer class="row">
      <p class="col-md-12 col-sm-12 col-xs-12"><img class="col-md-1 col-sm-1 center" src="<?php echo $ezAdminUrl; ?>img/paypal-partner.png" alt="Official PayPal Partner" title="EZ PayPal developer is an official PayPal partner" data-toggle="tooltip"/></p>
    </footer>
    <?php
  }
  ?>

  </div><!-- .ch-container -->

  <!-- external javascript -->
  <script src='<?php echo $ezAdminUrl; ?>js/bootstrap.min.js'></script>
  <script src='<?php echo $ezAdminUrl; ?>js/bootstrap-editable.min.js'></script>
  <script src='<?php echo $ezAdminUrl; ?>js/bootstrap-tour.min.js'></script>
  <script src='<?php echo $ezAdminUrl; ?>js/bootstrapValidator.min.js'></script>
  <script src='<?php echo $ezAdminUrl; ?>js/fileinput.min.js'></script>
  <script src='<?php echo $ezAdminUrl; ?>js/bootbox.min.js'></script>
  <!-- application specific -->
  <?php
  if (!empty($app->js)) {
    foreach ($app->js as $js) {
      echo "  <script src='{$ezAdminUrl}js/$js.js'></script>\n";
    }
  }
  ?>
  <script src='<?php echo $ezAdminUrl; ?>js/charisma.js'></script>
  </body>
  </html>

  <?php
}
