<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Statistics</a>
    </li>
  </ul>
</div>

<?php
openBox("Statistics and Charts", "stats", 12);
?>
<p>This feature is available in the  <a href="http://buy.thulasidas.com/ads-ez" title="Get Ads EZ Pro for $15.95" class="goPro">Pro version</a> of this program, which allows you to view your ad serving statistics to pinpoint opportunities to optimize your revenue. </p>
<hr>
<h4>Screenshot of Statistics Page from the <a href="#" class="goPro">Pro</a> Version</h4>
<?php
showScreenshot(9);
?>
<div class="clearfix"></div>

<?php
closeBox();
require 'promo.php';
require 'footer.php';
