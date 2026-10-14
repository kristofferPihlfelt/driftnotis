<?php
/**
 * Plugin Name:       Driftnotis
 * Plugin URI:        https://wordpressutveckling.se/bygg-ditt-forsta-wordpress-plugin/
 * Description:       Visar en meddelanderad högst upp på sajten. Text, på/av och färg styrs under Inställningar > Driftnotis.
 * Version:           1.0.0
 * Requires at least: 6.7
 * Requires PHP:      8.2
 * Author:            Pihlfelt & Vänner
 * Author URI:        https://pihlfelt.se
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       driftnotis
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default values. Used by register_setting() and as a fallback on output.
 */
function driftnotis_defaults() {
	return array(
		'text'  => '',
		'aktiv' => 0,
		'farg'  => '#1e3a5f',
	);
}

/**
 * Saved settings merged with the defaults, so every key always exists.
 */
function driftnotis_settings() {
	return wp_parse_args( (array) get_option( 'driftnotis', array() ), driftnotis_defaults() );
}

/* ---- Settings page --------------------------------------------------- */

add_action( 'admin_menu', 'driftnotis_add_page' );
function driftnotis_add_page() {
	add_options_page(
		__( 'Driftnotis', 'driftnotis' ),
		__( 'Driftnotis', 'driftnotis' ),
		'manage_options',
		'driftnotis',
		'driftnotis_render_page'
	);
}

function driftnotis_render_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'driftnotis' );
			do_settings_sections( 'driftnotis' );
			submit_button( __( 'Spara', 'driftnotis' ) );
			?>
		</form>
	</div>
	<?php
}

add_action( 'admin_init', 'driftnotis_register_settings' );
function driftnotis_register_settings() {
	register_setting(
		'driftnotis',
		'driftnotis',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'driftnotis_sanitize',
			'default'           => driftnotis_defaults(),
		)
	);

	add_settings_section( 'driftnotis_main', '', 'driftnotis_section_intro', 'driftnotis' );

	add_settings_field( 'driftnotis_text', __( 'Meddelande', 'driftnotis' ), 'driftnotis_field_text', 'driftnotis', 'driftnotis_main', array( 'label_for' => 'driftnotis_text' ) );
	add_settings_field( 'driftnotis_aktiv', __( 'Visa raden', 'driftnotis' ), 'driftnotis_field_aktiv', 'driftnotis', 'driftnotis_main', array( 'label_for' => 'driftnotis_aktiv' ) );
	add_settings_field( 'driftnotis_farg', __( 'Bakgrundsfärg', 'driftnotis' ), 'driftnotis_field_farg', 'driftnotis', 'driftnotis_main', array( 'label_for' => 'driftnotis_farg' ) );
}

function driftnotis_section_intro() {
	echo '<p>' . esc_html__( 'Raden visas högst upp på alla sidor så länge den är påslagen och har en text.', 'driftnotis' ) . '</p>';
}

function driftnotis_field_text() {
	$s = driftnotis_settings();
	printf(
		'<input type="text" id="driftnotis_text" name="driftnotis[text]" value="%s" class="regular-text" maxlength="200">',
		esc_attr( $s['text'] )
	);
}

function driftnotis_field_aktiv() {
	$s = driftnotis_settings();
	printf(
		'<input type="checkbox" id="driftnotis_aktiv" name="driftnotis[aktiv]" value="1" %s>',
		checked( 1, $s['aktiv'], false )
	);
}

function driftnotis_field_farg() {
	$s = driftnotis_settings();
	printf(
		'<input type="color" id="driftnotis_farg" name="driftnotis[farg]" value="%s">',
		esc_attr( $s['farg'] )
	);
}

/**
 * Runs on every save, before anything reaches the database.
 * An unchecked checkbox is simply missing from $input, hence empty().
 */
function driftnotis_sanitize( $input ) {
	$input    = (array) $input;
	$defaults = driftnotis_defaults();
	$farg     = sanitize_hex_color( $input['farg'] ?? '' );

	return array(
		'text'  => sanitize_text_field( $input['text'] ?? '' ),
		'aktiv' => empty( $input['aktiv'] ) ? 0 : 1,
		'farg'  => $farg ? $farg : $defaults['farg'],
	);
}

/* ---- Front end ------------------------------------------------------- */

add_action( 'wp_body_open', 'driftnotis_print' );
function driftnotis_print() {
	$s = driftnotis_settings();
	if ( ! $s['aktiv'] || '' === $s['text'] ) {
		return;
	}
	echo '<div class="driftnotis" role="status">' . esc_html( $s['text'] ) . '</div>';
}

add_action( 'wp_enqueue_scripts', 'driftnotis_styles' );
function driftnotis_styles() {
	$s = driftnotis_settings();
	if ( ! $s['aktiv'] || '' === $s['text'] ) {
		return;
	}
	// CSS has no escaping function, so only a value that passes sanitize_hex_color() again goes in.
	$farg = sanitize_hex_color( $s['farg'] );
	$farg = $farg ? $farg : driftnotis_defaults()['farg'];

	// A handle without src prints nothing by itself; the inline CSS is the whole stylesheet.
	wp_register_style( 'driftnotis', false, array(), '1.0.0' );
	wp_enqueue_style( 'driftnotis' );
	wp_add_inline_style( 'driftnotis', '.driftnotis{background:' . $farg . ';color:#fff;padding:.6em 1em;text-align:center;font-size:.95em}' );
}

/* ---- Translations and uninstall ------------------------------------- */

add_action( 'init', 'driftnotis_textdomain' );
function driftnotis_textdomain() {
	// Only needed for .mo files shipped inside the plugin folder. Translations from
	// wordpress.org are picked up automatically since WordPress 4.6.
	load_plugin_textdomain( 'driftnotis', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

register_activation_hook( __FILE__, 'driftnotis_activate' );
function driftnotis_activate() {
	// register_uninstall_hook() writes to an option, so it belongs here and not at file load.
	register_uninstall_hook( __FILE__, 'driftnotis_uninstall' );
}

function driftnotis_uninstall() {
	delete_option( 'driftnotis' );
}
