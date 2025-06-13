<?php
namespace WPGraphQL\RealtyPress;
defined( 'ABSPATH' ) || exit;

use WPGraphQL\Data\Connection\ConnectionHelper;
use WPGraphQL\AppContext;
use GraphQL\Type\Definition\ResolveInfo;

final class Resolvers {

	public static function resolve_properties_connection(
		$root, array $args, AppContext $ctx, ResolveInfo $info
	): array {
		global $wpdb;
		$where    = [];
		$sql_args = [];

		if ( ! empty( $args['where']['City'] ) ) {
			$where[]    = 'City = %s';
			$sql_args[] = $args['where']['City'];
		}
		if ( ! empty( $args['where']['minPrice'] ) ) {
			$where[]    = 'Price >= %f';
			$sql_args[] = $args['where']['minPrice'];
		}

		$where_sql = $where ? 'WHERE ' . implode( ' AND ', $where ) : '';
		$sql = $wpdb->prepare(
			"SELECT property_id FROM {$wpdb->prefix}rps_property {$where_sql}",
			...$sql_args
		);
		$ids = $wpdb->get_col( $sql );
		return ConnectionHelper::connection_from_ids( $ids, $args, $ctx, $info );
	}
}


