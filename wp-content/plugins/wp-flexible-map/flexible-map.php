<?php
/*
Plugin Name: Flexible Map
Plugin URI: http://flexible-map.webaware.net.au/
Description: Embed Google Maps in pages and posts, either by centre coodinates or street address, or by URL to a Google Earth KML file.
Version: 1.9.2
Author: WebAware
Author URI: http://webaware.com.au/
Text Domain: flexible-map
Domain Path: /languages/
*/

/*
copyright (c) 2011-2015 WebAware Pty Ltd (email : support@webaware.com.au)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if (!defined('ABSPATH')) {
	exit;
}


define('FLXMAP_PLUGIN_FILE', __FILE__);
define('FLXMAP_PLUGIN_ROOT', dirname(__FILE__) . '/');
define('FLXMAP_PLUGIN_NAME', basename(dirname(__FILE__)) . '/' . basename(__FILE__));
define('FLXMAP_PLUGIN_VERSION', '1.9.2');

// shortcode tags
define('FLXMAP_PLUGIN_TAG_MAP', 'flexiblemap');


// instantiate the plug-in
require FLXMAP_PLUGIN_ROOT . 'includes/class.FlxMapPlugin.php';
$FlxMapPlugin = FlxMapPlugin::getInstance();

/**
* utility function so themes can easily display the map
* to return as a string without output to screen, add 'echo'=>'false' to array of attributes
* @param array $attrs
* @return string
*/
function flexmap_show_map($attrs) {
	$plugin = FlxMapPlugin::getInstance();
	$map = $plugin->getMap($attrs);

	if (!isset($attrs['echo']) || FlxMapPlugin::isYes($attrs['echo'])) {
		echo $map;
	}

	return $map;
}

/**
* load the scripts required for the maps to work, e.g. for single-page AJAX websites
* @param array $locales optional: an array of required locale scripts
*/
function flexmap_load_scripts($locales = array()) {
	wp_enqueue_script('flxmap');

	if (count($locales) > 0) {
		$plugin = FlxMapPlugin::getInstance();
		$plugin->setLocales($locales);
	}
}
