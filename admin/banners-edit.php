<?php
require_once('header.php');
$pwd = getcwd();
chdir('..');

function blush($color) {
  $color = trim(str_replace("#", "", $color));
  $len = strlen($color);
  $dec = hexdec($color);
  if ($len == 3) {
    $dec += 2 * pow(16, 2);
    $dec = min(array(0xfaa, $dec));
  }
  else if ($len == 6) {
    $dec += 2 * pow(16, 5);
    $dec = min(array(0xffaaaa, $dec));
  }
  $hex = dechex($dec);
  return "#$hex";
}

$bannerMeta = EZ::getAllAds();
$banners = array_keys($bannerMeta);
?>
<style scoped>
  div.all {display:inline-block; margin-right:10px; margin-bottom:10px;width:520px;height:275px;overflow:hidden; border: 1px dotted black; padding:5px;}
  div.right{float:right;display:inline-block;width:400px;padding:0;border:none;margin:0}
  div.left{float:left;display:inline-block;width:100px;padding:0;border:none;margin:0;text-align: center}
  img.thumb {max-height:240px;max-width:100%;margin:auto;padding:0;border:none;}
  label{width:90px}
  a.wrap{display:inline-block;width:300px;vertical-align:middle}
</style>

<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Edit Banners</a>
    </li>
  </ul>
</div>

<button id="hide" title="If you want to hide images that already have all the required info, click here." data-toggle="tooltip" data-placement="right" class="btn btn-info">Hide</button>
<button id="show" title="By default, this editor shows only those banners that need editing. If you would like to se all banners, including those with all the required info already entered, please click on this button." data-toggle="tooltip" data-placement="right" class="btn btn-primary" style="display:hidden">Show</button>
<?php
openBox("Editing Banner Meta Data", "pencil", 12, "<p>The meta data items displayed with the banners below are editable. Click on any underlined quantity to edit it. Click on the thumbnail of the banner to see it in full size.</p><p>By default, this editor shows only those banners that lack some required data. If you would like to se all banners, including those with all the required info already entered, please click on the <b>Show</b> button.</p>");
$catSource = EZ::mkCatSource();
$catNames = EZ::mkCatNames();
$actSource = "[{value: 1, text: 'Active'}, {value:0, text: 'Disabled'}]";
$noBanners = true;
foreach ($banners as $b) {
  $color = "#ddd";
  if (!empty($bannerMeta[$b])) { // banner in the DB
    extract($bannerMeta[$b]);
    $class = "class='all hideable'";
    $style = "style='background-color:$color'";
    $catName = $catNames[$category];
  }
  else {
    $id = $title = $target = $category = $active = $size = '';
    $md5 = md5($b);
    $iSize = getimagesize($b);
    $width = $iSize[0];
    $height = $iSize[1];
    $size = "{$width}x{$height}";
    $noBanners = false;
  }
  $style = "";
  if (empty($target)) {
    $color = blush($color);
    $style = "style='background-color:$color'";
    $class = "class='all shown'";
    $noBanners = false;
  }
  if (empty($title)) {
    $color = blush($color);
    $style = "style='background-color:$color'";
    $class = "class='all shown'";
    $noBanners = false;
  }
  if (empty($catName) || $catName == 'unknown') {
    $color = blush($color);
    $style = "style='background-color:$color'";
    $class = "class='all shown'";
    $noBanners = false;
  }

  $iSize = getimagesize($b);

  echo <<<EOF

      <div $style $class>
        <div class='left'>
          <a href='../$b' data-toggle="lightbox" class='thumbnail'>
            <img src='../$b' alt='$b' class='thumb thumbnails' />
          </a>
        </div>
        <div class='right'>
          <label>Target URL</label>
          <a href="#" class='xedit wrap' data-name='target' data-tpl='<input type="text" style="width400px">' data-type='text' data-pk='$id' data-title='Target URL'>$target</a>
          <br />
          <label>Title/Alt</label>
          <a href="#" class='xedit wrap' data-name='title' data-type='text' data-tpl='<input type="text" style="width:500px">' data-pk='$id' data-title='Title/Alt'>$title</a>
          <br />
          <label>Width</label> <a class='xedit' data-name='width' data-type='text' data-pk='$id' data-title='Width'>$width</a><label style="width:40px"></label>
          <label>Height</label> <a class='xedit' data-name='height' data-type='text' data-pk='$id' data-title='Height'>$width</a>
          <br />
          <label>Category</label>
          <a href="#" class='xedit' data-name='category' data-type='select' data-pk='$id' data-title='Category' data-source="$catSource" data-value='$category'>$catName</a>
          <br />
          <label>Status</label>
          <a href="#" class='xedit' data-name='active' data-type='select' data-pk='$id' data-title='Status' data-source="$actSource" data-value='$active'></a>
          <br />
          <br />
          <label>Id</label>$id
          <br />
          <label>File</label><code>$b</code>
        </div>
      </div>

EOF;
}
if ($noBanners) {
  ?>
  <div>All your banners seem to have all the required information in the database. If you want to see and modify the information, please click on the "Show" button above.</div>
  <?php
}
closeBox();
?>
<script>
  $(document).ready(function() {
    $("#hide").hide();
    $("#hide").click(function() {
      $(".hideable").hide(500);
      $("#hide").hide();
      $("#show").show();
    });
    $("#show").click(function() {
      $(".hideable").show(500);
      $("#hide").show();
      $("#show").hide();
    });
    $('.xedit').editable({url: 'ajax/banners.php'});
    $('.hideable').hide();
  });
</script>

<?php
chdir($pwd);
require 'footer.php';
