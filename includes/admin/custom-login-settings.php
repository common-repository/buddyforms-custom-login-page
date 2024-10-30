<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }


add_filter( 'buddyforms_admin_tabs', 'buddyforms_custom_login_admin_tab', 1, 1 );
function buddyforms_custom_login_admin_tab( $tabs ) {

	$tabs['custom_login'] = 'Custom Login';

	return $tabs;

}

add_action( 'buddyforms_settings_page_tab', 'buddyforms_custom_login_settings_page_tab' );
function buddyforms_custom_login_settings_page_tab( $tab ) {
	global $buddyforms;
	if ( $tab != 'custom_login' ) {
		return $tab;
	}
	$custom_login_settings = get_option( 'buddyforms_custom_login_settings' );

	$pages = buddyforms_get_all_pages( 'id', 'settings' );
	$allowed = array(
		'select' => array(
			'id'    => array(),
			'class'    => array(),
			'name'  => array(),
		),
		'option' => array(
			'value'    => array(),
			'selected' => array(),
			'class'    => array(),
			'id'    => array(),
			'value'    => array(),
		),
	);
	?>

	<div class="metabox-holder">
		<div class="postbox buddyforms-metabox">
			<div class="inside">
				<form method="post" action="options.php">

					<?php settings_fields( 'buddyforms_custom_login_settings' ); ?>

					<table class="form-table">

						<tbody>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="buddyforms_custom_login">Login Page</label>
								<span class="buddyforms-help-tip"></span></th>
							<td class="forminp forminp-select">
								<?php
								$login_page = empty( $custom_login_settings['login_page'] ) ? '' : $custom_login_settings['login_page'];
								if ( function_exists( 'buddyforms_get_all_pages_dropdown' ) ) {
									$pages_dropdown = buddyforms_get_all_pages_dropdown( 'buddyforms_custom_login_settings[login_page]', $login_page, 'buddyforms_custom_login_login_page' );
									if ( ! empty( $pages_dropdown ) ) {
										echo wp_kses( $pages_dropdown, $allowed );
									}
								} else {
									if ( isset( $pages ) && is_array( $pages ) ) {
										echo '<select name="buddyforms_custom_login_settings[login_page]" id="buddyforms_custom_login_login_page">';
										$pages['none'] = 'WordPress Default';
										foreach ( $pages as $page_id => $page_name ) {
											if ( ! empty( $page_name ) ) {
												echo '<option ' . selected( $login_page, $page_id ) . 'value="' . esc_attr( $page_id ) . '">' . esc_html( $page_name ) . '</option>';
											}
										}
										echo '</select>';
									}
								}
								?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="buddyforms_custom_login_lavel_1">Display Login Form?</label>
								<span class="buddyforms-help-tip"></span></th>
							<td class="forminp forminp-select">
								<?php
								$display_login_form = empty( $custom_login_settings['display_login_form'] ) ? '' : $custom_login_settings['display_login_form'];
								?>
								<select name="buddyforms_custom_login_settings[display_login_form]" id="buddyforms_custom_login_display_login_form">
									<option <?php selected( $display_login_form, 'overwrite' ); ?> value="overwrite">Overwrite Page Content</option>
									<option <?php selected( $display_login_form, 'above' ); ?> value="above">Above the Content</option>
									<option <?php selected( $display_login_form, 'under' ); ?> value="under">Under the Content</option>
									<option <?php selected( $display_login_form, 'shortcode' ); ?> value="shortcode">I use the Shortcode</option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="buddyforms_custom_login_lavel_1">Display Registration Link?</label>
								<span class="buddyforms-help-tip"></span></th>
							<td>
								<?php
								$register_page = empty( $custom_login_settings['register_page'] ) ? '' : $custom_login_settings['register_page'];
								if ( isset( $pages ) && is_array( $pages ) ) {
									echo '<select name="buddyforms_custom_login_settings[register_page]" id="buddyforms_registration_form">';
									echo '<option value="default">' . esc_html__( 'WordPress Default', 'buddyforms' ) . '</option>';
									echo '<option value="none">' . esc_html__( 'None', 'buddyforms' ) . '</option>';
									foreach ( $pages as $page_id => $page_name ) {
										if ( ! empty( $page_name ) ) {
											echo '<option ' . selected( $register_page, $page_id ) . 'value="' . esc_attr( $page_id ) . '">' . esc_html( $page_name ) . '</option>';
										}
									}
									echo '</select>';
								}
								?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="buddyforms_custom_login_lavel_1">Redirect after Login</label>
								<span class="buddyforms-help-tip"></span></th>
							<td class="forminp forminp-select">
								<?php
								$redirect_page = empty( $custom_login_settings['redirect_page'] ) ? '' : $custom_login_settings['redirect_page'];
								if ( function_exists( 'buddyforms_get_all_pages_dropdown' ) ) {
									$pages_dropdown = buddyforms_get_all_pages_dropdown( 'buddyforms_custom_login_settings[redirect_page]', $redirect_page, 'buddyforms_custom_login_redirect_page', __( 'WordPress Default', 'buddyforms' ), 'default' );
									if ( ! empty( $pages_dropdown ) ) {
										echo wp_kses( $pages_dropdown, $allowed );
									}
								} else {
									if ( isset( $pages ) && is_array( $pages ) ) {
										echo '<select name="buddyforms_custom_login_settings[redirect_page]" id="buddyforms_custom_login_redirect_page">';
										$pages['default'] = 'WordPress Default';
										foreach ( $pages as $page_id => $page_name ) {
											if ( ! empty( $page_name ) ) {
												echo '<option ' . selected( $redirect_page, $page_id ) . 'value="' . esc_attr( $page_id ) . '">' . esc_html( $page_name ) . '</option>';
											}
										}
										echo '</select>';
									}
								}
								?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="buddyforms_custom_login_redirect_logged_off_user">Redirect logged off users to a login and create a private site or network</label>
								<span class="buddyforms-help-tip"></span></th>
							<td class="forminp forminp-select">
								<?php
								$redirect_logged_off_user = empty( $custom_login_settings['redirect_logged_off_user'] ) ? 'No' : $custom_login_settings['redirect_logged_off_user'];

								echo '<select name="buddyforms_custom_login_settings[redirect_logged_off_user]" id="buddyforms_custom_login_redirect_logged_off_user">';
								echo '<option ' . selected( $redirect_logged_off_user, 'No' ) . 'value="No">No</option>';
								echo '<option ' . selected( $redirect_logged_off_user, 'Yes' ) . 'value="Yes">Yes</option>';
								echo '</select>';
								?>
							</td>
						</tr>
						<tr valign="top" class="bfcl_use_custom_url">
							<th scope="row" class="titledesc">
								<label for="buddyforms_custom_login_use_custom_redirect_url">Use custom url to redirect?</label>
								<span class="buddyforms-help-tip"></span>
							</th>
							<td>
								<?php
								$use_custom_redirect_url = ! empty( $custom_login_settings['use_custom_redirect_url'] ) ? 'checked' : '';
								echo "<input type='checkbox' " . esc_attr( $use_custom_redirect_url ) . " name='buddyforms_custom_login_settings[use_custom_redirect_url]' id='buddyforms_custom_login_use_custom_redirect_url'>";
								?>
							</td>
						</tr>
						<tr valign="top" class="bfcl_use_custom_url bfcl_set_custom_url">
							<th scope="row" class="titledesc">
								<label for="buddyforms_custom_login_set_custom_redirect_url">Plese enter your custom url</label>
								<span class="buddyforms-help-tip"></span>
							</th>
							<td>
								<?php
								$set_custom_redirect_url = ! empty( $custom_login_settings['set_custom_redirect_url'] ) ? $custom_login_settings['set_custom_redirect_url'] : '';
								echo "<input type='url' value='" . esc_attr( $set_custom_redirect_url ) . "' name='buddyforms_custom_login_settings[set_custom_redirect_url]' id='buddyforms_custom_login_set_custom_redirect_url' placeholder='https://example.com' style='width:400px !important;'>";
								?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="buddyforms_custom_login_public_accessible">Public Accessible Pages</label>
								<span class="buddyforms-help-tip"></span></th>
							<td class="forminp forminp-select">
								<?php
								unset( $pages['none'] );
								$public_accessible_pages = empty( $custom_login_settings['public_accessible_pages'] ) ? array() : $custom_login_settings['public_accessible_pages'];
								if ( isset( $pages ) && is_array( $pages ) ) {
									echo '<select class="bf-select2" multiple name="buddyforms_custom_login_settings[public_accessible_pages][]" id="buddyforms_custom_login_public_accessible_pages">';
									foreach ( $pages as $page_id => $page_name ) {
										if ( ! empty( $page_name ) ) {

											$public_accessible_selcted = '';
											if ( in_array( $page_id, $public_accessible_pages ) ) {
												$public_accessible_selcted = $page_id;
											}

											echo '<option ' . selected( $public_accessible_selcted, $page_id ) . 'value="' . esc_attr( $page_id ) . '">' . esc_html( $page_name ) . '</option>';
										}
									}
									echo '</select>';
								}
								?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="buddyforms_custom_login_public_accessible">Public Accessible Post Types</label>
								<span class="buddyforms-help-tip"></span></th>
							<td class="forminp forminp-select">
								<?php
								$public_accessible_post_types = empty( $custom_login_settings['public_accessible_post_types'] ) ? array() : $custom_login_settings['public_accessible_post_types'];
								if ( isset( $pages ) && is_array( $pages ) ) {
									echo '<select class="bf-select2" multiple name="buddyforms_custom_login_settings[public_accessible_post_types][]" id="buddyforms_custom_login_public_accessible_post_types">';
									foreach ( get_post_types( array( 'public' => true ), 'names' ) as $post_type ) {
										$public_accessible_post_types_selcted = '';
										if ( in_array( $post_type, $public_accessible_post_types ) ) {
											$public_accessible_post_types_selcted = $post_type;
										}
										echo '<option ' . selected( $public_accessible_post_types_selcted, $post_type ) . 'value="' . esc_attr( $post_type ) . '">' . esc_html( $post_type ) . '</option>';
									}
									echo '</select>';
								}
								?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="buddyforms_custom_login_lavel_1">Set "Remember Me" checkbox as checked by default</label>
								<span class="buddyforms-help-tip"></span>
							</th>
							<td>
								<?php
								$remember_me_as_default = ! empty( $custom_login_settings['remember_me_as_default'] ) ? 'checked' : '';
								echo "<input type='checkbox' " . esc_attr( $remember_me_as_default ) . " name='buddyforms_custom_login_settings[remember_me_as_default]' id='buddyforms_custom_login_settings_remember_me'>"
								?>
							</td>
						</tr>
						</tbody>
					</table>
					<?php submit_button(); ?>

				</form>
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- .metabox-holder -->
	<?php
}

add_action( 'admin_init', 'buddyforms_custom_login_register_option' );
function buddyforms_custom_login_register_option() {
	// creates our settings in the options table
	register_setting( 'buddyforms_custom_login_settings', 'buddyforms_custom_login_settings', 'buddyforms_custom_login_settings_default_sanitize' );
}

// Sanitize the Settings
function buddyforms_custom_login_settings_default_sanitize( $new ) {
	return $new;
}

add_action( 'admin_enqueue_scripts', 'buddyforms_custom_login_add_admin_js' );
function buddyforms_custom_login_add_admin_js( $hook ) {
	wp_register_script( 'admin_js', dirname( plugin_dir_url( __FILE__ ) ) . '/js/admin.js', array( 'jquery' ), '1.1', true );
	wp_enqueue_script( 'admin_js' );
}
