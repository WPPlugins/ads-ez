<div class="col-lg-4 col-sm-12">
  <h4>Play with a Demo</h4>
  If you would like to play with the admin interface without messing up your installation, <a href="http://demo.thulasidas.com/<?php echo EZ::$slug; ?>" title='Visit the demo site to play with the admin interface' data-toggle='tooltip' target="_blank">please visit <?php echo EZ::$name; ?> demo site</a>.
  <div id='supportChannels'>
    <h4>Need Support?</h4>
    <ul>
      <li>Please check the carefully prepared <a href="http://www.thulasidas.com/plugins/<?php echo EZ::$slug; ?>#faq" class="popup-long" title='Your question or issue may be already answered or resolved in the FAQ' data-toggle='tooltip'> Plugin FAQ</a> for answers.</li>
      <?php
      if (EZ::$isPro) {
        ?>
        <li>The Pro version comes with a short <a href='http://support.thulasidas.com/open.php' class='popup btn-xs btn-success' title='Open a support ticket if you have trouble with your Pro version. It is free during the download link expiry time.' data-toggle='tooltip'>Free Support</a>.</li>
        <?php
      }
      else {
        ?>
        <li>For the lite version, you may be able to get support from the <a href='https://wordpress.org/support/plugin/<?php echo EZ::$wpslug; ?>' class='popup' title='WordPress forums have community support for this plugin' data-toggle='tooltip'>WordPress support forum</a>.</li>
        <li class="text-success bg-success">Visit the <a href='http://buy.thulasidas.com/update.php' class='popup btn-xs btn-success' title='If you purchased the Pro version of this plugin, but did not get an automated email or a download page, please click here to find it.' data-toggle='tooltip'>Product Delivery Portal</a> to download the Pro version, if you have purchased it.</li>
        <?php
      }
      ?>
      <li>For preferential support and free updates, you can purchase a <a href='http://buy.thulasidas.com/support' class='popup btn-xs btn-info' title='Support contract costs only $4.95 a month, and you can cancel anytime. Free updates upon request, and support for all the products from the author.' data-toggle='tooltip'>Support Contract</a>.</li>
      <li>For one-off support issues, you can raise a one-time paid <a href='http://buy.thulasidas.com/ezsupport' class='popup btn-xs btn-primary' title='Support ticket costs $0.95 and lasts for 72 hours' data-toggle='tooltip'>Support Ticket</a> for prompt support.</li>
      <li>Please include a link to your blog when you contact the plugin author for support.</li>
    </ul>
  </div>
  <h4>Happy with this plugin?</h4>
  Please leave a short review and rate it at <a href="https://wordpress.org/plugins/<?php echo EZ::$wpslug; ?>/" class="popup-long" title='Please help the author and other users by leaving a short review for this plugin and by rating it' data-toggle='tooltip'>WordPress</a>. Thanks!
  <div class="text-success bg-success" style="margin-top:5px;padding:5px">
    <h4>Admin Pages in your Language</h4>
    <?php
    $show_google_translate = array('name' => __('Enable Translation?', 'easy-common'),
        'help' => __('This application can display its admin pages using Google<sup>&reg;</sup> Translate. If you would like to see the pages in your language, please enable this option. You will then see a language selector near the top right corner where you can choose your language.', 'easy-common'),
        'type' => 'checkbox',
        'reload' => true,
        'value' => false);
    ?>
    <table class="table table-striped table-bordered responsive">
      <thead>
        <tr style="line-height:0px;padding:0">
          <th style="width:50%;min-width:150px;padding:0"></th>
          <th style="width:55%;min-width:80px;padding:0"></th>
          <th class="center-text" style="width:15%;min-width:50px;padding:0"></th>
        </tr>
      </thead>
      <tbody>
        <?php
        echo EZ::renderOption('show_google_translate', $show_google_translate);
        ?>
      </tbody>
    </table>
    <div class="clearfix"></div>
  </div>
</div>
<div class="clearfix"></div>
<script>
  var xeditHandler = 'ajax/options.php';
  var xparams = {};
  $(document).ready(function () {
    $("#showSupportChannels").click(function (e) {
      e.preventDefault();
      var bg = $("#supportChannels").css("backgroundColor");
      var fg = $("#supportChannels").css("color");
      $("#supportChannels").css({backgroundColor: "yellow", color: "black"});
      setTimeout(function () {
        $("#supportChannels").css({backgroundColor: bg, color: fg});
      }, 500);
    });
    setTimeout(function () {
      $(".xeditReload").editable('option', 'success', function () {
        ezReload();
      });
    }, 1500);
  });
</script>
