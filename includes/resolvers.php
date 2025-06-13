<?php
/**
 * Register Root‑level connections for WPGraphQL‑RealtyPress.
 */

namespace WPGraphQL\RealtyPress;

defined( 'ABSPATH' ) || exit;

use WPGraphQL\Data\Connection\ConnectionHelper;
use WPGraphQL\AppContext;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Hook into schema generation.
 */
add_action(
	'graphql_register_types',
	function () {

		global $wpdb;

		/*
		 * ------------------------------------------------------------------
		 *  <RootQuery>.properties
		 * ------------------------------------------------------------------
		 *  Now delegates to Resolvers::resolve_properties_connection
		 *  to avoid duplicated SQL logic.
		 */
		register_graphql_connection(
			[
				'fromType'           => 'RootQuery',
				'toType'             => 'Property',
				'fromFieldName'      => 'properties',
				'connectionTypeName' => 'Properties',
				'args'               => [
					'where' => [
						'type'        => 'PropertyFilterInput',
						'description' => __( 'Filter properties by field values', 'wpgraphql-realtypress' ),
					],
				],
				'resolve'            => [ Resolvers::class, 'resolve_properties_connection' ],
			]
		);

		/*
		 * ------------------------------------------------------------------
		 *  <RootQuery>.allPropertyPhotos
		 * ------------------------------------------------------------------
		 */
		register_graphql_connection(
			[
				'fromType'           => 'RootQuery',
				'toType'             => 'PropertyPhoto',
				'fromFieldName'      => 'allPropertyPhotos',
				'connectionTypeName' => 'PropertyPhotos',
				'resolve'            => function ( $root, array $args, AppContext $context, ResolveInfo $info ) use ( $wpdb ) {

					$ids = $wpdb->get_col( "SELECT details_id FROM {$wpdb->prefix}rps_property_photos" );

					return ConnectionHelper::connection_from_ids( $ids, $args, $context, $info );
				},
			]
		);

		/*
		 * <RootQuery>.allPropertyRooms
		 * ------------------------------------------------------------------
		 */
		register_graphql_connection(
			[
				'fromType'           => 'RootQuery',
				'toType'             => 'PropertyRoom',
				'fromFieldName'      => 'allPropertyRooms',
				'connectionTypeName' => 'PropertyRooms',
				'resolve'            => function ( $root, array $args, AppContext $context, ResolveInfo $info ) use ( $wpdb ) {

					$ids = $wpdb->get_col( "SELECT room_id FROM {$wpdb->prefix}rps_property_rooms" );

					return ConnectionHelper::connection_from_ids( $ids, $args, $context, $info );
				},
			]
		);

		/*
		 * <RootQuery>.allRealtyAgents
		 * ------------------------------------------------------------------
		 */
		register_graphql_connection(
			[
				'fromType'           => 'RootQuery',
				'toType'             => 'RealtyAgent',
				'fromFieldName'      => 'allRealtyAgents',
				'connectionTypeName' => 'RealtyAgents',
				'resolve'            => function ( $root, array $args, AppContext $context, ResolveInfo $info ) use ( $wpdb ) {

					$ids = $wpdb->get_col( "SELECT agent_id FROM {$wpdb->prefix}rps_agent" );

					return ConnectionHelper::connection_from_ids( $ids, $args, $context, $info );
				},
			]
		);

		/*
		 * <RootQuery>.allRealtyBoards
		 * ------------------------------------------------------------------
		 */
		register_graphql_connection(
			[
				'fromType'           => 'RootQuery',
				'toType'             => 'RealtyBoard',
				'fromFieldName'      => 'allRealtyBoards',
				'connectionTypeName' => 'RealtyBoards',
				'resolve'            => function ( $root, array $args, AppContext $context, ResolveInfo $info ) use ( $wpdb ) {

					$ids = $wpdb->get_col( "SELECT OrganizationID FROM {$wpdb->prefix}rps_boards" );

					return ConnectionHelper::connection_from_ids( $ids, $args, $context, $info );
				},
			]
		);

		/*
		 * <RootQuery>.allRealtyOffices
		 * ------------------------------------------------------------------
		 */
		register_graphql_connection(
			[
				'fromType'           => 'RootQuery',
				'toType'             => 'RealtyOffice',
				'fromFieldName'      => 'allRealtyOffices',
				'connectionTypeName' => 'RealtyOffices',
				'resolve'            => function ( $root, array $args, AppContext $context, ResolveInfo $info ) use ( $wpdb ) {

					$ids = $wpdb->get_col( "SELECT office_id FROM {$wpdb->prefix}rps_office" );

					return ConnectionHelper::connection_from_ids( $ids, $args, $context, $info );
				},
			]
		);
	}
);




