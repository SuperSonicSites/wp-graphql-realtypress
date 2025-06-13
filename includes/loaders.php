<?php
/**
 * Safe DataLoader registration for WPGraphQL-RealtyPress.
 *
 * Works with any WPGraphQL version: if the DataLoader API is missing
 * we still define a harmless stub so referencing the loader never fatal.
 */

namespace WPGraphQL\RealtyPress;

defined( 'ABSPATH' ) || exit;

/**
 * Detect whether the WPGraphQL DataLoader API is available.
 *
 * @return bool
 */
function wprp_has_dataloader_api(): bool {
    return class_exists( '\WPGraphQL\Data\DataLoader\AbstractDataLoader' );
}

if ( wprp_has_dataloader_api() ) {

    /**
     * Real implementation of the RealtyBoardLoader.
     * Extends the WPGraphQL DataLoader base class when available.
     */
    class RealtyBoardLoader extends \WPGraphQL\Data\DataLoader\AbstractDataLoader {

        /**
         * Load the board row by ID.
         *
         * @param mixed $id
         * @return array|\WP_Error|null
         */
        public function load( $id ) {
            // TODO: implement actual fetch logic.
            return null;
        }

        /**
         * Prime the cache (no-op for now).
         *
         * @param mixed $id
         * @param mixed $value
         */
        protected function prime( $id, $value ) {}
    }

    /**
     * DataLoader for Property records.
     */
    class PropertyLoader extends \WPGraphQL\Data\DataLoader\AbstractDataLoader {

        /**
         * Fetch a property row by ID.
         *
         * @param mixed $id
         * @return array|null
         */
        public function load( $id ) {
            global $wpdb;

            $sql = $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}rps_property WHERE property_id = %d",
                $id
            );

            return $wpdb->get_row( $sql, ARRAY_A );
        }

        /**
         * Prime the cache (unused for now).
         *
         * @param mixed $id
         * @param mixed $value
         */
        protected function prime( $id, $value ) {}
    }

    /**
     * Register our loaders with WPGraphQL.
     */
    add_filter(
        'graphql_data_loaders',
        function ( array $loaders ) {
            $loaders['realty_board'] = RealtyBoardLoader::class;
            $loaders['property']     = PropertyLoader::class;
            return $loaders;
        }
    );

} else {

    /**
     * Fallback stub so references to RealtyBoardLoader never fatal.
     */
class RealtyBoardLoader {
        public function load( $id ) {
            return null;
        }
    }

    class PropertyLoader {
        public function load( $id ) {
            return null;
        }
    }
}


