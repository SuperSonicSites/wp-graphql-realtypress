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

                $filters = $args['where'] ?? [];

                if ( isset( $filters['City'] ) && '' !== $filters['City'] ) {
                        $where[]    = 'City = %s';
                        $sql_args[] = $filters['City'];
                }

                if ( isset( $filters['minPrice'] ) && '' !== $filters['minPrice'] ) {
                        $where[]    = 'Price >= %f';
                        $sql_args[] = $filters['minPrice'];
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


