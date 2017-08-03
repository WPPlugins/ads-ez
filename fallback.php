<?php
  echo <<<EOF0
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
  <head>
  <title>Easy Ads for WordPress</title>
  <style type='text/css'>
    body {margin:0; height:100%; background-color:transparent; width:100%; text-align:center;}
    #ezwrapper {border: 1px solid black; margin: 0; padding: 0px; position: relative;}#ezfooter {position:absolute;bottom:0;border-width:1px;border-color:#000;height:12px;padding:1px;font-family:Arial;font-size:10px;font-weight:bold;color:#fff;background:#b00;} #ezfooter a:link, #ezfooter a:visited, #ezfooter a:hover, #ezwrapper a:link {color:#fff;text-decoration:none;} </style>
  </head>
  <body>
    <div id="ezwrapper"><a href='ck.php?url=$badgeTarget' target='_blank'><div style="display:block;height:$height;background-color:#3CADEB;font-size:$fontsize;font-family:arial;padding:2vh">Your ads will be inserted here by <b>Ads EZ!</div><div style="height:$height;background-color:#B1FF79;color:#308;font-size:$fontsize;font-family:arial;padding:2vh">Get Your Own Personal Ad Server!</b></div><div style="height:$height;background-color:#FFE779;color:#028;font-size:$small;font-family:arial;padding:2vh">Please visit your admin page and set up your ads and banners now!</div></a>
EOF0;
  if ($badgeEnabled) {
    echo <<<EOF1
 <div id='ezfooter'><a href='$badgeTarget' title='$title' id='ezlink' target='_blank' onmouseover='this.innerHTML="$badgeLong"' onmouseout='this.innerHTML="EZ"'>$badgeShort</a></div>
EOF1;
  }
  echo <<<EOF3
     </div>
  </body>
</html>
EOF3;
