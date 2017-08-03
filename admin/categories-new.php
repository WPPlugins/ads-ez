<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">New Category</a>
    </li>
  </ul>
</div>

<?php
openBox("New Category", "plus", 12);
?>
<p>This feature is available in the  <a href="http://buy.thulasidas.com/ads-ez" title="Get Ads EZ Pro for $15.95" class="goPro">Pro version</a> of this program, which allows you to create new categories and edit their meta data in a neat interface. </p>
<p>In this lite version, you have three categories already defined for you. You can <a href="categories.php">view and edit</a> them according to your preference. If you need to define more than three categories, you will have to do direct database manipulation to add or delete categories. See the <code>categories</code> table in your database using a DB tool such as <b>phpMyAdmin</b>.</p>
<?php
closeBox();
require 'promo.php';
require 'footer.php';
