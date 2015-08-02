<?php
/**
	ReduxFramework Sample Config File
	For full documentation, please visit: https://github.com/ReduxFramework/ReduxFramework/wiki
 * */

if (!class_exists("Redux_Framework_config")) {

	class Redux_Framework_config {

		public $args = array();
		public $sections = array();
		public $theme;
		public $ReduxFramework;

		public function __construct() {

			if ( !class_exists("ReduxFramework" ) ) {
				return;
			}

			// This is needed. Bah WordPress bugs.  ;)
			if ( defined('TEMPLATEPATH') && strpos( Redux_Helpers::cleanFilePath( __FILE__ ), Redux_Helpers::cleanFilePath( get_template_directory() ) ) !== false) {
				$this->initSettings();
			} else {
				add_action('plugins_loaded', array($this, 'initSettings'), 10);
			}
		}

		public function initSettings() {

			// Just for demo purposes. Not needed per say.
			$this->theme = wp_get_theme();

			// Set the default arguments
			$this->setArguments();

			// Set a few help tabs so you can see how it's done
			//$this->setHelpTabs();

			// Create the sections and fields
			$this->setSections();

			if (!isset($this->args['opt_name'])) { // No errors please
				return;
			}

			// If Redux is running as a plugin, this will remove the demo notice and links
			//add_action( 'redux/loaded', array( $this, 'remove_demo' ) );

			// Function to test the compiler hook and demo CSS output.
			//add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 2);
			// Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
			// Change the arguments after they've been declared, but before the panel is created
			//add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );
			// Change the default value of a field after it's been set, but before it's been useds
			//add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );
			// Dynamically add a section. Can be also used to modify sections/fields
			//add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

			$this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
		}

		/**

			This is a test function that will let you see when the compiler hook occurs.
			It only runs if a field	set with compiler=>true is changed.

		 * */
		function compiler_action($options, $css) {
			//echo "<h1>The compiler hook has run!";
			//print_r($options); //Option values
			//print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )

			/*
				// Demo of how to use the dynamic CSS and write your own static CSS file
				$filename = dirname(__FILE__) . '/style' . '.css';
				global $wp_filesystem;
				if( empty( $wp_filesystem ) ) {
				require_once( ABSPATH .'/wp-admin/includes/file.php' );
				WP_Filesystem();
				}

				if( $wp_filesystem ) {
				$wp_filesystem->put_contents(
				$filename,
				$css,
				FS_CHMOD_FILE // predefined mode settings for WP files
				);
				}
			 */
		}

		function dynamic_section($sections) {
			//$sections = array();
			$sections[] = array(
				'title' => 'Help',
				'desc' => '<iframe src="http://clinico.creaws.com/manual" style="width:100%;height:1600px;"></iframe>',
				'icon' => 'el-icon-book',
				// Leave this as a blank section, no options just some intro text set above.
			);
			return $sections;
		}

		/**

			Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.

		 * */
		function change_arguments($args) {
			//$args['dev_mode'] = true;

			return $args;
		}

		/**

			Filter hook for filtering the default value of any given field. Very useful in development mode.

		 * */
		function change_defaults($defaults) {
			$defaults['str_replace'] = "Testing filter hook!";

			return $defaults;
		}

		// Remove the demo link and the notice of integrated demo from the redux-framework plugin
		/*function remove_demo() {

			// Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
			if (class_exists('ReduxFrameworkPlugin')) {
				remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);

				// Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
				remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));

			}
		}*/

		public function setSections() {

			/**
				Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
			 * */
			// Background Patterns Reader
			$sample_patterns_path = THEME_DIR . '/images/patterns/';
			$sample_patterns_url = THEME_URI . '/images/patterns/';
			$sample_patterns = array();

			if (is_dir($sample_patterns_path)) :

				if ($sample_patterns_dir = opendir($sample_patterns_path)) :
					$sample_patterns = array();

					while (( $sample_patterns_file = readdir($sample_patterns_dir) ) !== false) {

						if (stristr($sample_patterns_file, '.png') !== false || stristr($sample_patterns_file, '.jpg') !== false) {
							$name = explode(".", $sample_patterns_file);
							$name = str_replace('.' . end($name), '', $sample_patterns_file);
							$sample_patterns[] = array('alt' => $name, 'img' => $sample_patterns_url . $sample_patterns_file);
						}
					}
				endif;
			endif;

			ob_start();

			$ct = wp_get_theme();
			$this->theme = $ct;
			$item_name = $this->theme->get('Name');
			$tags = $this->theme->Tags;
			$theme_color = '#237dc8';
			$theme_hover_color = '#0161b3';
			$screenshot = $this->theme->get_screenshot();
			$redux_img = ReduxFramework::$_url . 'assets/img/';
			$class = $screenshot ? 'has-screenshot' : '';

			$customize_title = sprintf(__('Customize &#8220;%s&#8221;', THEME_SLUG), $this->theme->display('Name'));
			?>
			<div id="current-theme" class="<?php echo esc_attr($class); ?>">
			<?php if ($screenshot) : ?>
				<?php if (current_user_can('edit_theme_options')) : ?>
						<a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize" title="<?php echo esc_attr($customize_title); ?>">
							<img src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
						</a>
				<?php endif; ?>
					<img class="hide-if-customize" src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
			<?php endif; ?>

				<h4>
			<?php echo $this->theme->display('Name');	?>
				</h4>

				<div>
					<ul class="theme-info">
						<li><?php printf(__('By %s', THEME_SLUG), $this->theme->display('Author')); ?></li>
						<li><?php printf(__('Version %s', THEME_SLUG), $this->theme->display('Version')); ?></li>
						<li><?php echo '<strong>' . __('Tags', THEME_SLUG) . ':</strong> '; ?><?php printf($this->theme->display('Tags')); ?></li>
					</ul>
					<p class="theme-description"><?php echo $this->theme->display('Description'); ?></p>
				<?php
				if ($this->theme->parent()) {
					printf(' <p class="howto">' . __('This <a href="%1$s">child theme</a> requires its parent theme, %2$s.') . '</p>', __('http://codex.wordpress.org/Child_Themes', THEME_SLUG), $this->theme->parent()->display('Name'));
				}
				?>

				</div>

			</div>

			<?php
			$item_info = ob_get_contents();

			ob_end_clean();

			$sampleHTML = '';
			if (file_exists(dirname(__FILE__) . '/info-html.html')) {
				/** @global WP_Filesystem_Direct $wp_filesystem  */
				global $wp_filesystem;
				if (empty($wp_filesystem)) {
					require_once(ABSPATH . '/wp-admin/includes/file.php');
					WP_Filesystem();
				}
				$sampleHTML = $wp_filesystem->get_contents(dirname(__FILE__) . '/info-html.html');
			}

			// ACTUAL DECLARATION OF SECTIONS


			$this->sections[] = array(
				'title' => __('General Settings', THEME_SLUG),
				'icon_class' => 'icon-large',
				'icon' => 'el-icon-wrench',
				// 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
				'fields' => array(
					array(
						'id' => 'logo',
						'type' => 'media',
						'url'=> true,
						'title' => __('Custom Logo', THEME_SLUG),
						'compiler' => true,
						),
					array(
						'id' => 'logo-dimensions',
						'type' => 'dimensions',
						'units' => false,
						'title' => __('Logo Dimensions', THEME_SLUG),
						),
					array(
						'id' => 'logo-margin',
						'type' => 'spacing',
						'mode' => 'margin',  // absolute, padding, margin, defaults to padding
						'title' => __('Logo Spacing', THEME_SLUG),
						'default' => array('margin-top' => '0', 'margin-right'=>'0', 'margin-bottom' => '0', 'margin-left' => '0' )
						),
					array(
						'id' => 'logo-position',
						'type' => 'image_select',
						'title' => __('Logo Position', THEME_SLUG),
						'options' => array(
										'left' => array('title' => __('Left', THEME_SLUG), 'img' => $redux_img .'align-left.png'),
										'center' => array('title' => __('Center', THEME_SLUG), 'img' => $redux_img .'align-center.png'),
										'right' => array('title' => __('Right', THEME_SLUG), 'img' => $redux_img .'align-right.png')
										),//Must provide key => value(array:title|img) pairs for radio options
						'default' => 'left'
						),
					array(
						'id' => 'favicon',
						'type' => 'media',
						'url'=> true,
						'compiler' => true,
						'title' => __('Custom Favicon', THEME_SLUG),
						'desc'=> __('Upload a 32x32 .png image that will represent your website\'s favicon.', THEME_SLUG),
						'default' => array('url' => THEME_URI . '/favicon.ico'),
					),
					array(
						'id' => 'ga-code',
						'type' => 'textarea',
						'title' => __('Google Analytic Code', THEME_SLUG),
						'desc' => __('Paste your Google Analytics (or other) tracking code here.', THEME_SLUG),
						'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
						'default'	=> '',
						),
					array(
						'id' => 'ga-event-tracking',
						'type' => 'textarea',
						'title' => __('Google Event Tracking Code', THEME_SLUG),
						'desc' => __('Paste your Google Analytics (or other) tracking code here.', THEME_SLUG),
						'validate' => 'html', //see http://codex.wordpress.org/Function_Reference/wp_kses_post
						'default'	=> '',
						),
					array(
						'id' => '_theme_purchase_code',
						'type' => 'text',
						'width' => '250px',
						'title' => __('Theme update', THEME_SLUG),
						'desc' => __('Please insert your Item Purchase Code to get latest theme updates.', THEME_SLUG),
						),
					),
				);

			$this->sections[] = array(
				'title' => __('Display Options', THEME_SLUG),
				'icon_class' => 'icon-large',
				'icon' => 'el-icon-adjust-alt',
				'customizer' => false,
				'fields' => array(
					array(
						'id' => 'menu-position',
						'type' => 'image_select',
						'title' => __('Menu Position', THEME_SLUG),
						'options' => array(
							'left' => array('title' => __('Left', THEME_SLUG), 'img' => $redux_img .'align-left.png'),
							'center' => array('title' => __('Center', THEME_SLUG), 'img' => $redux_img .'align-center.png'),
							'right' => array('title' => __('Right', THEME_SLUG), 'img' => $redux_img .'align-right.png')
							),//Must provide key => value(array:title|img) pairs for radio options
						'default' => 'left'
						),
					array(
						'id' => 'menu-stick',
						'type' => 'checkbox',
						'title' => __('Use sticky menu', THEME_SLUG),
						'default' => '1'// 1 = on | 0 = off
						),
					array(
						'id' => 'breadcrumbs',
						'type' => 'checkbox',
						'title' => __('Show breadcrumbs', THEME_SLUG),
						'default' => '1'// 1 = on | 0 = off
						),
					array(
						'id' => 'modernizr',
						'type' => 'checkbox',
						'title' => __('Enable mobile menu on all touch-enabled devices', THEME_SLUG),
						'default' => '1'// 1 = on | 0 = off
						),
					array(
						'id' => 'blog_author',
						'type' => 'checkbox',
						'title' => __('Show post author', THEME_SLUG),
						'default' => '1'// 1 = on | 0 = off
						),
					array(
						'id' => 'show_comments',
						'type' => 'checkbox',
						'width' => '250px',
						'title' => __('Show comments on:', THEME_SLUG),
						'options'   => array(
							'pages' => 'Pages',
							'posts' => 'Posts',
						),
						'default'   => array(
							'pages' => '1',
							'posts' => '1',
						),
					),
					array(
						'id' => 'tech-category',
						'type' => 'select',
						'width' => '250px',
						'data' => 'categories',
						'title' => __('Technical category', THEME_SLUG),
						'desc' => __('The posts located in this category and subcategories are excluded from being displayed within the Homepage and Blog Lists.', THEME_SLUG),
						'multi' => true,
						'default' => array()
						),
					),
				);

			$this->sections[] = array(
				'title' => __('Styling Options', THEME_SLUG),
				'icon_class' => 'icon-large',
				'icon' => 'el-icon-brush',
				// 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
				'fields' => array(
					array(
						'id' => 'boxed-layout',
						'type' => 'switch',
						'title' => __('Enable Boxed Layout', THEME_SLUG),
						"default" => 0,
					),
					array(
						'id' => 'stylesheet',
						'type' => 'select',
						'width' => '250px',
						'title' => __('Theme Stylesheet', THEME_SLUG),
						'desc' => __('Select your current color scheme.', THEME_SLUG),
						'options' => array(
							'color-1' => 'Blue',
							'color-2' => 'Azure',
							'color-3' => 'Green',
							'color-4' => 'Orange',
							'color-5' => 'Red',
							'color-6' => 'Violet',
							),
						'default' => 'color-1',
					),
					array(
						'id' => 'is-custom-color',
						'type' => 'switch',
						'title' => __('Use customized theme colors', THEME_SLUG),
						'default' => '0',
						'on' => 'On',
						'off' => 'Off',
					),
					array(
						'id'        => 'theme-custom-color',
						'type'      => 'color',
						'title'     => __('Theme Custom Color', THEME_SLUG),
						'required' => array('is-custom-color', '=', '1'),
						'transparent' => false,
						'default'   => $theme_color,
						'validate'  => 'color',
						'customizer' => false,
						),
					array(
						'id'        => 'theme-custom-hover-color',
						'type'      => 'color',
						'transparent' => false,
						'required' => array('is-custom-color', '=', '1'),
						'title'     => __('Theme Custom Hover Color', THEME_SLUG),
						'default'   => $theme_hover_color,
						'validate'  => 'color',
						'customizer' => false,
						),
					array(
						'id' => 'url_background',
						'type' => 'url',
						'title' => __('Background Settings', THEME_SLUG),
						'url_text' => __('Click this link to customize your background settings', THEME_SLUG),
						//'urlhint' => __('This URL will be opened in a new window, please don\'t forget to save your current settings!', THEME_SLUG),
						'href' => 'themes.php?page=custom-background',
					),
					)
				);

			$this->sections[] = array(
				'title' => __('Layout Options', THEME_SLUG),
				'icon_class' => 'icon-large',
				'icon' => 'el-icon-magic',
				'customizer' => false,
				'fields' => array(
					array(
						'id' => 'def-page-layout',
						'type' => 'image_select',
						'compiler' => true,
						'title' => __('Default Page Sidebar Position', THEME_SLUG),
						'desc' => __('You can override these settings on each page individually.', THEME_SLUG),
						'options' => array(
								'none' => array('alt' => __('No Sidebar', THEME_SLUG), 'img' => $redux_img . 'none.png'),
								'left' => array('alt' => __('Left Sidebar', THEME_SLUG), 'img' => $redux_img . 'left.png'),
								'right' => array('alt' => __('Right Sidebar', THEME_SLUG), 'img' => $redux_img . 'right.png'),
								'both' => array('alt' => __('Double Sidebars', THEME_SLUG), 'img' => $redux_img . 'both.png'),
						),
						'default' => 'none'
					),
					array(
						'id' => 'def-page-sidebar1',
						'type' => 'select',
						'width' => '250px',
						'data' => 'sidebars',
						'required' => array('def-page-layout', '!=', 'none'),
						'title' => __('Assign Page Sidebar', THEME_SLUG),
						'desc' => __('You can override these setting on each page individually.', THEME_SLUG),
						),
					array(
						'id' => 'def-page-sidebar2',
						'type' => 'select',
						'width' => '250px',
						'data' => 'sidebars',
						'required' => array('def-page-layout', '=', 'both'),
						'title' => __('Assign Page Left Sidebar', THEME_SLUG),
						'desc' => __('You can override these setting on each page individually.', THEME_SLUG),
						),
					array(
						'id' => 'def-blog-layout',
						'type' => 'image_select',
						'compiler' => true,
						'title' => __('Default Blog Sidebar Position', THEME_SLUG),
						'desc' => __('You can override this settings on each page individually.', THEME_SLUG),
						'options' => array(
							'none' => array('alt' => __('No Sidebar', THEME_SLUG), 'img' => $redux_img . 'none.png'),
								'left' => array('alt' => __('Left Sidebar', THEME_SLUG), 'img' => $redux_img . 'left.png'),
								'right' => array('alt' => __('Right Sidebar', THEME_SLUG), 'img' => $redux_img . 'right.png'),
								'both' => array('alt' => __('Double Sidebars', THEME_SLUG), 'img' => $redux_img . 'both.png'),
						),
						'default' => 'none'
					),
					array(
						'id' => 'def-blog-sidebar1',
						'type' => 'select',
						'width' => '250px',
						'required' => array('def-blog-layout', '!=', 'none'),
						'data' => 'sidebars',
						'title' => __('Assign Blog Sidebar', THEME_SLUG),
						'desc' => __('You can override these setting on each page individually.', THEME_SLUG),
						),
					array(
						'id' => 'def-blog-sidebar2',
						'type' => 'select',
						'width' => '250px',
						'required' => array('def-blog-layout', '=', 'both'),
						'data' => 'sidebars',
						'title' => __('Assign Blog Left Sidebar', THEME_SLUG),
						'desc' => __('You can override these setting on each page individually.', THEME_SLUG),
						),
					array(
						'id' => 'footer-sidebar-top',
						'type' => 'select',
						'width' => '250px',
						'data' => 'sidebars',
						'title' => __('Select Footer\'s sidebar area', THEME_SLUG),
						'desc' => __('This options will set the default Footer widget area, unless you override it on each page.', THEME_SLUG),
						),
					array(
						'id' => 'footer-sidebar-bottom',
						'type' => 'select',
						'width' => '250px',
						'data' => 'sidebars',
						'title' => __('Select Footer\'s copyrights sidebar area', THEME_SLUG),
						'desc' => __('This options will set the default Footer Copyrights widget area, unless you override it on each page.', THEME_SLUG),
						),
					array(
						'id' => 'toggle-sidebar',
						'type' => 'select',
						'width' => '250px',
						'data' => 'sidebars',
						'title' => __('Select Header\'s toggle sidebar area', THEME_SLUG),
						'desc' => __('This options will set the header\'s widget area, which can be toggled.', THEME_SLUG),
						),
					array(
						'id' => 'toggle-sidebar-title',
						'type' => 'text',
						'width' => '250px',
						'title' => __('Enter toggle sidebar area\'s title', THEME_SLUG),
						'desc' => __('This option determines the title of toggle sidebar area', THEME_SLUG),
						'default' => 'Find a Doctor'
						),
					array(
						'id'=>'sidebars',
						'type' => 'multi_text',
						'title' => __('Sidebar Generator', THEME_SLUG),
						'desc' => __('Please note: dot\'t forget to remove all the <a href="widgets.php">widgets</a> within the sidebar before deleting!', THEME_SLUG),
						'options' => array('Main Sidebar', 'Footer Sidebar'),
						)
					),
				);

			$this->sections[] = array(
				'title' => __('Typography', THEME_SLUG),
				'icon_class' => 'icon-large',
				'icon' => 'el-icon-font',
				// 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
				'fields' => array(
						array(
							'id' => 'menu-font',
							'type' => 'typography',
							'title' => __('Main Menu Font Settings', THEME_SLUG),
							'google' => true,
							'color' => true,
							'line-height' => true,
							'font-size' => true,
							'font-backup' => true,
							'default' => array(
								'font-size' => '15px',
								'color' => '#000',
								'line-height' => '15px',
								'google' => true,
								'font-family' => 'Lato',
								'font-weight' => '400',
							),
						),
						array(
							'id' => 'header-font',
							'type' => 'typography',
							'title' => __('Headers Font Settings', THEME_SLUG),
							'google' => true,
							'font-backup' => true,
							'font-size' => true,
							'line-height' => true,
							'color' => true,
							'word-spacing' => false,
							'letter-spacing' => false,
							'text-transform' => true,
							'default' => array(
								'font-size' => '26px',
								'line-height' => '30px',
								'color' => '#373737',
								'google' => true,
								'font-family' => 'Lato',
								'font-weight' => '400',
							),
						),
						array(
							'id' => 'body-font',
							'type' => 'typography',
							'title' => __('General Text Font Settings', THEME_SLUG),
							'google' => true,
							'font-backup' => true,
							'font-size' => true,
							'line-height' => true,
							'color' => true,
							'word-spacing' => true,
							'letter-spacing' => true,
							'default' => array(
								'color' => THEME_CSS_BODY_COLOR,
								'font-size' => '14px',
								'line-height' => '21px',
								'google' => true,
								'font-family' => 'Lato',
								'font-weight' => '400',
								'word-spacing' => '0',
								'letter-spacing' => '0',
							),
						)
					)
				);

			$this->sections[] = array(
				'title' => __('HomePage', THEME_SLUG),
				'icon_class' => 'icon-large',
				'icon' => 'el-icon-home',
				'customizer' => false,
				'fields' => array(
					array(
						'id' => 'home-slider-type',
						'type' => 'radio',
						'title' => __('HomePage Slider', THEME_SLUG),
						'options' => array('none' => 'None', 'img-slider' => 'Image Slider', 'video-slider' => 'Video', 'stat-img-slider' => 'Static Image'), //Must provide key => value pairs for radio options
						'default' => 'img-slider',
					),
					array(
						'id' => 'home-header-slider',
						'type' => 'text',
						'required' => array('home-slider-type', '=', 'img-slider'),
						'title' => __('Slider shortcode', THEME_SLUG),
						'default' => '[rev_slider main_slider_wide]',
					),
					array(
						'id' => 'home-header-video',
						'type' => 'text',
						'required' => array('home-slider-type', '=', 'video-slider'),
						'title' => __('Video URL', THEME_SLUG),
						'desc' => __('Insert Vimeo or Youtube resource URL', THEME_SLUG),
						'validate' => 'url',
						'default' => ''
						),
					array(
						'id' => 'home-header-image',
						'type' => 'media',
						'required' => array('home-slider-type', '=', 'stat-img-slider'),
						'url'=> true,
						'readonly' => false,
						'title' => __('Static Image', THEME_SLUG),
						'compiler' => 'true',
						),
					array(
						'id' => 'home-slider-video-autoplay',
						'type' => 'switch',
						'required' => array('home-slider-type', '=', 'vidio-slider', 'home-slider-switch', '=', '1'),
						'title' => __('AutoPlay Video', THEME_SLUG),
						"default" => 0,
						),
					array(
						'id' => 'def-home-layout',
						'type' => 'image_select',
						'compiler' => true,
						'title' => __('Default HomePage Sidebar', THEME_SLUG),
						'options' => array(
							'none' => array('alt' => __('No Sidebar', THEME_SLUG), 'img' => $redux_img . 'none.png'),
							'left' => array('alt' => __('Left Sidebar', THEME_SLUG), 'img' => $redux_img . 'left.png'),
							'right' => array('alt' => __('Right Sidebar', THEME_SLUG), 'img' => $redux_img . 'right.png'),
							'both' => array('alt' => __('Double Sidebars', THEME_SLUG), 'img' => $redux_img . 'both.png'),
						),
						'default' => 'none'
					),
					array(
						'id' => 'def-home-sidebar1',
						'type' => 'select',
						'required' => array('def-home-layout', '!=', 'none'),
						'data' => 'sidebars',
						'title' => __('Assign HomePage Sidebar', THEME_SLUG),
						'width' => '250px',
						),
					array(
						'id' => 'def-home-sidebar2',
						'type' => 'select',
						'required' => array('def-home-layout', '=', 'both'),
						'data' => 'sidebars',
						'title' => __('Assign HomePage Left Sidebar', THEME_SLUG),
						'width' => '250px',
						),
					array(
						'id' => 'def-home-category',
						'type' => 'select',
						'data' => 'categories',
						'title' => __('Blog Posts Category', THEME_SLUG),
						'width' => '250px',
						'multi' => true,
						'default' => array()
						),
					array(
						'id' => 'benefits-sidebar',
						'type' => 'select',
						'data' => 'sidebar',
						'title' => __('Benefits Widgetized Area', THEME_SLUG),
						'desc' => __('Show widgets of this sidebar under the slider on the Home Page.', THEME_SLUG),
						'width' => '250px',
						),

					)
				);
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )  {
			$this->sections[] = array(
				'title' => __('WooCommerce', THEME_SLUG),
				'icon_class' => 'icon-large',
				'icon' => ' el-icon-shopping-cart',
				'customizer' => false,
				'fields' => array(
					array(
						'id' => 'def-woo-layout',
						'type' => 'image_select',
						'compiler' => true,
						'title' => __('Default WooCommerce Sidebar', THEME_SLUG),
						'options' => array(
							'none' => array('alt' => __('No Sidebar', THEME_SLUG), 'img' => $redux_img . 'none.png'),
							'left' => array('alt' => __('Left Sidebar', THEME_SLUG), 'img' => $redux_img . 'left.png'),
							'right' => array('alt' => __('Right Sidebar', THEME_SLUG), 'img' => $redux_img . 'right.png'),
						),
						'default' => 'none'
					),
					array(
						'id' => 'def-woo-sidebar',
						'type' => 'select',
						'width' => '250px',
						'data' => 'sidebars',
						'required' => array('def-woo-layout', '!=', 'none'),
						'title' => __('WooCommerce Sidebar', THEME_SLUG),
						),
					array(
						'id' => 'woo-num-products',
						'type' => 'spinner',
						'title' => __('Products per page', THEME_SLUG),
						"default" => get_option('posts_per_page'),
						"min" => "1",
						"step" => "1",
						"max" => "200",
						),
					),
				);
		}
		$this->sections[] = array(
			'icon' => 'el-icon-twitter',
			'icon_class' => 'icon-large',
			'title' => __('Social Options', THEME_SLUG),
			'customizer' => false,
			'fields' => array(
				array(
					'id' => 'turn-twitter',
					'type' => 'switch',
					'title' => __('Enable Twitter Feed', THEME_SLUG),
					'default' => '0',
					'on' => 'On',
					'off' => 'Off',
				),
				array(
					'id' => 'tw-api-name',
					'type' => 'text',
					'required' => array('turn-twitter', '=', '1'),
					'title' => __('Twitter username', THEME_SLUG),
					'default' => '',
				),
				array(
					'id' => 'tw-api-key',
					'type' => 'text',
					'required' => array('turn-twitter', '=', '1'),
					'title' => __('Twitter API key', THEME_SLUG),
					'default' => '',
				),
				array(
					'id' => 'tw-api-secret',
					'type' => 'text',
					'required' => array('turn-twitter', '=', '1'),
					'title' => __('Twitter API secret', THEME_SLUG),
					'default' => '',
				),
				array(
					'id' => 'tw-access-token',
					'type' => 'text',
					'required' => array('turn-twitter', '=', '1'),
					'title' => __('Twitter Access token', THEME_SLUG),
					'default' => '',
				),
				array(
					'id' => 'tw-access-token-secret',
					'type' => 'text',
					'required' => array('turn-twitter', '=', '1'),
					'title' => __('Twitter Access token secret', THEME_SLUG),
					'default' => '',
				),
				array (
				'id'=>"social-group",
				'type' => 'group',//doesn't need to be called for callback fields
				'title' => __('Social networks', THEME_SLUG),
				'subtitle' => __('Add any social network you like.', THEME_SLUG),
				'groupname' => __('Social networks', THEME_SLUG), // Group name
				'fields' =>
					array(
						array(
							'id' => 'title',
							'type' => 'text',
							'title' => __('Social account title', THEME_SLUG),
							'subtitle' => __('For example: My office twitter', THEME_SLUG),
							'validate' => 'no_html',
							),
						array(
							'id'=>'soc-select-fa',
							'type' => 'select',
							'data' => 'fa-icons',
							'title' => __('Select the icon for this social contact', THEME_SLUG),
							'desc' => __('Here\'s a list of all the social icons by name and icon.', THEME_SLUG),
							),
						array(
							'id' => 'soc-url',
							'type' => 'text',
							'title' => __('Url to your account', THEME_SLUG),
							'subtitle' => __('For example: http://twitter.com/john.doe', THEME_SLUG),
							'validate' => 'url',
							),
						),
					),
				),
			);
			$this->sections[] = array(
				'title' => __('Help', THEME_SLUG),
				'header' => __('Welcome to the Simple Options Framework Demo', THEME_SLUG),
				'icon_class' => 'icon-large',
				'icon' => 'el-icon-book',
				'fields' => array(
					array(
						'id' => 'help_online',
						'type' => 'raw',
						'align' => true,
						'markdown' => true,
						'content' => '<a class="button" href="http://clinico.creaws.com/manual/" target="_blank"><i class="fa fa-life-bouy"></i>&nbsp;&nbsp;Help online</a>&nbsp;<a class="button" href="https://www.youtube.com/user/cwsvideotuts" target="_blank"><i class="fa fa-video-camera"></i>&nbsp;&nbsp;Help video</a>'
						)
					),
				);

			$theme_info = '<div class="redux-framework-section-desc">';
			$theme_info .= '<p class="redux-framework-theme-data description theme-uri">' . __('<strong>Theme URL:</strong> ', 'redux-framework') . '<a href="' . $this->theme->get('ThemeURI') . '" target="_blank">' . $this->theme->get('ThemeURI') . '</a></p>';
			$theme_info .= '<p class="redux-framework-theme-data description theme-author">' . __('<strong>Author:</strong> ', 'redux-framework') . $this->theme->get('Author') . '</p>';
			$theme_info .= '<p class="redux-framework-theme-data description theme-version">' . __('<strong>Version:</strong> ', 'redux-framework') . $this->theme->get('Version') . '</p>';
			$theme_info .= '<p class="redux-framework-theme-data description theme-description">' . $this->theme->get('Description') . '</p>';
			$tabs = $this->theme->get('Tags');
			if (!empty($tabs)) {
				$theme_info .= '<p class="redux-framework-theme-data description theme-tags">' . __('<strong>Tags:</strong> ', 'redux-framework') . implode(', ', $tabs) . '</p>';
			}
			$theme_info .= '</div>';
		}

		public function setHelpTabs() {

		}

		/**

			All the possible arguments for Redux.
			For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

		 * */
		public function setArguments() {

			$theme = wp_get_theme(); // For use with some settings. Not necessary.

			$this->args = array(
				// TYPICAL -> Change these values as you need/desire
				'opt_name' => $theme->get( 'TextDomain' ), // This is where your data is stored in the database and also becomes your global variable name.
				'display_name' => $theme->get('Name'), // Name that appears at the top of your panel
				'display_version' => $theme->get('Version'), // Version that appears at the top of your panel
				'display_description' => $theme->get('Description'),
				'menu_type' => 'menu', //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
				'allow_sub_menu' => true, // Show the sections below the admin menu item or not
				'menu_title' => __('Theme Options', THEME_SLUG),
				'page' => __('Theme Options', THEME_SLUG),
				// You will need to generate a Google API key to use this feature.
				// Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
				'google_api_key' => 'AIzaSyAX_2L_UzCDPEnAHTG7zhESRVpMPS4ssII', // Must be defined to add google fonts to the typography module
				//'admin_bar' => false, // Show the panel pages on the admin bar
				'global_variable' => '', // Set a different name for your global variable other than the opt_name
				'dev_mode' => true, // Show the time the page took to load, etc
				'customizer' => true, // Enable basic customizer support
				// OPTIONAL -> Give you extra features
				'page_priority' => null, // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
				'page_parent' => 'themes.php', // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
				'page_permissions' => 'manage_options', // Permissions needed to access the options panel.
				'menu_icon' => '', // Specify a custom URL to an icon
				'last_tab' => '', // Force your panel to always open to a specific tab (by id)
				'page_icon' => 'icon-themes', // Icon displayed in the admin panel next to your menu_title
				'page_slug' => '_options', // Page slug used to denote the panel
				'save_defaults' => true, // On load save the defaults to DB before user clicks save or not
				'default_show' => false, // If true, shows the default value next to each field that is not the default value.
				'default_mark' => '', // What to print by the field's title if the value shown is default. Suggested: *
				// CAREFUL -> These options are for advanced use only
				'transient_time' => 60 * MINUTE_IN_SECONDS,
				'output' => true, // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
				'output_tag' => true, // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
				//'domain'             	=> 'redux-framework', // Translation domain key. Don't change this unless you want to retranslate all of Redux.
				//'footer_credit'      	=> '', // Disable the footer credit of Redux. Please leave if you can help it.
				// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
				'database' => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
				'show_import_export' => true, // REMOVE
				'system_info' => false, // REMOVE
				'help_tabs' => array(),
				'help_sidebar' => '', // __( '', $this->args['domain'] );
			);


			// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
			$this->args['share_icons'][] = array(
				'url' => 'https://twitter.com/Creative_WS',
				'title' => 'Follow us on Twitter',
				'icon' => 'el-icon-twitter'
			);
			$this->args['share_icons'][] = array(
				'url' => 'https://www.facebook.com/CreaWS',
				'title' => 'Like us on Facebook',
				'icon' => 'el-icon-facebook'
			);
			$this->args['share_icons'][] = array(
				'url' => 'http://www.behance.net/CreativeWS',
				'title' => 'Find us on Behance',
				'icon' => 'el-icon-behance'
			);

			// Panel Intro text -> before the form
			if (!isset($this->args['global_variable']) || $this->args['global_variable'] !== false) {
				if (!empty($this->args['global_variable'])) {
					$v = $this->args['global_variable'];
				} else {
					$v = str_replace("-", "_", $this->args['opt_name']);
				}
				$this->args['intro_text'] = sprintf(__('<p>Did you know that Redux sets a global variable for you? To access any of your saved options from within your code you can use your global variable: <strong>$%1$s</strong></p>', THEME_SLUG), $v);
			} else {
				$this->args['intro_text'] = __('<p>This text is displayed above the options panel. It isn\'t required, but more info is always better! The intro_text field accepts all HTML.</p>', THEME_SLUG);
			}

			// Add content after the form.
			//$this->args['footer_text'] = __('<p>This text is displayed below the options panel. It isn\'t required, but more info is always better! The footer_text field accepts all HTML.</p>', THEME_SLUG);
		}

	}

	new Redux_Framework_config();
}
