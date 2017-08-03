<?php

$options = array();
$options['salt'] = array('name' => __('DB Security Salt', 'ads-ez'),
    'type' => 'hidden',
    'help' => __('Not visible to the end user', 'ads-ez'));
$options['badge_enable'] = array('name' => __('Enable Badge', 'ads-ez'),
    'help' => __('Ads EZ can show a small badge near the bottom left corner of your badges and ads, similar to traditional ad providers.', 'ads-ez'),
    'type' => 'checkbox',
    'value' => 0);
$options['badge_target'] = array('name' => __('Badge Target', 'ads-ez'),
    'value' => 'http://ads-ez.com/ads/',
    'help' => __('The URL target where your users will be taken to when they click on the badge.', 'ads-ez'));
$options['badge_short_text'] = array('name' => __('Badge Short Text', 'ads-ez'),
    'value' => 'EZ',
    'help' => __('The short text that is displayed as the badge. When the user hovers over the badge, it will expand to the long text.', 'ads-ez'));
$options['badge_long_text'] = array('name' => __('Badge Long Text', 'ads-ez'),
    'value' => 'Ads by Ads EZ',
    'help' => __('The long text that is displayed as the badge on mouseover. When the user hovers over the badge, it will expand from the short text to the long text.', 'ads-ez'));
$options['fallback_ad'] = array('name' => __('Fallback Ad', 'ads-ez'),
    'value' => 'fallback.php',
    'help' => __('If no ads can be found for the category and size requested, a fallback can be served. Please ensure that the fallback ad gracefully scales to various sizes. The default fallback ad (<code>fallback.php</code> in the installation folder) is a good starting point to design your own. If you need <a class="goPro" href="http://buy.thulasidas.com/customization" data-product="customization">customization</a> help, please contact the author.', 'ads-ez'));
$options['show_google_translate'] = array('name' => __('Enable Google Translation of Admin Pages?', 'ads-ez'),
    'help' => __('This plugin can display its admin pages using Google Translate. If you would like to see the pages in your language, please enable this option. You will then see a language selector near the top right corner where you can choose your language.', 'ads-ez'),
    'type' => 'checkbox',
    'reload' => true,
    'value' => false);
$options['dynamic_menu'] = array('name' => __('Enable Dynamic Menu?', 'ads-ez'),
    'type' => 'checkbox',
    'help' => __('By default, the menu on the admin pages expands on hover, which you may find annoying in the standalone mode. You can disable the feature here.', 'ads-ez'),
    'value' => true,
    'reload' => true);
