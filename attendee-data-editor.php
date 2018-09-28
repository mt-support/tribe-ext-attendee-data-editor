<?php
/**
 * Plugin Name:       Event Tickets Plus Extension: Attendee Data Editor
 * Plugin URI:        https://theeventscalendar.com/extensions/attendee-data-editor/
 * GitHub Plugin URI: https://github.com/mt-support/tribe-ext-attendee-data-editor
 * Description:       Makes it possible and easy for admins to edit custom attendee meta fields.
 * Version:           1.2.1
 * Extension Class:   Tribe__Extension__Attendee_Data_Editor
 * Author:            Modern Tribe, Inc.
 * Author URI:        http://m.tri.be/1971
 * License:           GPL version 3 or any later version
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       tribe-ext-attendee-data-editor
 *
 *     This plugin is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     any later version.
 *
 *     This plugin is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *     GNU General Public License for more details.
 */

// Do not load unless Tribe Common is fully loaded and our class does not yet exist.
if (
	class_exists( 'Tribe__Extension' )
	&& ! class_exists( 'Tribe__Extension__Attendee_Data_Editor' )
) {
	class Tribe__Extension__Attendee_Data_Editor extends Tribe__Extension {
		const REQUIRED_PHP_VERSION = '5.6';

		private $dir = '';
		private $url = '';

		/**
		 * Register plugin requirements (ET+ 4.7 or greater) with the extension manager.
		 */
		public function construct() {
			$this->add_required_plugin( 'Tribe__Tickets_Plus__Main', '4.7.0' );
		}

		/**
		 * Perform sanity checks and initial setup.
		 */
		public function init() {
			load_plugin_textdomain( 'tribe-ext-attendee-data-editor', false, basename( __DIR__ ) . '/languages/' );

			if ( ! $this->php_version_check() ) {
				return;
			}

			$this->dir = __DIR__;
			$this->url = plugin_dir_url( __FILE__ );

			$this->class_loader();

			// Avoid syntax errors if this file is loaded under PHP 5.2 or earlier
			$main_class = 'Tribe\Extensions\Attendee_Data_Editor\Main';
			new $main_class( $this->url );
		}

		/**
		 * @return bool
		 */
		private function php_version_check() {
			if ( version_compare( PHP_VERSION, self::REQUIRED_PHP_VERSION, '<' ) ) {
				if (
					is_admin()
					&& current_user_can( 'activate_plugins' )
				) {
					$message = '<p>';
					$message .= sprintf( __( '%s requires PHP version %s or newer to work. Please contact your website host and inquire about updating PHP.', 'tribe-ext-attendee-data-editor' ), $this->get_name(), self::REQUIRED_PHP_VERSION );
					$message .= sprintf( ' <a href="%1$s">%1$s</a>', 'https://wordpress.org/about/requirements/' );
					$message .= '</p>';

					tribe_notice( $this->get_name(), $message, 'type=error' );
				}

				return false;
			}

			return true;
		}

		/**
		 * @return Tribe__Autoloader
		 */
		public function class_loader() {
			if ( empty( $this->class_loader ) ) {
				$this->class_loader = new Tribe__Autoloader;
				$this->class_loader->set_dir_separator( '\\' );
				$this->class_loader->register_prefix(
					'Tribe\Extensions\Attendee_Data_Editor\\',
					__DIR__ . DIRECTORY_SEPARATOR . 'src'
				);
			}

			$this->class_loader->register_autoloader();
			return $this->class_loader;
		}
	}
}
