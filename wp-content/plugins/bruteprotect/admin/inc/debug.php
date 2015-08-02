<?php if( ! empty( $debug_notice ) ) : ?>
    <div class="updated before-h2">
        <p><?php echo $debug_notice; ?></p>
    </div>
<?php endif; ?>

<h2>BruteProtect Connection Status</h2>
<?php if( ! $has_access ) : ?>
        <p class="bp_connection_status bad">Your site is having trouble connecting to the BruteProtect API.</p>
<?php else : ?>
        <p class="bp_connection_status good">Your site is connecting successfully to the BruteProtect API.</p>
<?php endif; ?>

<h3>Connection Results</h3>
<pre id="bp_connection_results">
    <?php print_r( $error_info ); ?>
</pre>
<form method="post">
    <input type="hidden" name="bp_debug_action" value="send_error_report" />
    <input type="hidden" name="bp_debug_nonce" value="<?php echo $error_report_nonce; ?>" />
    <input type="submit" value="Send Report to BruteProtect Support" />
</form>

<h3>Change API Endpoints</h3>
<p>Changing your endpoint location may have unwanted effects. Only do this if instructed by someone from the BruteProtect support team.</p>
<form method="post">
    <input type="hidden" name="bp_debug_action" value="change_endpoints" />
    <input type="hidden" name="bp_debug_nonce" value="<?php echo $endpoint_nonce; ?>" />
    <p>
        Endpoint Location:<br />
        <input type="radio" name="brute_endpoint" value="live" <?php echo $live_checked; ?> /> Live<br />
        <input type="radio" name="brute_endpoint" value="testing" <?php echo $testing_checked; ?> /> Testing
    </p>
    <input type="submit" value="Change" />
</form>