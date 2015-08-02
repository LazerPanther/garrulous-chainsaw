<?php
	require_once('class-tgm-plugin-activation.php');

	add_action('tgmpa_register', 'my_theme_register_required_plugins');

	function my_theme_register_required_plugins()	{
		$plugins = array(
			array(
				'name'						=> 'CWS PageBuilder plugin', // The plugin name
				'slug'						=> 'cws-pb', // The plugin slug (typically the folder name)
				'source'					=> THEME_DIR . '/plugins/cws-pb.zip', // The plugin source
				'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
				'version' 				=> '1.0.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' 	=> true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
			),
			array(
				'name'						=> 'Revolution Slider Plugin', // The plugin name
				'slug'						=> 'revslider', // The plugin slug (typically the folder name)
				'source'					=> THEME_DIR . '/plugins/revslider.zip', // The plugin source
				'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
				'version' 				=> '4.5.95 SkyWood (16th July 2014)', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' 	=> true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
			),
			array(
				'name'						=> 'CWS Demo Importer Plugin', // The plugin name
				'slug'						=> 'cws-demo-importer', // The plugin slug (typically the folder name)
				'source'					=> THEME_DIR . '/plugins/cws-demo-importer.zip', // The plugin source
				'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
				'version' 				=> '1.0.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' 	=> true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
			),
			array(
				'name'						=> 'Contact Form 7 Plugin', // The plugin name
				'slug'						=> 'contact-form-7', // The plugin slug (typically the folder name)
				'source'					=> 'https://downloads.wordpress.org/plugin/contact-form-7.4.0.1.zip', // The plugin source
				'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
				'version' 				=> '4.0.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' 	=> true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url' 			=> 'https://wordpress.org/plugins/contact-form-7/', // If set, overrides default API URL and points to an external URL
			),	
			array(
				'name'						=> 'WP Flexible Map Plugin', // The plugin name
				'slug'						=> 'wp-flexible-map', // The plugin slug (typically the folder name)
				'source'					=> 'https://downloads.wordpress.org/plugin/wp-flexible-map.1.8.1.zip', // The plugin source
				'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
				'version' 				=> '4.0.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation' 		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' 	=> true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url' 			=> 'https://wordpress.org/plugins/wp-flexible-map/', // If set, overrides default API URL and points to an external URL
			)						
		);

		/**
		* Array of configuration settings. Amend each line as needed.
		* If you want the default strings to be available under your own theme domain,
		* leave the strings uncommented.
		* Some of the strings are added into a sprintf, so see the comments at the
		* end of each line for what each argument will be.
		*/
		$config = array(
			'domain'						=> THEME_SLUG,					// Text domain - likely want to be the same as your theme.
			'default_path' 			=> '',									// Default absolute path to pre-packaged plugins
			'parent_menu_slug' 	=> 'themes.php', 				// Default parent menu slug
			'parent_url_slug' 	=> 'themes.php', 				// Default parent URL slug
			'menu'							=> 'install-required-plugins', 	// Menu slug
			'has_notices'				=> true,												// Show admin notices or not
			'is_automatic'			=> false,							// Automatically activate plugins after installation or not
			'message' 			=> '',							// Message to output right before the plugins table
			'strings'				=> array(
				'page_title'											=> __( 'Install Required Plugins', THEME_SLUG ),
				'menu_title'											=> __( 'Install Plugins', THEME_SLUG ),
				'installing'											=> __( 'Installing Plugin: %s', THEME_SLUG ), // %1$s = plugin name
				'oops'														=> __( 'Something went wrong with the plugin API.', THEME_SLUG ),
				'notice_can_install_required'			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
				'notice_can_install_recommended'	=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_install'						=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
				'notice_can_activate_required'		=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
				'notice_can_activate_recommended'	=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_activate' 		=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
				'notice_ask_to_update' 			=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_update' 			=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
				'install_link' 							=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
				'activate_link' 						=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
				'return'										=> __( 'Return to Required Plugins Installer', THEME_SLUG ),
				'plugin_activated'					=> __( 'Plugin activated successfully.', THEME_SLUG ),
				'complete' 									=> __( 'All plugins installed and activated successfully. %s', THEME_SLUG ), // %1$s = dashboard link
				'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
			)
		);

		tgmpa( $plugins, $config );

	}
?>