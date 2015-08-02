<?php

/**
 * Email notifications for backups
 *
 * @extends HMBKP_Service
 */
class HMBKP_Email_Service extends HMBKP_Service {

	/**
	 * Human readable name for this service
	 * @var string
	 */
	public $name = 'Email';

	/**
	 * Determines whether to show or hide the service tab in destinations form
	 * @var boolean
	 */
	public $isTabVisible = false;

	/**
	 * Output the email form field
	 *
	 * @access  public
	 */
	public function field() { ?>

		<div>

			<label for="<?php echo esc_attr( $this->get_field_name( 'email' ) ); ?>"><?php _e( 'Email notification', 'hmbkp' ); ?></label>

			<input type="email" id="<?php echo esc_attr( $this->get_field_name( 'email' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email' ) ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'email' ) ); ?>" />

       <p class="description"><?php printf( __( 'Receive a notification email when a backup completes, if the backup is small enough (&lt; %s) then it will be attached to the email. Separate multiple email address\'s with a comma.', 'hmbkp' ), '<code>' . size_format( hmbkp_get_max_attachment_size() ) . '</code>' ); ?></p>

		</div>

	<?php }

	/**
	 * Not used as we only need a field
	 *
	 * @see  field
	 * @return string Empty string
	 */
	public function form() {
		return '';
	}

	public static function constant() { ?>

		<tr<?php if ( defined( 'HMBKP_ATTACHMENT_MAX_FILESIZE' ) ) { ?> class="hmbkp_active"<?php } ?>>

			<td><code>HMBKP_ATTACHMENT_MAX_FILESIZE</code></td>

			<td>

				<?php if ( defined( 'HMBKP_ATTACHMENT_MAX_FILESIZE' ) ) { ?>
				<p><?php printf( __( 'You\'ve set it to: %s', 'hmbkp' ), '<code>' . HMBKP_ATTACHMENT_MAX_FILESIZE . '</code>' ); ?></p>
				<?php } ?>

				<p><?php printf( __( 'The maximum filesize of your backup that will be attached to your notification emails . Defaults to %s.', 'hmbkp' ), '<code>10MB</code>' ); ?> <?php _e( 'e.g.', 'hmbkp' ); ?> <code>define( 'HMBKP_ATTACHMENT_MAX_FILESIZE', '25MB' );</code></p>

			</td>

		</tr>

	<?php }

	/**
	 * The sentence fragment that is output as part of the schedule sentence
	 *
	 * @return string
	 */
	public function display() {

		if ( $emails = $this->get_email_address_array() ) {

			$email = '<code>' . implode( '</code>, <code>', array_map( 'esc_html', $emails ) ) . '</code>';

			return sprintf( __( 'Send an email notification to %s', 'hmbkp' ), $email );

		}

		return '';

	}

	/**
	 * Used to determine if the service is in use or not
	 */
	public function is_service_active() {
		return (bool) $this->get_email_address_array();
	}

	/**
	 * Validate the email and return an error if validation fails
	 *
	 * @param  array  &$new_data Array of new data, passed by reference
	 * @param  array  $old_data  The data we are replacing
	 * @return null|array        Null on success, array of errors if validation failed
	 */
	public function update( &$new_data, $old_data ) {

		$errors = array();

		if ( isset( $new_data['email'] ) ) {

			if ( ! empty( $new_data['email'] ) ) {

				foreach ( explode( ',', $new_data['email'] ) as $email ) {

					$email = trim( $email );

					if ( ! is_email( $email ) ) {
						$errors['email'] = sprintf( __( '%s isn\'t a valid email',  'hmbkp' ), $email );
					}

					if ( ! empty( $errors['email'] ) ) {
						$new_data['email'] = '';
					}

				}

			}

			return $errors;

		}

	}

	/**
	 * Get an array or validated email address's
	 * @return array An array of validated email address's
	 */
	private function get_email_address_array() {

		$emails = array_map( 'trim', explode( ',', $this->get_field_value( 'email' ) ) );

		return array_filter( array_unique( $emails ), 'is_email' );

	}

	/**
	 * Fire the email notification on the hmbkp_backup_complete
	 *
	 * @see  HM_Backup::do_action
	 * @param  string $action The action received from the backup
	 * @return void
	 */
	public function action( $action ) {

		if ( $action == 'hmbkp_backup_complete' && $this->get_email_address_array() ) {

			$file = $this->schedule->get_archive_filepath();

			$sent = false;

			$download = add_query_arg( 'hmbkp_download', base64_encode( $file ), HMBKP_ADMIN_URL );
			$domain   = parse_url( home_url(), PHP_URL_HOST ) . parse_url( home_url(), PHP_URL_PATH );

			$headers  = 'From: BackUpWordPress <' . apply_filters( 'hmbkp_from_email', get_bloginfo( 'admin_email' ) ) . '>' . "\r\n";

			// The backup failed, send a message saying as much
			if ( ! file_exists( $file ) && ( $errors = array_merge( $this->schedule->get_errors(), $this->schedule->get_warnings() ) ) ) {

				$error_message = '';

				foreach ( $errors as $error_set )
					$error_message .= implode( "\n - ", $error_set );

				if ( $error_message )
					$error_message = ' - ' . $error_message;

				$subject = sprintf( __( 'Backup of %s Failed', 'hmbkp' ), $domain );

				$message = sprintf( __( 'BackUpWordPress was unable to backup your site %1$s.', 'hmbkp' ) . "\n\n" . __( 'Here are the errors that we\'re encountered:', 'hmbkp' ) . "\n\n" . '%2$s' . "\n\n" . __( 'If the errors above look like Martian, forward this email to %3$s and we\'ll take a look', 'hmbkp' ) . "\n\n" . __( "Kind Regards,\nThe Apologetic BackUpWordPress Backup Emailing Robot", 'hmbkp' ), home_url(), $error_message, 'support@hmn.md' );

				wp_mail( $this->get_email_address_array(), $subject, $message, $headers );

				return;

			}

			$subject = sprintf( __( 'Backup of %s', 'hmbkp' ), $domain );

			// If it's larger than the max attachment size limit assume it's not going to be able to send the backup
			if ( @filesize( $file ) < hmbkp_get_max_attachment_size() ) {

				$message = sprintf( __( 'BackUpWordPress has completed a backup of your site %1$s.', 'hmbkp' ) . "\n\n" . __( 'The backup file should be attached to this email.', 'hmbkp' ) . "\n\n" . __( 'You can download the backup file by clicking the link below:', 'hmbkp' ) . "\n\n" . '%2$s' . "\n\n" . __( "Kind Regards,\nThe Happy BackUpWordPress Backup Emailing Robot", 'hmbkp' ), home_url(),  $download );

				$sent = wp_mail( $this->get_email_address_array(), $subject, $message, $headers, $file );

			}

			// If we didn't send the email above then send just the notification
			if ( ! $sent ) {

				$message = sprintf( __( 'BackUpWordPress has completed a backup of your site %1$s.', 'hmbkp' ) . "\n\n" . __( 'Unfortunately the backup file was too large to attach to this email.', 'hmbkp' ) . "\n\n" . __( 'You can download the backup file by clicking the link below:', 'hmbkp' ) . "\n\n" . '%2$s' . "\n\n" . __( "Kind Regards,\nThe Happy BackUpWordPress Backup Emailing Robot", 'hmbkp' ), home_url(), $download );

				wp_mail( $this->get_email_address_array(), $subject, $message, $headers );

			}

		}

	}

	public static function intercom_data() { return array(); }

	public static function intercom_data_html() {}

}

// Register the service
HMBKP_Services::register( __FILE__, 'HMBKP_Email_Service' );
