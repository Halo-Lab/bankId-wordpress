<?php
add_action('admin_menu', 'bankID_menu' ); 

function bankID_menu() {
	add_menu_page(
		'Bank ID Registration',
		'Bank ID',
		'manage_options',
		'bankid-registration',
		'bankID_admin_form',
		plugins_url( 'bankID-registration/admin/img/bankid-admin.svg' )
	);
	add_action( 'admin_init', 'update_bankID_info' );
}

add_filter( 'plugin_action_links', 'bankID_plugin_action_links', 10, 2 );

function bankID_plugin_action_links( $links, $file ) {

	$settings_link = wpcf7_link(
		menu_page_url( 'bankid-registration', false ),
		'Settings'
	);

	array_unshift( $links, $settings_link );

	return $links;
}

function update_bankID_info () {
	register_setting('bankid-registration-key-settings', 'bankid-registration_key');
	register_setting('bankid-registration-key-settings', 'bankid-registration_test_api');
}

function bankID_admin_form(){
	?>
	<h1>Bank ID Settinsg</h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'bankid-registration-key-settings' ); ?>
		<?php do_settings_sections( 'bankid-registration-key-settings' ); ?>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">Enable test API:</th>
				<td>
					<input type="checkbox" name="bankid-registration_test_api" id="bankid-registration_test_api" value="1" <?php checked( get_option('bankid-registration_test_api'), 1 ); ?>>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Your Authorization Key:</th>
				<td><input type="text" name="bankid-registration_key" id="bankid-registration_key" value="<?php echo get_option('bankid-registration_key'); ?>"/></td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>

	<?php
}