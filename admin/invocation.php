<?php require 'header.php'; ?>
<style scoped>
  .all {display:inline-block; margin-right:10px; margin-bottom:10px; max-width:750px;min-width: 200px;width:100%;overflow:hidden; border: 1px dotted black; padding:5px;}
  label{width:90px}
  a.xedit{width:70px;display:inline-block}
</style>

<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Invocation Code</a>
    </li>
  </ul>
</div>
<h3>Generate Your Ad Invocation Codes</h3>
<?php
openBox("Invocation Code", "picture", 10, "<p>In order to serve your ads on your websites, you will paste the HTML snippets (so-called invocation codes) that you generate on this page wherever you want the ads to appear.</p><p>The ads can be served based on size and category. For instance, if you want to serve 300x250 ads for eBooks on your book review sites, please select the category and size accordingly. If you want all the ads of a particular size to appear, you can select <b>All</b> as the category.</p><p>The size parameter, on the other hand, cannot be ignored, and defaults to 300x250.</p>");
$catSource = "[{value: 'All', text:'All'},";
$categories = $db->getData('categories', '*', 'active=1');
foreach ($categories as $cat) {
  extract($cat);
  $catSource .= "{value: '$name', text: '$name'},";
}
$catSource .= "]";

$sizes = $db->getColData('banners', 'size');
$defaultSize = $sizes[0];
$sizeSource = "[";
foreach ($sizes as $size) {
  $sizeSource .= "{value: '$size', text: '$size'},";
}
$sizeSource .= "]";
?>
<div class='all'>
  <p>Filter your ads by category and size to generate the invocation codes. </p>
  <label>Category:</label>
  <a id='category' href="#" class='xedit' data-name='category' data-type='select' data-pk='1' data-title='Category' data-source="<?php echo $catSource; ?>" data-value='All'>All</a>
  <br />
  <label>Size:</label>
  <a id='size' href="#" class='xedit' data-name='size' data-type='select' data-pk='1' data-title='Size' data-source="<?php echo $sizeSource; ?>" data-value='<?php echo $defaultSize; ?>'><?php echo $defaultSize; ?></a>
  <br />
</div>
<div class="clearfix"></div>
<div class='all'>
  <p>Copy and paste this code on your page where you would like to see the ads.</p>
  <pre id="code" onclick="containerSelect(this)"></pre>
</div>
<div class="clearfix"></div>
<?php
closeBox();
?>
<div class="clearfix"></div>

<?php openBox("Preview Your Ad", "eye-open", 10);
?>
<div class='all'>
  <p> <a class="btn-sm btn-success" href="#" data-toggle='tooltip' title='Refresh the ad to see a new one of the same size and category.' id='refresh'><i class="glyphicon glyphicon-refresh icon-white"></i></a> &nbsp;Preview of your ad.</p>
  <div class="all" id='preview'></div>
  <br />
</div>
<?php
closeBox();
?>
<script>
  $(document).ready(function () {
    $('.xedit').editable({mode: 'inline', url: 'ajax/success.php', display: function (value) {
        $(this).text(value);
        refresh();
      }});
    $('#refresh').click(function () {
      refresh();
    });
    refresh();
    function refresh() {
      $('#code').text(setCode());
      $('#preview').html(setCode());
    }
    function setCode() {
      var category = $('#category').text();
      if (category === "All") {
        category = "";
      }
      var size = $('#size').text();
      var sizeArray = size.split("x");
      var w = sizeArray[0];
      var h = sizeArray[1];
      var url = location.href.replace('?inframe', '');
      var queryString = '?';
      if (category && category !== 'All') {
        queryString += 'cat=' + category + '&';
      }
      if (size) {
        queryString += 'size=' + size;
      }
      url = url.replace('admin/invocation', 'ad');
      return '<iframe src="' + url + queryString + '" width="' + w + '" height="' + h + '" style="border:0;margin-left:10px;overflow:hidden"></iframe>';;
    }
  });
</script>

<?php

require 'footer.php';
