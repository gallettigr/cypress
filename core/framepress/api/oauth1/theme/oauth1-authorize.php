<?php
login_header( __('Authorize', 'oauth'), '', $errors);
$current_user = wp_get_current_user();
$url = site_url( 'wp-login.php?action=oauth1_authorize', 'login_post' );
?>
<form name="oauth1_authorize_form" id="oauth1_authorize_form" action="<?php echo esc_url( $url ); ?>" method="post">

	<h2 class="login-title"><?php echo esc_html( sprintf( __('Connect %1$s'), $consumer->post_title ) ) ?></h2>
	<div class="login-info row">
		<div class="avatar col-xs-4"><?php echo get_avatar( $current_user->ID, '78' ); ?></div>
		<div class="notice col-xs-8"><?php printf(__( 'Howdy <strong>%1$s</strong>, would like to connect to %3$s.' ), $current_user->user_login, $consumer->post_title,
				get_bloginfo( 'name' )) ?></div>
	</div>

	<?php
	/**
	 * Fires inside the lostpassword <form> tags, before the hidden fields.
	 *
	 * @since 2.1.0
	 */
	do_action( 'oauth1_authorize_form', $consumer ); ?>
	<p class="nav">
		<button type="submit" name="wp-submit" value="authorize" class="btn btn-primary button"><?php _e('Submit'); ?></button>
		<button type="submit" name="wp-submit" value="cancel" class="btn btn-primary button"><?php _e('Cancel'); ?></button>
	</p>

</form>

<p id="nav">
<a href="<?php echo esc_url( wp_login_url( $url, true ) ); ?>"><?php _e( 'Switch user' ) ?></a>
<?php
if ( get_option( 'users_can_register' ) ) :
	$registration_url = sprintf( '<a href="%s">%s</a>', esc_url( wp_registration_url() ), __( 'Register' ) );
	/**
	 * Filter the registration URL below the login form.
	 *
	 * @since 1.5.0
	 *
	 * @param string $registration_url Registration URL.
	 */
	echo ' | ' . apply_filters( 'register', $registration_url );
endif;
?>
</p>

<?php
login_footer();
