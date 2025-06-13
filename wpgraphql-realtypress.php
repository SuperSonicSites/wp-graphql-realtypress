<?php
/**
 * Plugin Name: WPGraphQL for RealtyPress
 * Description : Exposes RealtyPress data through the WPGraphQL schema.
 * Version     : 0.1.2
 * Author      : Your Name
 * License     : GPL‑2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper: best‑effort detection of RealtyPress (Lite or Premium).
 *
 * Checks constants, classes, functions, and—last resort—the existence
 * of the core wp_rps_property table.
 *
 * @return bool
 */
function wprp_realtypress_is_present() : bool {

	global $wpdb;

	$table_exists = (bool) $wpdb->get_var(
		$wpdb->prepare(
			'SHOW TABLES LIKE %s',
			$wpdb->esc_like( "{$wpdb->prefix}rps_property" )
		)
	);

	return
		defined( 'RPS_PLUGIN_VERSION' )  || // Premium (current)
		defined( 'RPS_VERSION' )         || // Lite
		class_exists( '\RealtyPressPremium' )
		|| function_exists( 'rps_init' ) // Legacy bootstrap
		|| $table_exists;                // Fallback (user renamed constants)
}

/**
 * Defer bootstrapping until **init** so every other plugin has already
 * loaded.  This avoids a race where our earlier test ran before
 * RealtyPress defined its constants.
 */
add_action(
	'init',
	function () {

		if ( ! class_exists( '\WPGraphQL' ) ) {
			// WPGraphQL missing
			add_action(
				'admin_notices',
				function () {
					printf(
						'<div class="notice notice-error"><p>%s</p></div>',
						esc_html__(
							'WPGraphQL for RealtyPress requires the “WPGraphQL” plugin to be active.',
							'wpgraphql-realtypress'
						)
					);
				}
			);
			return;
		}

		if ( ! wprp_realtypress_is_present() ) {
			// RealtyPress missing
			add_action(
				'admin_notices',
				function () {
					printf(
						'<div class="notice notice-error"><p>%s</p></div>',
						esc_html__(
							'WPGraphQL for RealtyPress couldn’t detect RealtyPress. Please activate either RealtyPress Premium or RealtyPress Lite.',
							'wpgraphql-realtypress'
						)
					);
				}
			);
			return;
		}

		// ------------------------------------------------------------------
		//  All dependencies satisfied — load plugin modules.
		// ------------------------------------------------------------------
		foreach (
			[
				'includes/types.php',
				'includes/loaders.php',
				'includes/resolvers.php',
				'includes/connections.php',
			]
			as $module
		) {
			require_once plugin_dir_path( __FILE__ ) . $module;
		}
	},
	/* priority */ 20   // After most plugins’ own init callbacks
);

