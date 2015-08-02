<div  class="container" id="wpm_header">
	<div class="row wpm_header_row"  >
    	<div class="six columns align_left">
        	<?php wpm_logo(); ?>
        </div>
        <div class="six columns align_right">
            <?php 
			if ( ! dynamic_sidebar( 'header_widget_area' ) ) : 
			
			wpm_social();
			wpm_contact(); 
			
			endif; // end header widget
			?>
        </div>
     </div>
</div>

<div class="main_warper">

<?php if(is_page_template( 'no_menue_left.php' ) | is_page_template( 'no_menue_right.php' ) | is_page_template( 'no_menue_full.php' )){ }else{ ?>    
<div class="container" id="wpm_menu_bg">
	<div class="row menu_bg_row"  >
    	
    	<div class="twelve columns">
        	<?php wpm_nav(); ?>    
        </div>
        
        <div class="wpm_search_warp align_right">
        	<?php wpm_search(); ?>
        </div>
        
    </div>
</div>
<?php } ?>