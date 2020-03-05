<?php

namespace TK_Exclude_VCS_Updates;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use stdClass;

if ( ! class_exists( Plugins::class ) ) {
	/**
	 * Plugins class.
	 */
	class Plugins extends Core {
		/**
		 * The list of plugin slugs detected as having VCS and an available update (ones to be excluded from updates).
		 *
		 * @var array
		 */
		private $excluded_plugins = [];

		/**
		 * Plugins constructor.
		 */
		public function __construct() {
			add_filter( 'site_transient_update_plugins', [ $this, 'process_updates' ], 100 );
			add_filter( 'admin_notices', [ $this, 'notice' ], 5 );
			add_action( 'admin_head', [ $this, 'style_each_excluded_plugin_in_plugins_list' ] );
		}

		/**
		 * Exclude plugins whose folders contain a VCS directory from appearing in the list of updates.
		 *
		 * To avoid accidentally overwriting your hard development work with a released version, such as from WordPress.org.
		 *
		 * @link https://wordpress.org/support/topic/disable-updates-for-vcs-plugins-themes-like-git-svn-etc/
		 *
		 * @param stdClass $list
		 *
		 * @see  \get_plugin_updates()
		 *
		 * @return stdClass
		 */
		public function process_updates( $list ) {
			if (
				current_user_can( 'update_plugins' )
				&& defined( 'WP_PLUGIN_DIR' )
				&& isset( $list )
				&& is_object( $list )
				&& ! empty( $list->response )
			) {
				$plugins = [];

				foreach ( $list->response as $plugin_file => $plugin_data ) {
					$plugin_dir_name = strstr( $plugin_file, DIRECTORY_SEPARATOR, true );

					$plugin_location = trailingslashit( WP_PLUGIN_DIR ) . $plugin_dir_name;

					foreach ( $this->get_vcs_names() as $vcs_name ) {
						$find = trailingslashit( $plugin_location ) . $vcs_name;

						if ( file_exists( $find ) ) {
							$plugins[] = $plugin_dir_name;

							/**
							 * Action hook when a plugin is found to have VCS and therefore removed from plugin updates list.
							 *
							 * @param string $plugin_dir_name The plugin directory name.
							 * @param array  $plugin_data     The plugin data, such as for getting the nice name.
							 * @param string $plugin_file     The plugin file name (dir/file.php).
							 * @param string $find            The found file that exempted this plugin from automatic updates.
							 */
							do_action( __FUNCTION__, $plugin_dir_name, (array) $plugin_data, $plugin_file, $find );

							unset( $list->response[ $plugin_file ] );
							continue;
						}
					}
				}

				$this->excluded_plugins = array_unique( $plugins );
			}

			return $list;
		}

		/**
		 * Whether or not we have exclusions and are on the correct admin screen.
		 *
		 * @return bool
		 */
		private function should_appear() {
			$current_screen = get_current_screen();

			if (
				empty( $this->excluded_plugins )
				|| empty( $current_screen->base )
			) {
				return false;
			}

			if (
				'update-core' === $current_screen->base
				|| 'plugins' === $current_screen->base
			) {
				return true;
			}

			return false;
		}

		/**
		 * Display the notice about each excluded plugin, only on the Update Core or the Plugins List screens.
		 */
		public function notice() {
			if ( ! $this->should_appear() ) {
				return;
			}

			$list = sprintf( '<strong>%s</strong>', implode( ', ', $this->excluded_plugins ) );

			echo '<div class="notice notice-warning"><p>';
			echo sprintf( esc_html__( 'These plugins were excluded from update checks because of having version control: %s', 'tk-exclude-vcs-updates' ), $list );
			echo '</p></div>';
		}

		/**
		 * Output CSS for plugins excluded due to VCS in the Plugins List.
		 *
		 * @link  https://user-images.githubusercontent.com/1812179/75998828-0692ac80-5ec7-11ea-9f85-bfa47b8fd544.png Screenshot.
		 *
		 * @since 1.1.0
		 */
		public function style_each_excluded_plugin_in_plugins_list() {
			if ( ! $this->should_appear() ) {
				return;
			}

			$parent = PHP_EOL . 'table.plugins';

			$selector = '';

			foreach ( $this->excluded_plugins as $plugin ) {
				$selector .= sprintf( '%s tr[data-slug="%s"] th,', $parent, $plugin ); // the checkbox area
			}

			// Remove the final comma so we have a valid CSS selector.
			$selector = rtrim( $selector, ',' );
			?>

			<style id="tk-exclude-vcs-updates" type="text/css">
				<?php echo $selector; ?>
				{
					background-color: #ffb900; /* Same color as admin 'warning' notice. */
				}
			</style>
			<?php
		}

	} // end class
} // end if class_exists check
