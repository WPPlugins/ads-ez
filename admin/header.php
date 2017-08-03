<?php
error_reporting(E_ALL);

require_once '../AdsEZ.php';

if (empty($adsEz)) {
  $adsEz = new AdsEZ();
  $GLOBALS['adsEz'] = $adsEz;
}

$adsEz->css = array('bootstrap.lightbox', 'dropzone', 'summernote',
    'dataTables.bootstrap');

$adsEz->js = array('bootstrap.lightbox', 'jquery.dataTables.min', 'dataTables.bootstrap', 'ads-ez');

require_once 'header-functions.php';

if (menuShown()) {
  $tablesRequired = array('administrator', 'banners', 'categories', 'options_meta');
  require_once 'lock.php';
}

include_once('../debug.php');

renderHeader($adsEz);
?>
<div class="ch-container">
  <div class="row">
    <?php
    $width = 12;
    if (menuShown()) {
      $menu = array();
      $subMenu = array();
      $subMenu[] = array('text' => 'Banners');
      $subMenu[] = array(
          'url' => 'banners.php',
          'text' => 'All Banners',
          'icon' => 'th-list'
      );
      $subMenu[] = array(
          'url' => 'banners-edit.php',
          'text' => 'Edit Banners',
          'icon' => 'pencil'
      );
      $subMenu[] = array(
          'url' => 'banners-batch.php',
          'text' => 'Batch Process',
          'icon' => 'repeat'
      );
      $subMenu[] = array(
          'url' => 'banners-new.php',
          'text' => 'New Banners',
          'icon' => 'flag'
      );
      $menu[] = $subMenu;

      $subMenu = array();
      $subMenu[] = array('text' => 'HTML Ads');
      $subMenu[] = array(
          'url' => 'htmlAds.php',
          'text' => 'All HTML Ads',
          'icon' => 'th-large',
          'class' => 'red'
      );
      $subMenu[] = array(
          'url' => 'htmlAds-edit.php',
          'text' => 'New HTML Ad',
          'icon' => 'plus',
          'class' => 'red'
      );
      $menu[] = $subMenu;

      $subMenu = array();
      $subMenu[] = array('text' => 'Categories &amp; Stats');
      $subMenu[] = array(
          'url' => 'categories.php',
          'text' => 'Categories',
          'icon' => 'folder-open'
      );
      $subMenu[] = array(
          'url' => 'categories-new.php',
          'text' => 'New Category',
          'icon' => 'plus',
          'class' => 'red'
      );
      $subMenu[] = array(
          'url' => 'stats.php',
          'text' => 'Statistics',
          'icon' => 'stats',
          'class' => 'red'
      );
      $menu[] = $subMenu;

      $subMenu = array();
      $subMenu[] = array('text' => 'Integration');
      $subMenu[] = array(
          'url' => 'invocation.php',
          'text' => 'Invocation',
          'icon' => 'picture',
          'class' => 'red'
      );
      $subMenu[] = array(
          'url' => 'openx.php',
          'text' => 'OpenX Replacement',
          'icon' => 'trash',
          'class' => 'red'
      );
      $menu[] = $subMenu;

      renderMenu($menu);
      if (!EZ::isTopMenu()) {
        $width = 10;
      }
    }
    ?>
    <div id="content" class="col-lg-<?php echo $width; ?> col-sm-<?php echo $width; ?>">
    <?php
    if (!menuShown()) {
      renderLogo($adsEz);
    }
    ?>
    <!-- content starts -->
