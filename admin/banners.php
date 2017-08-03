<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Banners</a>
    </li>
  </ul>
</div>
<h3>All Your Banners</h3>
<?php
openBox("Banners", "th-list", 12, "<p>The table below listing your banners is editable. To see the banner, click on the ID number.</p>"
        . "<p>You can click on the Alt/Title or Target and enter new values.  You also can set a banner inactive (which means it won't be served) by clicking on the green <b>Active</b> button. An inactive button will have a red <b>Disabled</b> button.</p>"
        . "<p> If you want to upload new banners, please use the menu item <a href='banners-new.php'><b>New Banners</b></a>.<p>");
?>
<table class="table table-striped table-bordered responsive data-table">
  <thead>
    <tr>
      <th class="center-text" style='width:5%'>ID</th>
      <th style='width:35%'>Target</th>
      <th style='width:40%'>Alt/Title</th>
      <th class="center-text" style='width:6%'>Width</th>
      <th class="center-text" style='width:6%'>Height</th>
      <th class="center-text" style='width:8%;min-width:90px;'>Active?</th>
    </tr>
  </thead>
  <tbody>

    <?php
    $banners = $db->getData('banners');
    foreach ($banners as $b) {
      extract($b);
      if ($active) {
        $class = 'success';
      }
      else {
        $class = 'danger';
      }
      $url = EZ::adsURL() . $file;
      echo <<<EOF
    <tr>
      <td><a href='$url' data-toggle="lightbox" class='thumbnail center-text endpoint'>$id</a></td>
      <td><a class='xedit' data-name='target' data-pk='$id' data-tpl='<input type="text" style="width:400px">' data-validator='url'>$target</a></td>
      <td><a class='xedit' data-name='title' data-pk='$id' data-tpl='<input type="text" style="width:450px">'>$title</a></td>
      <td class="center-text">$width</td>
      <td class="center-text">$height</td>
      <td class="center-text"><a class='xedit-checkbox btn-sm btn-$class' data-name='active' data-type='checklist' data-pk='$id' data-title='Status' data-value='$active'></a></td>
    </tr>
EOF;
    }
    ?>
  </tbody>
</table>

<?php
closeBox();
?>
<script>
  var xeditHandler = 'ajax/banners.php';
</script>
<?php
require 'footer.php';
