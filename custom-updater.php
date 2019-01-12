<?php
/**
 * Plugin Name:     Custom Updater
 * Plugin URI:      https://www.ecotechie.io
 * Description:     Allows for customized/automatic plugin updating.
 * Author:          Sergio Scabuzzo
 * Author URI:      https://www.ecotechie.io
 * Text Domain:     custom-updater
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Custom_Updater
 */

/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

if ( ! function_exists( 'plugins_api' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
}
/**
 * Custom option and settings
 */
function custom_updater_settings_init() {
	// register a new setting for "custom-updater" page.
	register_setting( 'custom-updater', 'custom_updater_options' );

	// register a new section in the "custom-updater" page.
	add_settings_section(
		'custom_updater_section_developers',
		__( 'The Matrix has you.', 'custom-updater' ),
		'custom_updater_section_developers_cb',
		'custom-updater'
	);

	// register a new field in the "custom_updater_section_developers" section, inside the "custom-updater" page.
	add_settings_field(
		'custom_updater_field_pill', // as of WP 4.6 this value is used only internally
		// use $args' label_for to populate the id inside the callback.
		__( 'Pill', 'custom-updater' ),
		'custom_updater_field_pill_cb',
		'custom-updater',
		'custom_updater_section_developers',
		[
			'label_for'                  => 'custom_updater_field_pill',
			'class'                      => 'custom_updater_row',
			'custom_updater_custom_data' => 'custom',
		]
	);
}

/**
 * Register our custom_updater_settings_init to the admin_init action hook.
 */
add_action( 'admin_init', 'custom_updater_settings_init' );

/**
 * Custom option and settings:
 * callback functions
 */

// Developers section cb.

// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.
function custom_updater_section_developers_cb( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Follow the white rabbit.', 'custom-updater' ); ?></p>
	<?php
}

// pill field cb.

// field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// WordPress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.
function custom_updater_field_pill_cb( $args ) {
	// Get the value of the setting we've registered with register_setting().
	$options = get_option( 'custom_updater_options' );
	// Output the field.
	?>
	<select id="<?php echo esc_attr( $args['label_for'] ); ?>"
	data-custom="<?php echo esc_attr( $args['custom_updater_custom_data'] ); ?>"
	name="custom_updater_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	>
		<option value="red" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'red', false ) ) : ( '' ); ?>>
			<?php esc_html_e( 'red pill', 'custom-updater' ); ?>
		</option>
		<option value="blue" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'blue', false ) ) : ( '' ); ?>>
			<?php esc_html_e( 'blue pill', 'custom-updater' ); ?>
		</option>
	</select>
	<p class="description">
		<?php esc_html_e( 'You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.', 'custom-updater' ); ?>
	</p>
	<p class="description">
		<?php esc_html_e( 'You take the red pill and you stay in Wonderland and I show you how deep the rabbit-hole goes.', 'custom-updater' ); ?>
	</p>
	<?php
}

/**
 * Top level menu.
 */
function custom_updater_options_page() {
	// Add top level menu page.
	add_menu_page(
		'Custom Updater Options',
		'Custom Updater',
		'manage_options',
		'custom-updater',
		'custom_updater_options_page_html'
	);
}

/**
 * Register our custom_updater_options_page to the admin_menu action hook.
 */
add_action( 'admin_menu', 'custom_updater_options_page' );

/**
 * Top level menu:
 * callback functions
 */
function custom_updater_options_page_html() {
	// Check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Add error/update messages.

	// check if the user have submitted the settings
	// WordPress will add the "settings-updated" $_GET parameter to the url.
	if ( isset( $_GET['settings-updated'] ) ) {
		// Add settings saved message with the class of "updated".
		add_settings_error( 'custom_updater_messages', 'custom_updater_message', __( 'Settings Saved', 'custom-updater' ), 'updated' );
	}

	// Show error/update messages.
	settings_errors( 'custom_updater_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// Output security fields for the registered setting "custom-updater".
			settings_fields( 'custom-updater' );
			// output setting sections and their fields
			// (sections are registered for "custom-updater", each field is registered to a specific section).
			do_settings_sections( 'custom-updater' );
			// Output save settings button.
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php

	echo '<h2>Sample from get_plugins_installed()</h2>';
	echo '<pre>';
	echo '[akismet/akismet.php] => stdClass Object
        (
            [Name] => Akismet Anti-Spam
            [PluginURI] => https://akismet.com/
            [Version] => 4.0.8
            [Description] => Used by millions, Akismet is quite possibly the best way in the world to protect your blog from spam. It keeps your site protected even while you sleep. To get started: activate the Akismet plugin and then go to your Akismet Settings page to set up your API key.
            [Author] => Automattic
            [AuthorURI] => https://automattic.com/wordpress-plugins/
            [TextDomain] => akismet
            [DomainPath] =>
            [Network] =>
            [Title] => Akismet Anti-Spam
            [AuthorName] => Automattic
            [update] => stdClass Object
                (
                    [id] => w.org/plugins/akismet
                    [slug] => akismet
                    [plugin] => akismet/akismet.php
                    [new_version] => 4.1
                    [url] => https://wordpress.org/plugins/akismet/
                    [package] => https://downloads.wordpress.org/plugin/akismet.4.1.zip
                    [icons] => Array
                        (
                            [2x] => https://ps.w.org/akismet/assets/icon-256x256.png?rev=969272
                            [1x] => https://ps.w.org/akismet/assets/icon-128x128.png?rev=969272
                        )

                    [banners] => Array
                        (
                            [1x] => https://ps.w.org/akismet/assets/banner-772x250.jpg?rev=479904
                        )

                    [banners_rtl] => Array
                        (
                        )

                    [tested] => 5.0.3
                    [requires_php] =>
                    [compatibility] => stdClass Object
                        (
                        )

                )

        )';
	echo '</pre>';

	function get_plugins_installed() {
		// $plugins_installed = get_plugins();
		$plugins_installed = get_plugin_updates();
		// echo '<pre>' . print_r( $plugins_installed, true ) . '</pre>';

		foreach ( ( array ) $plugins_installed as $plugin => $data ) {
			echo '<br>===============================<br>';
			echo '<br><b>Plugin Name : </b>' . $data->Name . '<br>';
			echo '<br><b>Plugin Version : </b>' . $data->Version . '<br>';
			echo '<br><b>Plugin Path : </b>' . $plugin . '<br>';
			echo '<br>Last modified  : ' . date( 'F d Y H:i:s.', filemtime( WP_PLUGIN_DIR . '/' . $plugin ) ) . '<br>';
			/** Prepare our query */
			$call_api = plugins_api( 'plugin_information', array( 'slug' => $data->update->slug ) );
			?>
			<section>
				<?php
				/** Check for Errors & Display the results */
				if ( is_wp_error( $call_api ) ) {
					echo '<pre>' . print_r( $call_api->get_error_message(), true ) . '</pre>';
				} else {
					echo '<pre>' . print_r( $call_api, true ) . '</pre>';
					if ( ! empty( $call_api->downloaded ) ) {
						echo '<p>Downloaded: ' . print_r( $call_api->downloaded, true ) . ' times.</p>';
					}
				}
				?>
			</section>
			<?php
		}
	}
	get_plugins_installed();
}
