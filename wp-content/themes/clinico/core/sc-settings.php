<?php
global $cws_shortcode_attr;
$price_categories = array();
$redux_img = ReduxFramework::$_url . 'assets/img/';
$cws_shortcode_attr = array(
	'quote'=>array(
		'options'=>array(
			array(
				'id' => 'photo',
				'title' => __('Image', THEME_SLUG),
				'type' => 'media_library'
			),
			array(
				'id' => 'text',
				'title' => __('Quote text',THEME_SLUG),
				'type' => 'textarea',
				'w' => '75%',
				'rows' => '3'
			),
			array(
				'id' => 'author',
				'title' => __('Author',THEME_SLUG),
				'desc' => '',
				'type' => 'text',
				'w' => '75%'
			),
		)
	),
	'cws_cta'=>array(
		'options'=>array(
			array(
				'id' => 'fa',
				'title' => __('Icon',THEME_SLUG),
				'desc' => __('Icon', THEME_SLUG),
				'type' => 'select',
				'default' => '',
				'w' => '50%'
			),
			array(
				'id' => 'title',
				'title' => __('Title',THEME_SLUG),
				'type' => 'text',
				'w' => '50%'
			),
			array(
				'id' => 'text',
				'title' => __('Text',THEME_SLUG),
				'type' => 'textarea',
				'rows' => '5',
				'w' => '50%'
			),
			array(
				'id' => 'button_text',
				'title' => __('Button',THEME_SLUG),
				'type' => 'text',
				'w' => '50%'
			),
			array(
				'title' => __('Button URL',THEME_SLUG),
				'id' => 'link',
				'type' => 'text',
				'w' => '50%'
			)
		)
	),
	'embed' => array(
		'options' => array(
			array(
				'id' => 'url',
				'title' => __('Video/Audio url',THEME_SLUG),
				'type' => 'textarea',
				'rows' => '2'
			),
			array(
				'id' => 'width',
				'title' => __('Width',THEME_SLUG),
				'type' => 'text',
				'w' => '25%'
			),
			array(
				'id' => 'height',
				'title' => __('Height',THEME_SLUG),
				'type' => 'text',
				'w' => '25%'
			),
		)
	),
	'progress' => array(
		'options' => array(
			array(
				'id' => 'title',
				'title' => __('Title',THEME_SLUG),
				'type' => 'text',
				'w' => '50%'
			),
			array(
				'id' => 'progress',
				'title' => __('Percentage',THEME_SLUG),
				'desc' => __('From 0 to 100',THEME_SLUG),
				'type' => 'range',
				'min' => '0',
				'max' => '100',
				'step' => '1',
				'w' => '50%'
			),
			array(
				'id' => 'color',
				'title' => __('Color', THEME_SLUG),
				'type' => 'color',
				'default' => '',
				'w' => '50%'
			)
		)
	),
	'milestone' => array(
		'options' => array(
			array(
				'id' => 'fa',
				'title' => __('Icon',THEME_SLUG),
				'desc' => __('Icon', THEME_SLUG),
				'type' => 'select',
				'default' => '',
				'w' => '50%'
			),
			array(
				'id' => 'number',
				'title' => __('Number',THEME_SLUG),
				'type' => 'text',
				'w' => '50%'
			),
			array(
				'id' => 'text',
				'title' => __('Text',THEME_SLUG),
				'type' => 'textarea',
				'rows' => 3,
				'w' => '50%'
			),
		)
	),
	'alert' => array(
		'options' => array(
			array(
				'id' => 'type',
				'title' => __('Type',THEME_SLUG),
				'desc' => __('Info Box Type',THEME_SLUG),
				'type' => 'select',
				'source' => array(
					'information' => __('Information',THEME_SLUG),
					'warning' => __('Warning',THEME_SLUG),
					'success' => __('Success',THEME_SLUG),
					'error' => __('Error',THEME_SLUG),
					),
				'default' => '1',
				'w' => '50%'
			),
			array(
				'id' => 'grey_skin',
				'title' => __('Grey background',THEME_SLUG),
				'type' => 'check',
				'source' => array( '' => '' ),
				'default' => array(false),
				'w' => '50%'
			),
			array(
				'id' => 'title',
				'title' => __('Title',THEME_SLUG),
				'type' => 'text',
				'w' => '50%'
			),
			array(
				'id' => 'text',
				'title' => __('Text',THEME_SLUG),
				'type' => 'textarea',
				'rows' => '3',
				'w' => '50%'
			),
		)
	),
	'cws_button' => array(
		'options' => array(
			array('id' => 'type',
				  'desc' => __('Button type',THEME_SLUG),
				  'type' => 'select',
				  'source' => array(
				   		'default' => __('Default', THEME_SLUG),
				   		'rounded' => __('Rounded', THEME_SLUG),
				   		'arrow' => __('With Arrow', THEME_SLUG)
				   		),
				  'default' => 'default',
				  'w' => '50%'),
			array('id' => 'size',
				  'desc' =>__('Button size',THEME_SLUG),
				  'type' => 'select',
				  'source' => array(
				  		'large' => __('Large', THEME_SLUG),
				  		'medium' => __('Medium', THEME_SLUG),
				  		'small' => __('Small', THEME_SLUG),
				  		'mini' => __('Mini', THEME_SLUG)
				  ),
				  'default' => 'medium',
				  'w' => '50%'),
			array('id' => 'text',
				  'desc' =>__('Text',THEME_SLUG),
				  'type' => 'text',
				  'default' => '',
				  'w' => '50%'),
			array('id' => 'link',
				  'desc' =>__('URL',THEME_SLUG),
				  'type' => 'text',
				  'default' => '#',
				  'w' => '50%'),
			array('id' => 'custom_color',
				  'desc' => __('Customize button', THEME_SLUG),
				  'type' => 'check',
				  'source' => array( '' => '' ),
				  'default' => array(false),
				  'w' => '50%',
				  'hide'=>array('button_color','text_color','border_color')
				  ),
			array('id' => 'button_color',
				  'desc' => __('Button Color',THEME_SLUG),
				  'type' => 'color',
				  'default' => '#fff',
				  'w' => '20%',
				  'hidden' => true
				  ),
			array('id' => 'border_color',
				  'desc' => __('Border Color',THEME_SLUG),
				  'type' => 'color',
				  'default' => '#008fd5',
				  'w' => '20%',
				  'hidden' => true
				  ),
			array('id' => 'text_color',
				  'desc' => __('Text Color',THEME_SLUG),
				  'type' => 'color',
				  'default' => '#008fd5',
				  'w' => '20%',
				  'hidden' => true
				)
		)
	),
	'tweets' => array(
		'options' => array(
			array(
				'id' => 'title',
				'title' => __('Title',THEME_SLUG),
				'type' => 'text',
				'w' => '50%',
			),
			array(
				'id' => 'num',
				'title' => __('Number of tweets',THEME_SLUG),
				'type' => 'text',
				'default' => '4',
				'w' => '50%',
			),
			array(
				'id' => 'num_vis',
				'title' => __('Tweets per slide',THEME_SLUG),
				'type' => 'text',
				'default' => '2',
				'w' => '50%',
			),
		)
	),
	'fa' => array(
		'options' => array(
			array(
				'id' => 'fa',
				'title' => __('Icon',THEME_SLUG),
				'desc' => __('Icon',THEME_SLUG),
				'type' => 'select',
				'w' => '50%'
			),
			array(
				'id' => 'size',
				'title' => __('Size', THEME_SLUG),
				'desc' => __('Icon Size', THEME_SLUG),
				'type' => 'select',
				'source' => array(
					'lg' => '1x',
					'2x' => '2x',
					'3x' => '3x',
					'4x' => '4x',
					'5x' => '5x'
					),
				'default' => '2x',
				'w' => '50%',
			),
			array(
				'id' => 'custom_color',
				'title' => __('Customize',THEME_SLUG),
				'type' => 'check',
				'source' => array(
					'' => '',
					),
				'default' => array(false),
				'hide' => array('color','bg_color','border_color')
			),
			array(
				'id' => 'color',
				'title' => __('Text color',THEME_SLUG),
				'type' => 'color',
				'default' => '#008fd5',
				'w' => '20%',
				'hidden' => true
			),
			array(
				'id' => 'bg_color',
				'title' => __('Background color',THEME_SLUG),
				'type' => 'color',
				'default' => '#fff',
				'w' => '20%',
				'hidden' => true
			),
			array(
				'id' => 'border_color',
				'title' => __('Border color',THEME_SLUG),
				'type' => 'color',
				'default' => '#008fd5',
				'w' => '20%',
				'hidden' => true
			),
		)
	),
	'featured_fa' => array(
		'options' => array(
			array(
				'id' => 'fa',
				'title' => __('Icon',THEME_SLUG),
				'desc' => __('Icon',THEME_SLUG),
				'type' => 'select',
				'w' => '50%'
			),
			array(
				'id' => 'size',
				'title' => __('Icon Size', THEME_SLUG),
				'desc' => __('Icon Size', THEME_SLUG),
				'type' => 'select',
				'source' => array(
					'lg' => '1x',
					'2x' => '2x',
					'3x' => '3x',
					'4x' => '4x',
					'5x' => '5x'
					),
				'default' => '2x',
				'w' => '50%'
			),
			array(
				'id' => 'type',
				'title' => __('Icon Type', THEME_SLUG),
				'type' => 'radio',
				'source' => array(
					'icon_frame' => __('Large', THEME_SLUG),
					'pointer' => __('Small', THEME_SLUG),
					),
				'default' => 'icon_frame'
			),
			array(
				'id' => 'float',
				'title' => __('Float',THEME_SLUG),
				'type' => 'radio',
				'source' => array(
					'none' => __('None',THEME_SLUG),
					'left' => __('Left',THEME_SLUG),
					'right' => __('Right',THEME_SLUG),
					),
				'default' => 'none',
			),
			array(
				'id' => 'custom_color',
				'title' => __('Customize',THEME_SLUG),
				'type' => 'check',
				'source' => array(
					'' => '',
					),
				'default' => array(false),
				'hide' => array('color','bg_color','border_color')
			),
			array(
				'id' => 'color',
				'title' => __('Icon color',THEME_SLUG),
				'type' => 'color',
				'default' => '#fff',
				'w' => '20%',
				'hidden' => true
			),
			array(
				'id' => 'bg_color',
				'title' => __('Background color',THEME_SLUG), 
				'type' => 'color',
				'default' => '#008fd5',
				'w' => '20%',
				'hidden' => true
			),
		)
	),
	'mark' => array(
		'options' => array(
			array(
				'id' => 'color',
				'title' => __('Text color',THEME_SLUG),
				'type' => 'color',
				'default' => '#fff',
				'w' => '25%'
			),
			array(
				'id' => 'bgcolor',
				'title' => __('Text background',THEME_SLUG),
				'type' => 'color',
				'default' => '#4db1e2', // this should be adjusted to the theme's current color scheme
				'w' => '25%'
			),
		)
	),
	'price-table' => array(
		'options' => array(
			array(
				'id' => 'cat',
				'title' => __('Category',THEME_SLUG),
				'desc' => __('Posts category',THEME_SLUG),
				'type' => 'select',
				'source' => 'price_categories',
				'w' => '50%'
			),
			array(
				'id' => 'order',
				'title' => __('Order',THEME_SLUG),
				'type' => 'radio',
				'source' => array(
					'ASC' => __('Acsending',THEME_SLUG),
					'DESC' => __('Descending',THEME_SLUG),
					),
				'default' => 'DESC',
			),
			array(
				'id' => 'orderby',
				'title' => __('Order by',THEME_SLUG),
				'desc' => __('Order by',THEME_SLUG),
				'type' => 'select',
				'source' => array(
					'none' => __('No order',THEME_SLUG),
					'ID' => __('Post ID',THEME_SLUG),
					'author' => __('Author',THEME_SLUG),
					'title' => __('Title',THEME_SLUG),
					'date' => __('Date',THEME_SLUG),
					'modified' => __('Last modified date',THEME_SLUG),
					),
				'default' => 'modified',
				'w' => '50%'
			),
			array(
				'id' => 'posts',
				'title' => __('Posts',THEME_SLUG),
				'desc' => __('Posts to show',THEME_SLUG),
				'type' => 'select',
				'source' => array(
					'-1' => __('All',THEME_SLUG),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					),
				'default' => '-1',
				'w' => '50%'
			),
			array(
				'id' => 'columns',
				'title' => __('Columns',THEME_SLUG),
				'desc' => __('Columns per row',THEME_SLUG),
				'type' => 'select',
				'source' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4'
					),
				'default' => '2',
				'w' => '50%'
			),
		)
	),
	'ourteam' => array(
		'options' => array(
			array(
				'id' => 'title',
				'title' => __('Title',THEME_SLUG),
				'type' => 'text',
				'default' => '',
				'w' => '50%'
			),
			array(
				'id' => 'mode',
				'title' => __('Display as',THEME_SLUG),
				'type' => 'radio',
				'source' => array(
					'all' => __('Grid',THEME_SLUG),
					'carousel' => __('Carousel',THEME_SLUG),
					),
				'default' => 'all',
			),
			array(
				'id' => 'cat',
				'title' => __('Departments',THEME_SLUG),
				'desc' => __('Departments',THEME_SLUG),
				'type' => 'select',
				'select' => 'multiple',
				'source' => 'category_cws-staff-dept',
				'w' => '50%'
			),
			array(
				'id' => 'filtering',
				'title' => __('Use staff filter',THEME_SLUG),
				'type' => 'check',
				'source' => array( '' => '' ),
				'default' => array(true),
			),			
		)
	),
	'services' => array(
		'options' => array(
			array(
				'id' => 'dept',
				'title' => __('Select departments',THEME_SLUG),
				'desc' => __('Departments',THEME_SLUG),
				'type' => 'select',
				'select' => 'multiple',
				'source' => 'category_cws-staff-dept',
				'source_args' => 'parent=0',
				'w' => '100%'
			),
			array(
				'id' => 'deptopen', 
				'title' => __('Select departments to start in open mode ',THEME_SLUG),
				'desc' => __('Open mode',THEME_SLUG),
				'type' => 'select',
				'select' => 'multiple',
				'source' => 'category_cws-staff-dept',
				'source_args' => 'parent=0',
				'w' => '100%'
			),
		),
	),
	'portfolio' => array(
		'options' => array(
			array(
				'id' => 'title',
				'title' => __('Title',THEME_SLUG),
				'type' => 'text',
				'w' => '50%'
			),
			array(
				'id' => 'cols',
				'title' => __('Columns',THEME_SLUG),
				'desc' => __('Columns',THEME_SLUG),
				'type' => 'select',
				'source' => array(
					'1' => __('One', THEME_SLUG),
					'2' => __('Two', THEME_SLUG),
					'3' => __('Three', THEME_SLUG),
					'4' => __('Four', THEME_SLUG),
					),
				'default' => '2',
				'w' => '50%'
			),
			array(
				'id' => 'cat',
				'title' => __('Categories',THEME_SLUG),
				'desc' => __('Categories',THEME_SLUG),
				'type' => 'select',
				'select' => 'multiple',
				'source' => 'portfolio_categories',
				'w' => '50%'
			),
			array(
				'id' => 'filtering',
				'title' => __('Use portfolio filter',THEME_SLUG),
				'type' => 'check',
				'source' => array( '' => '' ),
				'default' => array(true),
			),
			array(
				'id' => 'usecarousel',
				'title' => __('Display as carousel', THEME_SLUG),
				'type' => 'check',
				'toggle' => array('carousel_title'),
				'source' => array('' => ''),
				'default' => array(false),
			),
			array(
				'id' => 'posts',
				'title' => __('Items per page',THEME_SLUG),
				'type' => 'text',
				'w' => '100px'
			),
		),
	),
	'shortcode_carousel' => array(
		'options' => array(
			array(
				'id' => 'title',
				'title' => __('Title',THEME_SLUG),
				'type' => 'text',
				'default' => '',
				'w' => '50%',
			),
		),
	),
	'shortcode_blog' => array(
		'options' => array(
			array(
				'id' => 'title',
				'title' => __('Title',THEME_SLUG),
				'type' => 'text',
				'default' => '',
				'w' => '50%',
			),
			array(
				'id' => 'columns',
				'title' => __('Columns',THEME_SLUG),
				'desc' => __('Columns',THEME_SLUG),
				'hidden' => false,
				'type' => 'select',
				'source' => array(
							'2' => __('Two Columns', THEME_SLUG),
							'3' => __('Three Columns', THEME_SLUG),
							'4' => __('Four Columns', THEME_SLUG),
							),
				'default' => '3',
				'w' => '50%',
			),
			array(
				'id' => 'post_count',
				'title' => __('Post count',THEME_SLUG),
				'type' => 'text',
				'default' => '9',
				'w' => '50%',
			),
			array(
				'id' => 'cat',
				'title' => __('Categories',THEME_SLUG),
				'desc' => __('Categories',THEME_SLUG),
				'type' => 'select',
				'select' => 'multiple',
				'source' => 'categories',
				'w' => '50%'
			),
		),
	),
);

// parse_options( ['options'], null, 'widget-' . $this->id_base, $this->number, '', false);
function parse_options ($options, $value, $prefix, $id_postfix = '', $parent = '', $usetd) {
	$output = '';
	$is_widget = false;
	$pre_prefix = 'cws-';
	if ('widget' == substr($prefix,0, 6) ) {
		$pre_prefix = 'class="widefat" ';
		$is_widget = true;
	}
	if (!empty($parent)) {
		$prefix = $prefix . '-' . $parent;
	}
	global $opt_attr;
	global $opt;
	$opt = $options;
	foreach($options as $attr) {
		$is_group = false;
		$opt_attr = $attr;
		$is_hidden = isset($attr['hidden']) ? $attr['hidden'] : false;
		if ($usetd)
			$output .= $is_hidden ? '<tr class="hidden" style="display:none;">' : '<tr>';
			//$output .= '<tr>';

			$width = isset($attr['w']) ? $attr['w'] : '';
			$title = isset($attr['title']) ? $attr['title'] : '';
			$current_value = isset($attr['default']) ? $attr['default'] : null;

			$style_width = !empty($width) ? ' style="width:' . $width . '"' : '';

			if ($usetd) {
				$output .= '<td>';
				if (!empty($value) && !isset($current_value)) {
					$current_value = $value;
				}
			}
			else {
				$is_group = true;
				// group
				$id = 'cws-mb-' . $parent . '-' . $attr['id'];
				if (!empty($value[$id])) {
					$current_value = $value[$id];
				}

				$output .= '<div class="group"';
				if (!empty($title))	{
					$title .= ':';
				}

				$inline_style = isset($attr['clear']) ? 'clear:both;' : '';

				$inline_style .= (isset($attr['hidden']) && true == $attr['hidden']) ? 'display:none;' : '';

				$inline_style .= !empty($width) ? 'width:' . $width . ';' : '';

				$output .= !empty($inline_style) ? ' style="'.$inline_style.'"' : '';


				$output .= '>';
			}

			if (!empty($title) && 'select' != $attr['type'])	{
				$output .= '<label for="'. $pre_prefix .'-'. $prefix . '-'. $attr['id'] . $id_postfix .'">' . $title . '</label>';
			}
			if ( isset($attr['desc']) ) {
				$output .= '<div class="desc">' . $attr['desc'] . '</div>';
			}

			if ($usetd)
				$output .= '</td><td>';
			elseif ($title)
				$output .= '&nbsp;&nbsp;';

			if ($is_widget) {
				//$unique_name = $prefix.'-'. $attr['id']; // . $id_postfix;
				// $prefix - widget-cws-services
				// $id_postfix - instance number
				$unique_name = $prefix .'['.$id_postfix.']['. $attr['id'] .']';
				$unique_id = $prefix .'-'.$id_postfix.'-'. $attr['id'];
				$name_id = ' name="'. $unique_name .'" id="'.	$unique_name .'"';
			} else {
				$unique_name = $pre_prefix . $prefix.'-'. $attr['id'] . $id_postfix;
				$name_id = ' name="'. $unique_name .'" id="'. $unique_name .'"';
			}

			if ( isset($attr['disabled']) && (true === $attr['disabled']) ) {
				$name_id .= ' disabled';
			}

			switch ($attr['type']) {
				case 'button':
					$output .= '<input type="button" class="button"' . $name_id . ' value="' . $attr['value'] . '" ' . $style_width . '/>';
				break;
				case 'image_select':
					if (isset($attr['options'])) {
						//$hidden = isset($attr['options']) ? ' style="display:'. ($attr['hidden'] ? 'none' : 'block') .';"' : '';
						$output .= '<ul class="redux-image-select"' . $name_id . '>';
						$options = $attr['options'];
						$attr_id = $attr['id'];
						$i = 0;
						foreach ($options as $k=>$v) {
							$i++;
							$output .= '<li class="redux-image-select">';
							$output .= '<label for="' . $attr_id . '_' . $i . '"' . ( $current_value == $k ? ' class="redux-image-select-selected"' : '' ) . '>';
							$js_onlick = gen_onclick($v, $prefix, 'show', $is_group);
							$output .= '<div class="cws_img_select_wrap">';
								$output .= '<input type="radio" ' .
									( !empty($js_onlick) ? $js_onlick . ' ' : '') .
									( $current_value == $k ? 'checked="checked"' : '' ) .
									' id="' . $attr_id . '_' . $i . '" name="' . $unique_name . '" value="'.$k.'">';
								$output .= '<img src="'.$v['img'].'" alt="'.$v['title'].'">';
							$output .= '</div>';
							$output .= '<div>'.$v['title'].'</div>';
							$output .= '</label></li>';
						}
						$output .= '</ul>';
					}
					break;
				case 'select':
					$multiple = '';
					if (isset($attr['select']) && $attr['select'] === 'multiple') {
						$multiple = ' multiple';
						if ($is_widget) {
							$name_id = ' name="'. $unique_name .'[]" id="'.	$unique_id .'[]"';
						}
						else {
							$name_id = ' name="'. $unique_name .'[]" id="'. $unique_name .'[]"';
						}
					}
					$output .= '<select' . $multiple . $name_id . $style_width . ' data-placeholder="'.$title.'">';
					if (isset($attr['source']) ) {
						$source = array();
						if (is_string($attr['source'])) {
							$cat = $attr['source'];
							if ('category_' == substr($cat, 0, 9)) {
								$cat_source_attr = isset($attr['source_args']) ? $attr['source_args'] : null;
								$source = get_taxonomy_array(substr($cat, 9), $cat_source_attr);
							} else {
								switch ($cat) {
									case 'price_categories':
										$source = get_price_categories();
									break;
									case 'team_categories':
										$source = get_taxonomy_array('cws-team-dept');
									break;
									case 'portfolio_categories':
										$source = get_taxonomy_array('cws-portfolio-type');
									break;
									case 'categories':
										$source = get_taxonomy_array('category');
									break;
									case 'sidebars':
										$source = get_sidebars_array();
									break;
									default:
										// by default we assume it's just a function we should call
										require_once(TEMPLATEPATH . '/framework/rc/inc/fields/select/fa-icons.php');
										$so = call_user_func_array($cat, array());
										$source = array_combine($so, $so);
								}
							}
						}
						else {
							$source =  $attr['source'];
						}
						$output .= '<option value=""></option>';
						foreach($source as $k=>$v) {
							$selected = '';
							if (is_array($current_value)) {
								$selected = in_array($k, $current_value) ? ' selected="selected"' : '';
							}
							else {
								$selected = ($k == $current_value ? ' selected="selected"' : '' );
							}
							$output .= '<option value="' . $k . '"' . $selected . '>' . $v . '</option>';
						}
					}
					$output .= '</select>';
				break;

				case 'range':
					$output .= '<input type="range"' . $name_id;
					$output .= ' value="' . $current_value . '" min="' . ( $attr["min"] ? $attr["min"] : "0" ) . '" max="' . ( $attr["max"] ? $attr["max"] : "100" ) . '" step="' . ( $attr["step"] ? $attr["step"] : "1" ) . '"';
					$output .= $style_width . '/>';
				break;

				case 'color':
					$output .= '<input type="text"' . $name_id;
					$output .= ' value="' . $current_value . '" data-default-color="' . $current_value . '"';
					$output .= $style_width . '/>';
				break;

				case 'check':
					$i = 0;
					foreach($attr['source'] as $k=>$v) {
						$js_onlick = '';
						$group_prefix = '';
						if (isset($attr['toggle'])) {
							$js_onlick = ' onclick="var input;';
							foreach ($attr['toggle'] as $control_name) {
								$js_onlick .= 'input = document.getElementById(\'cws-'. $group_prefix . $prefix.'-'. $control_name . $id_postfix .'\');';
								$js_onlick .= 'input.disabled = !this.checked;';
							}
							$js_onlick .= '"';
						} elseif ( isset($attr['hide']) ) {
							$js_onlick = gen_onclick($attr, $group_prefix . $prefix, 'toggle', $is_group);
						}
						$output .= '<input type="checkbox" name="cws-' . $group_prefix . $prefix.'-' . $attr['id'] . '" value="' . $k . '"' . ($current_value[$i] ? ' checked' : '' ) . $js_onlick . '>' . $v . '</input>&nbsp;';
						$i++;
					}
				break;

				case 'radio':
					foreach($attr['source'] as $k=>$v) {
						$output .= '<input type="radio" name="cws-'.$prefix.'-' . $attr['id'] . '" value="' . $k . '"' . ($k==$current_value ? ' checked' : '' ) . '>' . $v . '</input>&nbsp;';
					}
				break;

				case 'text':
					$val = '';
					if (!empty($current_value)) {
						$val = ' value="' . $current_value . '"';
					}
					$output .= '<input type="text"' . $name_id . $val . $style_width . '/>';
				break;

				case 'textarea':
					$val = '';
					if (!empty($current_value)) {
						$val = $current_value;
					}
					$output .= '<textarea' . $name_id . '" rows="' . $attr['rows'] . '"' . $style_width . '>' . $val . '</textarea>';
					$value = '';
				break;

				case 'group':
					$output .= parse_options($attr['source'], $value, $prefix, $id_postfix, $attr['id'], false);
				break;

				case 'media_library':
					$output .= "<img id='img-" . $unique_name . "'" . "class='selected_media' />";
					$output .= "<a id='media-" . $unique_name . "'>" . __("Select Image", THEME_SLUG) . "</a><a id='remov-" . $unique_name . "' style='display:none;'>" . __("Remove Image", THEME_SLUG) . "</a>";
				break;

			}
			if ($usetd) {
				$output .= '</td>';
				$output .= '</tr>';
			}
			else {
				$output .= '</div>';
			}
		}
	return $output;
}

function gen_onclick($options, $prefix, $operation = 'toggle', $group = false) {
	$js_onlick = ' onclick="';
	if (isset($options['show'])) {
		$controls = $options['show'];
		$js_onlick .= 'jQuery(\'';
		$i = 0;
		foreach ($controls as $control_name) {
			$js_onlick .= $i ? ',' : '';
			$control_name = str_replace('[]', '\\\\[\\\\]', $control_name);
			$js_onlick .= '#cws-'. $prefix.'-'. $control_name;
			$i++;
		}
		$js_onlick .= '\').parents(\'tr\').show(500);';
	}
	if (isset($options['hide'])) {
		$controls = $options['hide'];
		$js_onlick .= 'jQuery(\'';
		$i = 0;
		foreach ($controls as $control_name) {
			$js_onlick .= $i ? ',' : '';
			$control_name = str_replace('[]', '\\\\[\\\\]', $control_name);
			$js_onlick .= '#cws-'. $prefix.'-'. $control_name;
			$i++;
		}
		$method = $operation == 'toggle' ? 'toggle' : 'hide';
		if ($group){
			$js_onlick .= '\')' . '.parents(\'.group\').' . $method . '(500);';
		}
		else{
			$js_onlick .= '\')' . '.parents(\'tr\').' . $method . '(500);';			
		}
	}
	$js_onlick .= '"';
	return $js_onlick;
}

function search_id($arr, $field)
{
	$depend = null;
	foreach ($arr as $data)
	{
		if ($data['id'] == $field && isset($data['depend'])) {
			$depend = $data['depend'];
			$depend = $depend[0];
		}
		if ($depend) {
			if ($data['id'] == $depend)
				return $data;
		}
	}
}

function cws_shortcode_html_gen ($options_array, $id, $value, $id_postfix = '', $ispopup = true, $prefix = 'mb' ) {

	$output = '';

	if(@$options_array[$id]) {
		$output .= '<table class="mb-table">';
		if ($ispopup) {
			$output .= '<th colspan=2 class="shortcode-name">Shortcode: <span>' . $id . '</span></th>';
		}
		$output .= parse_options($options_array[$id]['options'], $value, $prefix, $id_postfix, '', true);

		$output .= '</table>';
	}
	return $output;
}

function get_price_categories () {
	global $wpdb;
	$get_price_categories = array();
	$cats = (array) $wpdb->get_results("SELECT t.term_id, t.name, t.slug
		FROM $wpdb->terms as t INNER JOIN
			(select tt.term_id FROM wp_term_taxonomy AS tt INNER JOIN
				(SELECT tr.term_taxonomy_id FROM $wpdb->term_relationships AS tr INNER JOIN
					(SELECT tr.object_id, tr.term_taxonomy_id FROM $wpdb->term_relationships AS tr
						WHERE tr.term_taxonomy_id =
						(SELECT tt.term_taxonomy_id FROM $wpdb->term_taxonomy AS tt WHERE tt.term_id =
							(SELECT t.term_id FROM $wpdb->terms AS t WHERE t.slug = \"post-format-table\")
						)
					)	AS temp ON tr.object_id = temp.object_id
					WHERE tr.term_taxonomy_id <> temp.term_taxonomy_id AND tr.term_taxonomy_id <> 1
					GROUP BY tr.term_taxonomy_id
				) as rels ON tt.term_taxonomy_id = rels.term_taxonomy_id
			) as cats ON t.term_id = cats.term_id");
	foreach ($cats as $k=>$v) {
		$get_price_categories[$v->slug] = $v->name;
	}
	return $get_price_categories;
}

function get_taxonomy_array($tax, $args = '') {
	if (!empty($args)) {
		$args .= '&';
	}
	$args .= 'hide_empty=0';
	$terms = get_terms($tax, $args);
	$ret = array();
	foreach ($terms as $k=>$v) {
		$ret[$v->slug] = $v->name;
	}
	return $ret;
}

function get_sidebars_array() {
	global $wp_registered_sidebars;
	$ret = array();
	foreach ( (array) $wp_registered_sidebars as $k=>$v) {
		$ret[$v['id']] = $v['name'];
	}
	return $ret;
}

/*
SELECT tr.term_taxonomy_id
FROM wp1_term_relationships AS tr
INNER JOIN (
SELECT tr.object_id, tr.term_taxonomy_id
FROM wp1_term_relationships AS tr
WHERE tr.term_taxonomy_id = (SELECT tt.term_taxonomy_id
	FROM wp1_term_taxonomy AS tt
	WHERE tt.term_id = (SELECT t.term_id
		FROM wp1_terms AS t
		WHERE t.slug = "post-format-table")
		)
	) AS temp ON tr.object_id = temp.object_id
WHERE tr.term_taxonomy_id <> temp.term_taxonomy_id AND tr.term_taxonomy_id <> 1
GROUP BY tr.term_taxonomy_id
*/