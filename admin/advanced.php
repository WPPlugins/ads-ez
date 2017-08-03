<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Advanced Tools</a>
    </li>
  </ul>
</div>

<?php
openBox("Advanced Tools", "cog", 12);
?>
<h3>Advanced Tools and Options</h3>
<p>This page is a collection of advanced tools and options that you can use to tweak your Ads EZ installation just the way you like it.</p>
<p> The following tools and the associated options are available in this advanced section of  the  <a href="http://buy.thulasidas.com/ads-ez" title="Get Ads EZ Pro for $15.95" class="goPro">Pro version</a> of this program. </p>

<ul>
  <li><b>Caching</b>: Ads EZ caches your data in memory for faster ad serving, which can make a significant performance difference for busy servers. In this section, you will see tools to visualize and manage caches, such as:<ul>
      <li><b>Cache Statistics</b>: View your ads cache and flush them, if needed</li>
      <li><b>Cache Visualization</b>: Graphical visualization of the memory cache if using APC (recommended).</li>
    </ul>
  </li>
  <li><b>CDN support</b>: You can specify a content delivery network (CDN) where your banners will be served from.</li>
  <li>
    <b>Banner Validation</b>: A tool to validate banners, which will:
    <ul><li>Validate the target URLs, </li>
      <li>Ensure that the banner file exists,</li>
      <li>Check if the file name has changed, and</li>
      <li>Verify the categories.</li>
    </ul>
  </li>
</ul>
<hr>
<h4>Screenshot of Advanced Tools from the <a href="#" class="goPro">Pro</a> Version</h4>
<?php
showScreenshot(10);
?>
<div class="clearfix"></div>
<?php
closeBox();
require 'promo.php';
require 'footer.php';
