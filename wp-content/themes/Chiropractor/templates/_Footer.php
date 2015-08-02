<?php if(is_page_template( 'lead_capture_left.php' ) | is_page_template( 'lead_capture_full.php' ) | is_page_template( 'lead_capture_right.php' )){ }else{ 
if(ot_get_option( 'footer_widget_disable' )!=='off'){ ?>
<div class="container footer_container">
		
        <div class="row footer_row" >
           <?php 			   
		   if (  is_wpm_sidebar_active('footer_widget_area_1') |  is_wpm_sidebar_active('footer_widget_area_2') | is_wpm_sidebar_active('footer_widget_area_3') |  is_wpm_sidebar_active('footer_widget_area_4') ) { 
		   
		   		$wpm_footer_layout = ot_get_option('wpm_footer_layout');
                 switch ($wpm_footer_layout)
				{
				case "one":
				  get_template_part( 'templates/_Footer_widget_1', '' );
				  break;
				
				case "two":
				  get_template_part( 'templates/_Footer_widget_2', '' );
				  break;
				
				case "three":
				  get_template_part( 'templates/_Footer_widget_3', '' );
				  break;
				
				case "four":
				  get_template_part( 'templates/_Footer_widget_4', '' );
				  break;
				
				case "five":
				  get_template_part( 'templates/_Footer_widget_5', '' );
				  break;
				
				case "six":
				  get_template_part( 'templates/_Footer_widget_6', '' );
				  break;
				case "seven":
				  get_template_part( 'templates/_Footer_widget_7', '' );
				  break;
				case "eight":
				  get_template_part( 'templates/_Footer_widget_8', '' );
				  break;
				      
				default:
				  get_template_part( 'templates/_Footer_widget_1', '' );
				}
				
				 
            }else{
				
				echo "<span>This area is widget ready area and you can add content here by going here...  <strong>Appearance >> Widget >> Footer widgets</strong>. You can add four widgets. Also you can disable this area by going here... <strong>Appearance >> Theme Options >> Footer Settings</strong>.</span>";
				
			}?>                   			
    	</div>
    </div>
<?php } 
 } ?>

<?php if(ot_get_option( 'footer_copyright_disable' )!=='off'){ ?>
<div class="container copyright_container" >
	
    <div class="row">		
            <div class="footer_nav">
			<?php 
				if ( has_nav_menu( 'footer' ) ) { 
					wp_nav_menu( array('theme_location' => 'footer', 'container' => false) );
				}
			  ?>
            </div>
    </div>        
	<div class="row copyright_row" >
             <div class="five  columns align_left"><?php if(ot_get_option( 'copy_right_mania' )){  echo ot_get_option( 'copy_right_mania' ); }else{ echo"@"." ".date('Y')." All Right Reserved"; } ?></div>
             <div class="seven columns align_right" ><?php if(ot_get_option( 'credit_text' )){ echo ot_get_option( 'credit_text' ); }else{ _e('Powered by WordPress. <a href="http://wpmania.net/Store/" target="_blank">Website Designed and developed</a> by <a href="http://wpmania.net/Store/" target="_blank">WpMania.Net</a> Team', 'wpm_textdomain'); } ?></div>
    </div>
</div>
<?php } ?>