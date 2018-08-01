<?php

// Version 1.0

if ( ! class_exists( 'Openclub_CSV_Dependency' ) ) {

	class Openclub_CSV_Dependency {

		private function __construct() {
		}

		/**
		 * @param string $plugin_file
		 *
		 * @return bool|void
		 */
		public static function check( string $plugin_file ) {

			if ( empty( trim( $plugin_file ) ) ) {
				error_log( 'Openclub_CSV_Dependency::check() must be passed a string.' );

				return;
			}

			if ( ! defined( 'OPENCLUB_CSV_PLUGIN_DIR' ) ) {
				define( 'OPENCLUB_CSV_PLUGIN_DIR', wp_normalize_path( WP_PLUGIN_DIR ) . '/openclub-csv/' );
			}

			if ( ! file_exists( OPENCLUB_CSV_PLUGIN_DIR . 'inc/class-factory.php' ) ) {
				self::log_error();
				self::deactivate_within_wp_admin( $plugin_file );

				return;
			}

			require_once( OPENCLUB_CSV_PLUGIN_DIR . 'inc/class-factory.php' );

			if ( ! defined( 'OPENCLUB_DEFAULT_FILTER_PRIORITY' ) ) {
				self::log_error();
				self::deactivate_within_wp_admin( $plugin_file );

				return;
			}

			return true;
		}

		private static function deactivate_within_wp_admin( $plugin_file ) {

			/**
			 * Check if the openclub-csv plugin is loaded in wp-admin
			 */
			add_action( 'admin_init', function () use ( $plugin_file ) {

				deactivate_plugins( plugin_basename( $plugin_file ) );
				if ( ! empty( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
				add_action( 'admin_notices', function () use ( $plugin_file ) {

					echo '<div class="notice notice-warning is-dismissible">
         <p>Plugin OpenClub CSV must exist and be activated. ' . plugin_dir_path( $plugin_file ) . 'has been deactivated</p>
     </div>';
				} );
			} );
		}

		private static function log_error() {

			error_log( 'The openclub-csv plugin is required. WordPress plugin ssc is exiting early.' );
		}

	}

}