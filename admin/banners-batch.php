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

function rglob($pattern, $flags = 0) {
  $files = glob($pattern, $flags);
  if (empty($files)) {
    $files = array();
  }
  $dirs = glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT);
  if (empty($dirs)) {
    $dirs = array();
  }
  if (is_array($dirs)) {
    foreach ($dirs as $dir) {
      $files = array_merge($files, rglob($dir . '/' . basename($pattern), $flags));
    }
  }
  return $files;
}

$bannerMeta = EZ::getAllAds();
$all = rglob('banners/*');
$dirs = rglob('banners/*', GLOB_ONLYDIR);
$banners = array_diff($all, $dirs);
$ajax = "success";
?>
<style scoped>
  div.all {display:inline-block; margin-right:10px; margin-bottom:10px; width:520px;height:275px;overflow:hidden; border: 1px dotted black; padding:5px;}
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
      <a href="#">Batch Process</a>
    </li>
  </ul>
</div>

<?php
insertAlerts(12);
openBox("Processing All New Banners", "repeat", 12, "<p>This page helps you enter the metadata required for new banners before they can be served. The items displayed with the banners below are editable. Click on any underlined quantity to edit it. Click on the thumbnail of the banner to see it in full size.</p>"
        . "<p>If you don't have any new banners (that is, banners with no data in the database), this page will be empty. Don't be alarmed, you can still <b><a href='banners-edit.php'>Edit Banners</a></b> to see and modify your existing banners.</p>");
$catSource = EZ::mkCatSource();
$catNames = EZ::mkCatNames();
$actSource = "[{value: 1, text: 'Active'}, {value:0, text: 'Disabled'}]";
$bannerId = 0;
$noBanners = true;
foreach ($banners as $b) {
  $color = "#fdd";
  $style = "style='background-color:$color'";
  $class = "class='all shown'";
  if (!empty($bannerMeta[$b])) { // banner in the DB. Ignore.
    continue;
  }
  $noBanners = false;
  $id = $title = $target = $category = $catName = $active = '';
  $md5 = md5($b);
  $iSize = getimagesize($b);
  $width = $iSize[0];
  $height = $iSize[1];
  $size = "{$width}x{$height}";
  $value = "[{file:'$b', width:'$width', height:'$height', active:'1'}]";
  ++$bannerId;
  echo <<<EOF

      <div $style $class>
        <div class='left'>
          <a href='../$b' data-toggle="lightbox" class='thumbnail'>
            <img src='../$b' alt='$b' class='thumb thumbnails' />
          </a>
        </div>
        <div class='right'>
          <label>Target URL</label>
          <a id='target-$bannerId' class='xedit wrap' data-name='target' data-tpl='<input type="text" style="width:400px">' data-type='text' data-pk='$id' data-title='Target URL'>$target</a>
          <br />
          <label>Title/Alt</label>
          <a id='title-$bannerId' class='xedit wrap' data-name='title' data-type='text' data-tpl='<input type="text" style="width:500px">' data-pk='$id' data-title='Title/Alt'>$title</a>
          <br />
          <label>Width</label> <a id='width-$bannerId' class='xedit' data-name='width' data-type='text' data-pk='$id' data-title='Width'>$width</a><label style="width:40px"></label>
          <label>Height</label> <a id='height-$bannerId' class='xedit' data-name='height' data-type='text' data-pk='$id' data-title='Height'>$height</a>
          <br />
          <label>Category</label>
          <a id='category-$bannerId' class='xedit' data-name='category' data-type='select' data-pk='$id' data-title='Category' data-source="$catSource" data-value='$category'>$catName</a>
          <br />
          <label>Status</label>
          <a id='active-$bannerId' class='xedit' data-name='active' data-type='select' data-pk='$id' data-title='Status' data-source="$actSource" data-value='$active'></a>
          <br />
          <br />
          <label>Id</label><span id='pk-$bannerId'>$id</span>
          <br />
          <label>File</label><span id='file-$bannerId'><code>$b</code></span>
          <br />
          <br />
          <a id='$bannerId' class='btn-sm btn-success createBanner' data-name='banner' data-type='banner' data-title='Create a New Banner' title='Create a new banner with this image.' data-value="$value"><i class="glyphicon glyphicon-flag icon-white"></i> Create a New Banner</a>

        </div>
      </div>

EOF;
}
if ($noBanners) {
  ?>
  <div>No new banners found. All your banners seem to be in the database. If you want to modify the banner information in the database, please <a href="banners-edit.php">Edit Banners</a>.</div>
  <?php
}
closeBox();
?>
<script>
  var xeditHandler = 'ajax/success.php';
  $(document).ready(function () {
    $('.createBanner').click(function () {
      var id = $(this).attr('id');
      $.ajax({url: 'ajax/banners-new.php',
        type: 'POST',
        data: {
          file: $(this).siblings("#file-" + id).text(),
          target: $(this).siblings("#target-" + id).text(),
          title: $(this).siblings("#title-" + id).text(),
          category: $(this).siblings("#category-" + id).text(),
          width: $(this).siblings("#width-" + id).text(),
          height: $(this).siblings("#height-" + id).text(),
          active: $(this).siblings("#active-" + id).text()
        },
        success: function (pk) {
          $("#pk-" + id).text(pk);
          $("#" + id).attr('disabled', 'disabled').text('Already Saved');
          xeditHandler = 'ajax/banners.php';
          $('.xedit').editable('option', 'url', xeditHandler).editable('option', 'pk', pk);
        },
        error: function (a) {
          flashError(a.responseText);
        }
      });
    });
  });
</script>
<?php
chdir($pwd);
require 'footer.php';
