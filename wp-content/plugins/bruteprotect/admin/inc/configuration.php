<?php
global $privacy_opt_in, $remote_security_options, $local_host, $bruteprotect_api_key;
global $privacy_success;
global $wordpress_success;
global $whitelist_success;
$brute_ip_whitelist = get_site_option( 'brute_ip_whitelist' );
$admins = get_site_option( 'brute_dashboard_widget_admin_only' );
define( 'BRUTE_IMAGE_PATH', plugins_url() . '/bruteprotect/images/' );
include( 'header.php' );
?>

<div id="bruteapi" class="new_ui row">

<header class="uiheader clearfix" data-equalizer>

    <div class="columns large-9 medium-8 small-12 logogroup" data-equalizer-watch>

        <h2 class="status">
            <div class="logo">
                <img src="<?PHP echo MYBP_URL; ?>assets/images/bruteprotect-dark.png" alt="BruteProtect">
                <span class="msg"><i class="fa fa-check-square "></i> <span>BruteProtect is working</span></span>
            </div><?php // logo ?>
        </h2>

    </div>
    <!-- // logogroup -->

    <div class="columns large-3 medium-4 small-12 btngroup" data-equalizer-watch>

	    <!-- this div used to hold the 'Disconnect Site' button -->

    </div>
    <!-- // btngroup -->

</header>


<style type="text/css">

#bruteapi {
box-sizing: border-box;
}
    
.jetpack-note {
    width: 100%;
    padding: 1.5rem 1.875rem 1.875rem 1.875rem;
    border: 1px #e9e9e9 solid;
    border-bottom: 0;
    background: #f9f9fa url('<?php echo BRUTE_IMAGE_PATH; ?>jetpack-clouds.png') -50px -25px no-repeat;
}

.dismiss-notice {
    float: right;
    font-size: 20px;
    color: #6a6a68;
    opacity: .5;
}

.jetpack-logo {
    width: 100%;
}

.msg-hld {
    margin-top: 70px;
}

.jetpack-note h3 {
    margin-top: 0;
}

.jetpack-logo a {
    display: block;
}

.jetpack-logo img {
   width: 325px;
   max-width: 100%;
}

.jetdesc {
    padding-right: 25%;
    max-width: 980px;
    margin-bottom: 0;
}

.jet-btn, .jet-btn:visited {
    background: #8cc258;
    color: #fff;
    padding: 20px 30px;
    text-align: center;
    float: right;
    display: block;
    margin-left: 45px;
    margin-top: 30px;
    border-radius: 6px;
    font-size: 1.5em;
    webkit-transition: all .2s ease-in-out;
    transition: all .2s ease-in-out;
}

.jet-btn:hover {
    background: #31621b;
    color: #fff;
}


.sunset {
    background: #f26722 url('<?php echo BRUTE_IMAGE_PATH; ?>jetpack-bp-clouds-2x.png') 50% 0 no-repeat;
    background-size: 345%;
    padding: 8rem 1.875rem 1.875rem 1.875rem;
    color: #fff;
    margin-top: -1.875rem;
    margin-right: -15px;
    float: right;
}

   /* @media screen and (-webkit-min-device-pixel-ratio: 2) and (min-device-pixel-ratio: 2) { 

       .sunset {
        background: #f26722 url('/wp-content/plugins/bruteprotect/images/jetpack-bp-clouds-2x.png') 50% 0 no-repeat; 
        background-size: 100%;
        }

    } */

@media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min-resolution: 2dppx), only screen and (min-resolution: 192dpi) {

    .sunset {
        background-size: 170%;
    }

  }


.sunset p {
    font-size: 14px;
    margin-bottom: 0; 
}

.sunset h4, .sunset a, .sunset a:hover {
    color: #fff;
}

.sunset a {
    text-decoration: underline;
    font-style: italic;
}

.sunset a:hover {
    text-decoration: none;
}


.new_ui #bp-settings-form {
    margin-left: 0;
}


.new_ui .brutecontainer h3.attn {
    text-align: left;
}

.whitelistoptions h4, .whitelistoptions p {
    text-align: left;
}

.settinghold {
    margin-left: 1.875rem;
}

.checkhold {
    padding-left: 0;
}


/* media queries */

@media screen and (max-width: 1125px) {

.sunset {
    width: 100%;
    margin: 0;
    float: none;
    margin-top: -1rem;
    margin-bottom: 1.875rem;
}

.sunset p {
    font-size: 18px;
}

.large-8.settinghold {
    width: 100%;
}

.new_ui #bp-settings-form {
    max-width: 90%;
}

} /* > 1125px */

@media screen and (max-width: 885px) {


.new_ui .brutecontainer input[type="submit"] {
    width: 100%;
}

.new_ui .bpwpoptions select {
    width: 100%;
}

textarea.ipholder {
    margin-bottom: 0;
}

.settinghold {
    margin-left: 0;
      padding-left: 30px;
    padding-right: 30px;
    margin-bottom: 55px;  
}

.new_ui #bp-settings-form {
    max-width: 100%;
}

.apioptions, 
.whitelistoptions,
.bpwpoptions,
.settinghold {

    margin-bottom: 55px;
}

} /* > 885px */

@media screen and (max-width: 775px) {

.jet-btn {
    float: none;
    width: 100%;
    margin: 0 0 15px 0;
}

.jetdesc {
    padding-right: 0;
}

} /* > 775px */

@media screen and (max-width: 645px) {

.brutecontainer .row {
    margin-left: 0;
    margin-right: 0;
}

.apioptions, 
.whitelistoptions,
.bpwpoptions {
    padding-left: 30px;
    padding-right: 30px;
    margin-bottom: 55px;
}

} /* > 645px */

@media screen and (max-width: 500px) {


.apioptions, 
.whitelistoptions,
.bpwpoptions,
.settinghold,
.jetpack-note {
    padding-left: 10px;
    padding-right: 10px;
}

#bruteapi {
    max-width: 98%;
}



} /* > 500px */

</style>
<?php /* ROCCO TO DO

1. Remove hardcode path to jetpack logo (in css + img tags below)
2. Insert the proper links for: Jetpack Logo & Learn More (link to Jetpack blog article), and BruteProtect Learn more


*/ ?>

	<?php if ( brute_show_jetpack_notice() ) : ?>
		<!-- This notice will disappear forever once dismissed -->
		<!-- get it back by visiting mysite.com/wp-admin/admin.php?page=bruteprotect-config&brute_dismiss_notice=0 -->
	<div id="jetpack-notice" class="jetpack-note">
		
        <div class="jetpack-logo">
            <a id="dismiss_button" class="dismiss-notice" href="?page=bruteprotect-config&brute_dismiss_notice=1" title="Dismiss"><i class="fa fa-times-circle"></i></a>
            <a href="http://jetpack.me/2014/12/16/jetpack-3-3-a-single-home-for-all-your-wordpress-sites/" title="Learn more about the new Jetpack changes" target="_blank"><img src="<?php echo BRUTE_IMAGE_PATH; ?>jetpack-logo.png" alt="Jetpack Logo" /></a></p>
        </div><?php // jp logo ?>


        <div class="msg-hld">

	        <?php if ( brute_needs_jetpack_install() ) : ?>
		        <a id="jetpack-button" class="jet-btn" href="plugin-install.php?tab=search&s=jetpack ">Install Jetpack Now</a>
	        <?php endif; ?>

	        <?php if ( brute_needs_jetpack_update() ) : ?>
		        <a id="jetpack-button" class="jet-btn" href="update-core.php">Upgrade Jetpack Now</a>
	        <?php endif; ?>


        <h3>All your sites. Always up-to-date.</h3>

        <p class="jetdesc">The release of Jetpack 3.3 brings you a centralized dashboard: now you can manage all your WordPress sites – both WordPress.com and Jetpack connected – in one spot! <a href="http://jetpack.me/2014/12/16/jetpack-3-3-a-single-home-for-all-your-wordpress-sites/" title="Learn more about the new Jetpack changes" target="_blank">Learn more.</a></p>
        </div><?php // msg-hld ?>

	</div>
	<?php endif; ?>


<div class="brutecontainer columns large-12 finalstep">


<div class="hover apipanel" data-equalizer data-equalizer-watch>

<div class="front" data-equalizer-watch>
<div class="frontinner" data-equalizer-watch>


<?php if ( bruteprotect_is_linked() ) : ?>
	<div id="tab-1" class="mybruteprotect row">

		<?php if ( !empty( $privacy_success ) ) : ?>
			<div id="alert-1" class="alert-box success" style="margin-bottom: 40px;">
				<?php _e( $privacy_success ); ?>
			</div>
		<?php endif; ?>

        <div class="sunset">
            <h4>As of Dec. 19, 2014</h4>
            <p>No new sites may be connected to MyBruteProtect. <br /><a href="https://bruteprotect.com/?p=1009" title="Learn more about the upcoming changes to BruteProtect">Learn more about the upcoming changes</a></p>
        </div>


        <div class="large-8 settinghold">
			<h3 class="attn">MyBruteProtect.com Settings</h3>


			<form action="#alert-1" method="post" accept-charset="utf-8" id="bp-settings-form">

				<input type="hidden" name="brute_action" value="privacy_settings"/>
				<input type="hidden" name="step_3" value="true"/>
				<?php $nonce_privacy = wp_create_nonce( 'brute_privacy' ); ?>
				<input type="hidden" name="brute_nonce" value="<?php echo $nonce_privacy; ?>"/>

				<div class="checkrow large-10" data-equalizer>

					<?php if ( is_array( $remote_security_options ) ) : ?>
						<?php foreach ( $remote_security_options as $key => $desc ) : ?>
							<?php $checked = ( isset( $privacy_opt_in[ $key ] ) ) ? 'checked="checked"' : ''; ?>
							<div class="checkrow" data-equalizer>

								<div class="columns large-1 medium-1 small-12 checkholder" data-equalizer-watch>
									<input name="privacy_opt_in[<?php echo $key; ?>]" type="checkbox" value="1"
									       class="bp_privacy_opt_in_checkbox" <?php echo $checked; ?> />
								</div><?php // lrg-1 ?>

								<div class="columns large-11 medium-11 small-12" data-equalizer-watch>
									<label for="privacy_opt_in[<?php echo $key; ?>]"
									       class="setting"><?php echo $desc; ?></label>
								</div><?php // lrg-11 ?>
                                <br /><br />
							</div><!-- row -->
						<?php endforeach; ?>
					<?php endif; ?>

				</div>
				<!-- row -->

				<div class="row">
					<input type="submit" value="Save Settings" class="permission">
				</div>
				<!-- row -->

			</form>
		
            </div>

	</div>
<?php endif; ?>
<br /><br />

<div id="tab-2" class="apioptions">

    <h3 class="attn">API Key for: <em><?php echo $local_host; ?></em></h3>


    <form action="" method="post" class="apiholder clearfix" id="remove_api_key_form">
        <input type="text" name="brute_api_key" value="<?php echo $bruteprotect_api_key; ?>" id="brute_api_key"
               disabled="disabled"/>
        <?php $nonce_remove_key = wp_create_nonce( 'brute_remove_key' ); ?>
        <input type="hidden" name="brute_nonce" value="<?php echo $nonce_remove_key; ?>"/>
        <input type="hidden" name="brute_action" value="remove_key"/>
        <input type="submit" value="Remove API Key" class="button green alignright" id="remove_api_key_button"/>

        <script>
            jQuery(document).ready(function () {
                jQuery("#remove_api_key_button").click(function (e) {
                    e.preventDefault();
                    var d = confirm("Removing your API key will remove any pro features you have as well as brute force protection. \n\n You can generate a new key in the future.\n\nAre you sure you want to remove your API key?");
                    if (d) {
                        jQuery("#remove_api_key_form").submit();
                    }
                });
            });
        </script>
    </form>


</div> <?php // tab 2 ?>



<div id="tab-3" class="whitelistoptions">

    <?php if ( !empty( $whitelist_success ) ) : ?>
        <div id="alert-2" class="alert-box success">
            <?php _e( $whitelist_success ); ?>
        </div>
    <?php endif; ?>

    <h3 class="attn">IP Whitelist: Always allow access from the following IP's</h3>


    <h4>Your current IP address is: <strong><?php echo $this->brute_get_ip(); ?></strong></h4>

    <p>Enter one IPv4 per line, * for wildcard octet<br/>
        <em>(ie: <code>192.168.0.1</code>
            and <code>192.168.*.*</code> are valid, <code>192.168.*</code> and <code>192.168.*.1</code> are
            invalid)</em>
    </p>

    <form action="#alert-2" method="post" class="clearfix">
        <?php $nonce_whitelist = wp_create_nonce( 'brute_whitelist' ); ?>
        <input type="hidden" name="brute_nonce" value="<?php echo $nonce_whitelist; ?>"/>
        <textarea name="brute_ip_whitelist" class="ipholder" rows="10"><?php echo $brute_ip_whitelist ?></textarea>

        <input type="hidden" name="brute_action" value="update_brute_whitelist"><br>
        <input type="submit" value="Save IP Whitelist" class="button" style="margin-bottom: 50px;" >
    </form>


</div><?php // tab 3 ?>



<div id="tab-4" class="bpwpoptions clearfix">
    <?php if ( !empty( $wordpress_success ) ) : ?>
        <div id="alert-3" class="alert-box success">
            <?php _e( $wordpress_success ); ?>
        </div>
    <?php endif; ?>
    <h3 class="attn">BruteProtect dashboard widget should be visible to...</h3>


    <form action="#alert-3" method="post" accept-charset="utf-8" id="bp-settings-form">
        <select name="brute_dashboard_widget_admin_only" id="brute_dashboard_widget_admin_only">
            <option value="0" <?php if ( $admins == '0' ) {
                echo 'selected="selected"';
            } ?>>All users who can see the dashboard
            </option>
            <option value="1" <?php if ( $admins == '1' ) {
                echo 'selected="selected"';
            } ?>>Admins Only
            </option>
        </select>
        <?php $nonce_general = wp_create_nonce( 'brute_general' ); ?>
        <input type="hidden" name="brute_nonce" value="<?php echo $nonce_general; ?>"/>
        <input type="hidden" name="brute_action" value="general_update" id="brute_action">
        <input type="submit" value="Save Changes" class="button button-primary blue alignright">
    </form>

</div> <?php // tab 4 ?>


</div><?php // frontinner ?>
</div> <?php // front of flip ?>

</div> <?php // hover ?>


</div>
<!-- // brute container -->
</div> <!-- // brute api -->

<?php include( 'footer.php' ); ?>
