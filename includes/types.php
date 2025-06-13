<?php
namespace WPGraphQL\RealtyPress;
defined( 'ABSPATH' ) || exit;

add_action( 'graphql_register_types', function() {
	// Input type
	register_graphql_input_type( 'PropertyFilterInput', [
		'description' => __( 'Filter properties by City or price', 'wpgraphql-realtypress' ),
		'fields'      => [
			'City'     => [ 'type' => 'String' ],
			'minPrice' => [ 'type' => 'Float' ],
		],
	] );

	// Helper to inject `id` field
	$add_id = function( string $type_name, string $pk, array $fields, string $table ) : array {
		static $cache = [];
		$key = $type_name . '|' . $pk;
		if ( isset( $cache[ $key ] ) ) {
			return $cache[ $key ];
		}
		$fields['id'] = [
			'type'        => 'ID',
			'description' => sprintf( __( 'Global Relay ID (%s)', 'wpgraphql-realtypress' ), $table ),
			'resolve'     => function( $row ) use ( $type_name, $pk ) {
				$value = is_array( $row ) ? ( $row[ $pk ] ?? null ) : ( $row->$pk ?? null );
				return graphql_encode_global_id( $type_name, $value );
			},
		];
		return $cache[ $key ] = $fields;
	};

	// 1) Single-property fetch
	register_graphql_field( 'RootQuery', 'property', [
		'type'        => 'Property',
		'description' => __( 'Fetch a single property by ID', 'wpgraphql-realtypress' ),
		'args'        => [
			'id' => [ 'type' => 'ID!' ],
		],
		'resolve'     => function( $_, $args ) {
			list( , $prop_id ) = graphql_decode_global_id( $args['id'] );
			return \RealtyPress_DDF_CRUD::get_property_by_id( $prop_id );
		},
	] );

	// 2) Object definitions
	$object_map = [
		'RealtyAgent'   => [ 'pk' => 'agent_id',  'table' => 'wp_rps_agent',         'fields' => [
			'agent_id'=>'String','Name'=>'String','Email'=>'String','Phones'=>'String',
		] ],
		'RealtyBoard'   => [ 'pk' => 'OrganizationID','table'=>'wp_rps_boards',    'fields'=>[
			'OrganizationID'=>'Int','ShortName'=>'String','LongName'=>'String',
		] ],
		'RealtyOffice'  => [ 'pk' => 'office_id', 'table'=>'wp_rps_office',         'fields'=>[
			'office_id'=>'String','Name'=>'String','City'=>'String','Province'=>'String',
		] ],
		'PropertyRoom'  => [ 'pk' => 'room_id',   'table'=>'wp_rps_property_rooms', 'fields'=>[
			'room_id'=>'String','Type'=>'String','Width'=>'String','Length'=>'String',
		] ],
		'PropertyPhoto' => [ 'pk' => 'details_id','table'=>'wp_rps_property_photos','fields'=>[
			'details_id'=>'String','Photos'=>'String','Description'=>'String',
		] ],
		'Property'      => [ 'pk' => 'property_id','table'=>'wp_rps_property',      'fields'=>[
			'property_id'=>'String','City'=>'String','Province'=>'String','Price'=>'Float',
		] ],
	];

	foreach ( $object_map as $type => $cfg ) {
		register_graphql_object_type( $type, [
			'description' => sprintf( __( '%s record from RealtyPress', 'wpgraphql-realtypress' ), $type ),
			'interfaces'  => [ 'Node', 'DatabaseIdentifier' ],
			'fields'      => function() use ( $cfg, $add_id, $type ) {
				$out = [];
				foreach ( $cfg['fields'] as $col => $t ) {
					$out[ $col ] = [
						'type'        => $t,
						'description' => sprintf( __( 'Column %s', 'wpgraphql-realtypress' ), $col ),
					];
				}
				// photos[] on Property
				if ( 'Property' === $type ) {
					$out['photos'] = [
						'type'        => [ 'list_of' => 'PropertyPhoto' ],
						'description' => __( 'Property photos', 'wpgraphql-realtypress' ),
					];
				}
				return $add_id( $type, $cfg['pk'], $out, $cfg['table'] );
			},
		] );
	}

	// 3) Single fetch for other types
	foreach ( [ 'RealtyAgent', 'RealtyBoard', 'RealtyOffice', 'PropertyRoom', 'PropertyPhoto' ] as $type ) {
		register_graphql_field( 'RootQuery', lcfirst( $type ), [
			'type'        => $type,
			'description' => sprintf( __( 'Fetch a %s by its ID', 'wpgraphql-realtypress' ), $type ),
			'args'        => [ 'id' => [ 'type' => 'ID!' ] ],
			'resolve'     => function( $_, $args ) use ( $type ) {
				list( , $id ) = graphql_decode_global_id( $args['id'] );
				$method = sprintf( 'get_%s_by_id', strtolower( $type ) );
				if ( method_exists( '\RealtyPress_DDF_CRUD', $method ) ) {
					return \RealtyPress_DDF_CRUD::$method( $id );
				}
				return null;
			},
		] );
	}
} );
