<?php
/*
Plugin Name: Price Table Widget
Description: A powerful yet simple price table widget for your sidebars or Page Builder pages.
Version: 1.0.6
Author: Greg Priday
Author URI: http://siteorigin.com
Plugin URI: http://siteorigin.com/price-table-widget/
License: GPL3
License URI: license.txt
*/

define('SOW_PT_VERSION', '1.0.6');
define('SOW_PT_FILE', __FILE__);

if( !class_exists('SiteOrigin_Widgets_Loader') ) include(plugin_dir_path(__FILE__).'base/loader.php');
new SiteOrigin_Widgets_Loader('price-table', __FILE__, plugin_dir_path(__FILE__).'inc/widget.php');