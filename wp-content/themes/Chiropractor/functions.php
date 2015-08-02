<?php
define('sc3', '');
include ('child_functions.php');

function wpm_lead_page_header_function2(){
echo '<div class="main_warper">';	
	}
add_action('wpm_lead_page_header_hook','wpm_lead_page_header_function2', '15');



function wpm_reset_function3() {	
	remove_filter('my_featured_columns_content', 'my_featured_columns_content_dfo');
	add_filter('my_featured_columns_content', 'my_featured_columns_content_dfo2');
	
		function my_featured_columns_content_dfo2($data){
			
			$data['order'] =  array('icon', 'title', 'content', 'readmore');
			$data['button'] = 'yes';
			$data['hide_title_icon'] = 'yes';
			
			return $data;
		}
	
	}
add_action( 'init', 'wpm_reset_function3' );
?>