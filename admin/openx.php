<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">OpenX Replacement</a>
    </li>
  </ul>
</div>

<?php
openBox("Replace your OpenX/Revive server", "trash", 12);
?>
<p>Ads EZ is designed to be drop-in replacement for OpenX (now known as Revive), a comprehensive and heavy-weight ad server. OpenX has a steep learning curve, and a sluggish and confusing interface, which is a bit dated. It also has a complex ad serving mechanism with click and impression counting and caching that can put your server under enormous load.</p>
<p>Ads Ez, on the other hand, is extremely light-weight. It is meant for personal ad serving, with impression statistics. With a couple of <code>.htaccess</code> directives that you can generate on this page, it is possible to use Ads EZ as a direct replacement for your OpenX server with tremendous performance boost.</p>
<p>This feature is only available in the  <a href="http://buy.thulasidas.com/ads-ez" title="Get Ads EZ Pro for $15.95" class="goPro">Pro version</a> of this program, which allows you to generate and tweak your server directives in a neat interface. </p>
<hr>
<h4>Screenshot of OpenX Integration from the <a href="#" class="goPro">Pro</a> Version</h4>
<?php
showScreenshot(11);
?>
<div class="clearfix"></div>
<?php
closeBox();
require 'promo.php';
require 'footer.php';
