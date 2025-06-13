<?php
// includes/types.php

/**
 * Register RealtyPress tables as GraphQL object types
 * and ensure each implements the Node interface by exposing
 * a globally‑unique Relay ID.
 */
add_action( 'graphql_register_types', function () {

	/*
	 * -------------------------------------------------------------------
	 *  Input types
	 * -------------------------------------------------------------------
	 */
	register_graphql_input_type( 'PropertyFilterInput', [
		'description' => __( 'Filter properties by various fields', 'wpgraphql-realtypress' ),
		'fields'      => [
			'City'     => [ 'type' => 'String' ],
			'minPrice' => [ 'type' => 'Float' ],
		],
	] );

	/*
	 * -------------------------------------------------------------------
	 *  Helper used below to DRY the “id” field definition.
	 * -------------------------------------------------------------------
	 */
	$add_id_field = function ( string $type_name, string $primary_key, array $fields, string $table_name ) : array {
		return array_merge(
			$fields,
			[
				'id' => [
					'type'        => [ 'non_null' => 'ID' ],
					'description' => sprintf(
						/* translators: %s: SQL table name */
						__( 'Relay‑compliant global ID derived from the primary key of %s.', 'wpgraphql-realtypress' ),
						$table_name
					),
					'resolve'     => function ( $row ) use ( $type_name, $primary_key ) {
						$value = is_array( $row )
							? ( $row[ $primary_key ] ?? null )
							: ( $row->$primary_key ?? null );

						return graphql_encode_global_id( $type_name, $value );
					},
				],
			]
		);
	};

	/*
	 * -------------------------------------------------------------------
	 *  RealtyAgent  – wp_rps_agent
	 * -------------------------------------------------------------------
	 */
	$realtyAgent_fields = [
		'agent_id'             => 'String',
		'AgentID'              => 'String',
		'OfficeID'             => 'String',
		'Name'                 => 'String',
		'ID'                   => 'String',
		'LastUpdated'          => 'String',
		'Position'             => 'String',
		'EducationCredentials' => 'String',
		'Photos'               => 'String',
		'PhotoLastUpdated'     => 'String',
		'Specialties'          => 'String',
		'Specialty'            => 'String',
		'Languages'            => 'String',
		'Language'             => 'String',
		'TradingAreas'         => 'String',
		'TradingArea'          => 'String',
		'Phones'               => 'String',
		'Websites'             => 'String',
		'Designations'         => 'String',
		'CustomAgent'          => 'Boolean',
		'Email'                => 'String',
	];

	register_graphql_object_type( 'RealtyAgent', [
		'description' => __( 'Agent record from RealtyPress', 'wpgraphql-realtypress' ),
		'interfaces'  => [ 'Node', 'DatabaseIdentifier' ],
		'fields'      => function () use ( $realtyAgent_fields, $add_id_field ) {
			$fields = [];
			foreach ( $realtyAgent_fields as $col => $type ) {
				$fields[ $col ] = [
					'type'        => $type,
					'description' => "Column $col from wp_rps_agent",
				];
			}
			return $add_id_field( 'RealtyAgent', 'agent_id', $fields, 'wp_rps_agent' );
		},
	] );

	/*
	 * -------------------------------------------------------------------
	 *  RealtyBoard – wp_rps_boards
	 * -------------------------------------------------------------------
	 */
	$realtyBoard_fields = [
		'OrganizationID' => 'Int',
		'ShortName'      => 'String',
		'LongName'       => 'String',
	];

	register_graphql_object_type( 'RealtyBoard', [
		'description' => __( 'Board record from RealtyPress', 'wpgraphql-realtypress' ),
		'interfaces'  => [ 'Node', 'DatabaseIdentifier' ],
		'fields'      => function () use ( $realtyBoard_fields, $add_id_field ) {
			$fields = [];
			foreach ( $realtyBoard_fields as $col => $type ) {
				$fields[ $col ] = [
					'type'        => $type,
					'description' => "Column $col from wp_rps_boards",
				];
			}
			// Primary key is OrganizationID
			return $add_id_field( 'RealtyBoard', 'OrganizationID', $fields, 'wp_rps_boards' );
		},
	] );

	/*
	 * -------------------------------------------------------------------
	 *  RealtyOffice – wp_rps_office
	 * -------------------------------------------------------------------
	 */
	$realtyOffice_fields = [
		'office_id'            => 'String',
		'OfficeID'             => 'String',
		'Name'                 => 'String',
		'ID'                   => 'String',
		'LastUpdated'          => 'String',
		'LogoLastUpdated'      => 'String',
		'Logos'                => 'String',
		'OrganizationType'     => 'String',
		'Designation'          => 'String',
		'Address'              => 'String',
		'Franchisor'           => 'String',
		'StreetAddress'        => 'String',
		'AddressLine1'         => 'String',
		'AddressLine2'         => 'String',
		'City'                 => 'String',
		'Province'             => 'String',
		'PostalCode'           => 'String',
		'Country'              => 'String',
		'AdditionalStreetInfo' => 'String',
		'CommunityName'        => 'String',
		'Neighbourhood'        => 'String',
		'Subdivision'          => 'String',
		'Phones'               => 'String',
		'Websites'             => 'String',
		'CustomOffice'         => 'Boolean',
	];

	register_graphql_object_type( 'RealtyOffice', [
		'description' => __( 'Office record from RealtyPress', 'wpgraphql-realtypress' ),
		'interfaces'  => [ 'Node', 'DatabaseIdentifier' ],
		'fields'      => function () use ( $realtyOffice_fields, $add_id_field ) {
			$fields = [];
			foreach ( $realtyOffice_fields as $col => $type ) {
				$fields[ $col ] = [
					'type'        => $type,
					'description' => "Column $col from wp_rps_office",
				];
			}
			return $add_id_field( 'RealtyOffice', 'office_id', $fields, 'wp_rps_office' );
		},
	] );

	/*
	 * -------------------------------------------------------------------
	 *  PropertyRoom – wp_rps_property_rooms
	 * -------------------------------------------------------------------
	 */
	$propertyRoom_fields = [
		'room_id'    => 'String',
		'ListingID'  => 'String',
		'Type'       => 'String',
		'Width'      => 'String',
		'Length'     => 'String',
		'Level'      => 'String',
		'Dimension'  => 'String',
		'CustomRoom' => 'Boolean',
	];

	register_graphql_object_type( 'PropertyRoom', [
		'description' => __( 'Room record from RealtyPress', 'wpgraphql-realtypress' ),
		'interfaces'  => [ 'Node', 'DatabaseIdentifier' ],
		'fields'      => function () use ( $propertyRoom_fields, $add_id_field ) {
			$fields = [];
			foreach ( $propertyRoom_fields as $col => $type ) {
				$fields[ $col ] = [
					'type'        => $type,
					'description' => "Column $col from wp_rps_property_rooms",
				];
			}
			return $add_id_field( 'PropertyRoom', 'room_id', $fields, 'wp_rps_property_rooms' );
		},
	] );

	/*
	 * -------------------------------------------------------------------
	 *  PropertyPhoto – wp_rps_property_photos
	 * -------------------------------------------------------------------
	 */
	$propertyPhoto_fields = [
		'details_id'       => 'String',
		'ListingID'        => 'String',
		'SequenceID'       => 'Int',
		'Description'      => 'String',
		'Photos'           => 'String',
		'LastUpdated'      => 'String',
		'PhotoLastUpdated' => 'String',
		'CustomPhoto'      => 'Boolean',
	];

	register_graphql_object_type( 'PropertyPhoto', [
		'description' => __( 'Photo record from RealtyPress', 'wpgraphql-realtypress' ),
		'interfaces'  => [ 'Node', 'DatabaseIdentifier' ],
		'fields'      => function () use ( $propertyPhoto_fields, $add_id_field ) {
			$fields = [];
			foreach ( $propertyPhoto_fields as $col => $type ) {
				$fields[ $col ] = [
					'type'        => $type,
					'description' => "Column $col from wp_rps_property_photos",
				];
			}
			return $add_id_field( 'PropertyPhoto', 'details_id', $fields, 'wp_rps_property_photos' );
		},
	] );

	/*
	 * -------------------------------------------------------------------
	 *  Property – wp_rps_property
	 * -------------------------------------------------------------------
	 *	⚠  Only a subset of columns is shown here to keep the snippet short.
	 */
	$property_fields = [
                'property_id'                     => 'String',  // bigint(12)
        'PostID'                          => 'String',  // bigint(12)
        'Offices'                         => 'String',  // varchar(40)
        'Agents'                          => 'String',  // varchar(50)
        'Board'                           => 'String',  // varchar(4)
        'ListingID'                       => 'String',  // bigint(20)
        'DdfListingID'                    => 'String',  // varchar(25)
        'LastUpdated'                     => 'String',  // varchar(25)
        'Latitude'                        => 'String',  // varchar(25)
        'Longitude'                       => 'String',  // varchar(25)
        'AmmenitiesNearBy'                => 'String',  // varchar(180)
        'CommunicationType'               => 'String',  // varchar(60)
        'CommunityFeatures'               => 'String',  // varchar(60)
        'Crop'                            => 'String',  // varchar(60)
        'DocumentType'                    => 'String',  // varchar(50)
        'EquipmentType'                   => 'String',  // varchar(60)
        'Easement'                        => 'String',  // varchar(60)
        'FarmType'                        => 'String',  // varchar(60)
        'Features'                        => 'String',  // varchar(255)
        'IrrigationType'                  => 'String',  // varchar(60)
        'Lease'                           => 'String',  // varchar(60)
        'LeasePerTime'                    => 'String',  // varchar(60)
        'LeasePerUnit'                    => 'String',  // varchar(60)
        'LeaseTermRemaining'              => 'String',  // varchar(60)
        'LeaseTermRemainingFreq'          => 'String',  // varchar(60)
        'LeaseType'                       => 'String',  // varchar(60)
        'ListingContractDate'             => 'String',  // varchar(20)
        'LiveStockType'                   => 'String',  // varchar(60)
        'LoadingType'                     => 'String',  // varchar(60)
        'LocationDescription'             => 'String',  // text
        'Machinery'                       => 'String',  // varchar(60)
        'MaintenanceFee'                  => 'String',  // varchar(60)
        'MaintenanceFeePaymentUnit'       => 'String',  // varchar(60)
        'MaintenanceFeeType'              => 'String',  // varchar(60)
        'ManagementCompany'               => 'String',  // varchar(60)
        'MunicipalID'                     => 'String',  // varchar(60)
        'OwnershipType'                   => 'String',  // varchar(60)
        'ParkingSpaceTotal'               => 'Int',     // int(10)
        'Plan'                            => 'String',  // varchar(60)
        'PoolType'                        => 'String',  // varchar(60)
        'PoolFeatures'                    => 'String',  // varchar(255)
        'Price'                           => 'Float',   // double
        'PricePerTime'                    => 'String',  // varchar(60)
        'PricePerUnit'                    => 'String',  // varchar(60)
        'PropertyType'                    => 'String',  // varchar(60)
        'PublicRemarks'                   => 'String',  // text
        'RentalEquipmentType'             => 'String',  // varchar(60)
        'RightType'                       => 'String',  // varchar(60)
        'RoadType'                        => 'String',  // varchar(60)
        'StorageType'                     => 'String',  // varchar(60)
        'Structure'                       => 'String',  // varchar(60)
        'SignType'                        => 'String',  // varchar(60)
        'TransactionType'                 => 'String',  // varchar(60)
        'TotalBuildings'                  => 'Int',     // int(10)
        'ViewType'                        => 'String',  // varchar(60)
        'WaterFrontType'                  => 'String',  // varchar(60)
        'WaterFrontName'                  => 'String',  // varchar(100)
        'AdditionalInformationIndicator'  => 'Boolean', // tinyint(1)
        'ZoningDescription'               => 'String',  // text
        'ZoningType'                      => 'String',  // varchar(60)
        'MoreInformationLink'             => 'String',  // varchar(255)
        'AnalyticsClick'                  => 'Int',     // int(11)
        'AnalyticsView'                   => 'Int',     // int(11)
        'BusinessType'                    => 'String',  // varchar(60)
        'BusinessSubType'                 => 'String',  // varchar(60)
        'EstablishedDate'                 => 'String',  // varchar(20)
        'Franchise'                       => 'String',  // varchar(60)
        'Name'                            => 'String',  // varchar(100)
        'OperatingSince'                  => 'String',  // varchar(20)
        'BathroomTotal'                   => 'Int',     // int(10)
        'BedroomsAboveGround'             => 'Int',     // int(10)
        'BedroomsBelowGround'             => 'Int',     // int(10)
        'BedroomsTotal'                   => 'Int',     // int(10)
        'Age'                             => 'Int',     // int(10)
        'Amenities'                       => 'String',  // text
        'Amperage'                        => 'String',  // varchar(60)
        'Anchor'                          => 'String',  // varchar(60)
        'Appliances'                      => 'String',  // text
        'ArchitecturalStyle'              => 'String',  // varchar(60)
        'BasementDevelopment'             => 'String',  // varchar(60)
        'BasementFeatures'                => 'String',  // varchar(255)
        'BasementType'                    => 'String',  // varchar(60)
        'BomaRating'                      => 'String',  // varchar(60)
        'CeilingHeight'                   => 'String',  // varchar(60)
        'CeilingType'                     => 'String',  // varchar(60)
        'ClearCeilingHeight'              => 'String',  // varchar(60)
        'ConstructedDate'                 => 'String',  // varchar(20)
        'ConstructionMaterial'            => 'String',  // varchar(60)
        'ConstructionStatus'              => 'String',  // varchar(60)
        'ConstructionStyleAttachment'     => 'String',  // varchar(60)
        'ConstructionStyleOther'          => 'String',  // varchar(60)
        'ConstructionStyleSplitLevel'     => 'String',  // varchar(60)
        'CoolingType'                     => 'String',  // varchar(60)
        'EnerguideRating'                 => 'String',  // varchar(60)
        'ExteriorFinish'                  => 'String',  // varchar(60)
        'FireProtection'                  => 'String',  // varchar(60)
        'FireplaceFuel'                   => 'String',  // varchar(60)
        'FireplacePresent'                => 'Boolean', // tinyint(1)
        'FireplaceTotal'                  => 'Int',     // int(10)
        'FireplaceType'                   => 'String',  // varchar(60)
        'Fixture'                         => 'String',  // varchar(255)
        'FlooringType'                    => 'String',  // varchar(60)
        'FoundationType'                  => 'String',  // varchar(60)
        'HalfBathTotal'                   => 'Int',     // int(10)
        'HeatingFuel'                     => 'String',  // varchar(60)
        'HeatingType'                     => 'String',  // varchar(60)
        'LeedsCategory'                   => 'String',  // varchar(60)
        'LeedsRating'                     => 'String',  // varchar(60)
        'RenovatedDate'                   => 'String',  // varchar(20)
        'RoofMaterial'                    => 'String',  // varchar(60)
        'RoofStyle'                       => 'String',  // varchar(60)
        'StoriesTotal'                    => 'Int',     // int(10)
        'SizeExterior'                    => 'String',  // varchar(60)
        'SizeInterior'                    => 'String',  // varchar(60)
        'SizeInteriorFinished'            => 'String',  // varchar(60)
        'StoreFront'                      => 'String',  // varchar(60)
        'TotalFinishedArea'               => 'String',  // varchar(60)
        'Type'                            => 'String',  // varchar(60)
        'Uffi'                            => 'String',  // varchar(60)
        'UnitType'                        => 'String',  // varchar(60)
        'UtilityPower'                    => 'String',  // varchar(60)
        'UtilityWater'                    => 'String',  // varchar(60)
        'VacancyRate'                     => 'String',  // varchar(60)
        'SizeTotal'                       => 'String',  // varchar(60)
        'SizeTotalText'                   => 'String',  // varchar(60)
        'SizeFrontage'                    => 'String',  // varchar(60)
        'AccessType'                      => 'String',  // varchar(60)
        'Acreage'                         => 'String',  // varchar(60)
        'LandAmenities'                   => 'String',  // text
        'ClearedTotal'                    => 'String',  // varchar(60)
        'CurrentUse'                      => 'String',  // varchar(60)
        'Divisible'                       => 'Boolean', // tinyint(1)
        'FenceTotal'                      => 'String',  // varchar(60)
        'FenceType'                       => 'String',  // varchar(60)
        'FrontsOn'                        => 'String',  // varchar(60)
        'LandDisposition'                 => 'String',  // varchar(60)
        'LandscapeFeatures'               => 'String',  // text
        'PastureTotal'                    => 'String',  // varchar(60)
        'Sewer'                           => 'String',  // varchar(60)
        'SizeDepth'                       => 'String',  // varchar(60)
        'SizeIrregular'                   => 'String',  // varchar(60)
        'SoilEvaluation'                  => 'String',  // varchar(60)
        'SoilType'                        => 'String',  // varchar(60)
        'SurfaceWater'                    => 'String',  // varchar(60)
        'TiledTotal'                      => 'String',  // varchar(60)
        'TopographyType'                  => 'String',  // varchar(60)
        'StreetAddress'                   => 'String',  // varchar(60)
        'AddressLine1'                    => 'String',  // varchar(60)
        'AddressLine2'                    => 'String',  // varchar(60)
        'StreetNumber'                    => 'String',  // varchar(60)
        'StreetName'                      => 'String',  // varchar(60)
        'StreetSuffix'                    => 'String',  // varchar(60)
        'StreetDirectionSuffix'           => 'String',  // varchar(60)
        'UnitNumber'                      => 'String',  // varchar(60)
        'City'                            => 'String',  // varchar(60)
        'Province'                        => 'String',  // varchar(60)
        'PostalCode'                      => 'String',  // varchar(12)
        'Country'                         => 'String',  // varchar(60)
        'AdditionalStreetInfo'            => 'String',  // varchar(100)
        'CommunityName'                   => 'String',  // varchar(60)
        'Neighbourhood'                   => 'String',  // varchar(60)
        'Subdivision'                     => 'String',  // varchar(60)
        'Utilities'                       => 'String',  // text
        'Parking'                         => 'String',  // text
        'OpenHouse'                       => 'String',  // varchar(255)
        'AlternateURL'                    => 'String',  // varchar(255)
        'CustomListing'                   => 'Boolean', // tinyint(1)
        'Sold'                            => 'Boolean', // tinyint(1)
        'GeoLastUpdated'                  => 'String',  // varchar(25)
        'GeoSource'                       => 'String',  // varchar(60)
        'LiveStream'                      => 'String',  // varchar(255)
	];

	register_graphql_object_type( 'Property', [
		'description' => __( 'Property record from RealtyPress', 'wpgraphql-realtypress' ),
		'interfaces'  => [ 'Node', 'DatabaseIdentifier' ],
		'fields'      => function () use ( $property_fields, $add_id_field ) {
			$fields = [];
			foreach ( $property_fields as $col => $type ) {
				$fields[ $col ] = [
					'type'        => $type,
					'description' => "Column $col from wp_rps_property",
				];
			}

			// Relation: photos[]
			$fields['photos'] = [
				'type'        => [ 'list_of' => 'PropertyPhoto' ],
				'description' => __( 'Photos for this property', 'wpgraphql-realtypress' ),
			];

			return $add_id_field( 'Property', 'property_id', $fields, 'wp_rps_property' );
		},
	] );
} );
