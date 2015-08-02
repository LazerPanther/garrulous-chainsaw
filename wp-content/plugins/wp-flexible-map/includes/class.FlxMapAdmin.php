<?php

/**
* class for admin screens
*/
class FlxMapAdmin {

	protected $plugin;

	/**
	* @param FlxMapPlugin $plugin
	*/
	public function __construct($plugin) {
		$this->plugin = $plugin;

		// add action hook for adding plugin meta links
		add_filter('plugin_row_meta', array($this, 'addPluginDetailsLinks'), 10, 2);
	}

	/**
	* action hook for adding plugin details links
	*/
	public function addPluginDetailsLinks($links, $file) {
		// add settings link
		if ($file == FLXMAP_PLUGIN_NAME) {
			$links[] = sprintf('<a href="https://wordpress.org/support/plugin/wp-flexible-map" target="_blank">%s</a>', _x('Get Help', 'plugin details links', 'flexible-map'));
			$links[] = sprintf('<a href="https://wordpress.org/plugins/wp-flexible-map/" target="_blank">%s</a>', _x('Rating', 'plugin details links', 'flexible-map'));
			$links[] = sprintf('<a href="https://translate.webaware.com.au/projects/flexible-map" target="_blank">%s</a>', _x('Translate', 'plugin details links', 'flexible-map'));
			$links[] = sprintf('<a href="http://shop.webaware.com.au/downloads/flexible-map/" target="_blank">%s</a>', _x('Donate', 'plugin details links', 'flexible-map'));
		}

		return $links;
	}

}
