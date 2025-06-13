<?php
/**
 * Plugin Name: WPGraphQL for RealtyPress
 * Description : Exposes RealtyPress data through the WPGraphQL schema.
 * Version     : 0.1.4
 * Author      : Your Name
 * License     : GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Detect RealtyPress presence (Lite or Premium), falling back to table existence.
 *
 * @return bool
 */
function wprp_realtypress_is_present(): bool {
	global $wpdb;
	static $present;

	if ( isset( $present ) ) {
		return $present;
	}

	$table_like   = $wpdb->esc_like( "{$wpdb->prefix}rps_property" );
	$table_exists = (bool) $wpdb->get_var(
		$wpdb->prepare( 'SHOW TABLES LIKE %s', $table_like )
	);

	$present = (
		defined( 'RPS_PLUGIN_VERSION' )  ||
		defined( 'RPS_VERSION' )         ||
		class_exists( '\RealtyPressPremium' ) ||
		function_exists( 'rps_init' )    ||
		$table_exists
	);

	return $present;
}

/**
 * Delay loading our schema modules until WPGraphQL is initializing its own schema.
 */
add_action(
	'graphql_init',
	function() {
		// 1) WPGraphQL present?
		if ( ! class_exists( '\WPGraphQL' ) ) {
			add_action( 'admin_notices', function() {
				echo '<div class="notice notice-error"><p>';
				esc_html_e( 'WPGraphQL for RealtyPress requires WPGraphQL to be active.', 'wpgraphql-realtypress' );
				echo '</p></div>';
			} );
			return;
		}

		// 2) RealtyPress present?
		if ( ! wprp_realtypress_is_present() ) {
			add_action( 'admin_notices', function() {
				echo '<div class="notice notice-error"><p>';
				esc_html_e( 'WPGraphQL for RealtyPress requires RealtyPress (Lite or Premium) to be active.', 'wpgraphql-realtypress' );
				echo '</p></div>';
			} );
			return;
		}

		// 3) Load all our schema modules
		$base = plugin_dir_path( __FILE__ ) . 'includes/';
		require_once $base . 'types.php';
		require_once $base . 'loaders.php';
		require_once $base . 'resolvers.php';
		require_once $base . 'connections.php';
	}
);
