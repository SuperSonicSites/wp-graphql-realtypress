<?php
/**
 * Stand‑alone resolvers used by WPGraphQL‑RealtyPress.
 *
 * NOTE: Connection registration now lives in includes/connections.php
 * to avoid double‑registration.  This file only holds re‑usable resolver
 * methods.
 */

namespace WPGraphQL\RealtyPress;

defined( 'ABSPATH' ) || exit;

use WPGraphQL\Data\Connection\ConnectionHelper;
use WPGraphQL\AppContext;
use GraphQL\Type\Definition\ResolveInfo;
use wpdb;

final class Resolvers {

	/**
	 * Resolve a Relay‑style connection of Property nodes.
	 *
	 * Used by <RootQuery>.properties in includes/connections.php.
	 *
	 * @param mixed      $root
	 * @param array      $args
	 * @param AppContext $context
	 * @param ResolveInfo $info
	 *
	 * @return array Relay‑formatted connection array.
	 */
	public static function resolve_properties_connection(
		$root,
		array $args,
		AppContext $context,
		ResolveInfo $info
	) : array {

		/** @var wpdb $wpdb */
		global $wpdb;

		$where    = [];
		$sql_args = [];

		// -- Simple filters --------------------------------------------------
		if ( ! empty( $args['where']['City'] ) ) {
			$where[]    = 'City = %s';
			$sql_args[] = $args['where']['City'];
		}
		if ( ! empty( $args['where']['minPrice'] ) ) {
			$where[]    = 'Price >= %f';
			$sql_args[] = $args['where']['minPrice'];
		}

                $sql = "SELECT property_id FROM {$wpdb->prefix}rps_property";
                if ( $where ) {
                        $sql .= ' WHERE ' . implode( ' AND ', $where );
                }

                // Only call wpdb::prepare when we actually have values to
                // substitute, otherwise the method triggers a _doing_it_wrong
                // warning. Expand $sql_args via the splat operator so each
                // placeholder receives the correct value.
                if ( $sql_args ) {
                        $sql = $wpdb->prepare( $sql, ...$sql_args );
                }

		$ids = $wpdb->get_col( $sql );

		return ConnectionHelper::connection_from_ids( $ids, $args, $context, $info );
	}
}


