<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">New HTML Ad</a>
    </li>
  </ul>
</div>

<?php
openBox("New HTML Ad", "plus", 12);
?>
<p>This feature is available in the  <a href="http://buy.thulasidas.com/ads-ez" title="Get Ads EZ Pro for $15.95" class="goPro">Pro version</a> of this program, which allows you to create and edit HTML ads in a neat interface. </p>
<p>In this lite version, you have only banner ads.</p>
<?php
closeBox();
require 'promo.php';
require 'footer.php';
