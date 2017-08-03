<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">New Banners</a>
    </li>
  </ul>
</div>

<?php
openBox("New Banners", "flag", 12);
?>
<p>This feature is available in the  <a href="http://buy.thulasidas.com/ads-ez" title="Get Ads EZ Pro for $15.95" class="goPro">Pro version</a> of this program, which allows you to upload multiple banners and edit their meta data in a neat interface. </p>
<p>In this lite version, you can upload the banner to your <code>banners</code> folder or any subfolder below it and run the Batch Process to update the meta data.</p>
<hr>
<h4>Screenshot of this page from the <a href="#" class="goPro">Pro</a> Version</h4>
<?php
showScreenshot(12);
?>
<div class="clearfix"></div>
<?php
closeBox();
require 'promo.php';
require 'footer.php';
