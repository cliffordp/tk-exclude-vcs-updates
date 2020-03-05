<?php

namespace TK_Exclude_VCS_Updates;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use stdClass;

if ( ! class_exists( Core::class ) ) {
	/**
	 * Core class.
	 *
	 * @todo Add support for themes
	 * @see \do_undismiss_core_update()
	 */
	abstract class Core {
		/**
		 * The types of files/directories indicative of being under version control.
		 *
		 * @return array
		 */
		public function get_vcs_names() {
			return [
				'.git',
				'.hg', // Mercurial
				'.svn',
			];
		}

		/**
		 * Process the list of plugins/themes.
		 *
		 * @param stdClass $list
		 */
		public abstract function process_updates( $list );

		/**
		 * Display the notice about each excluded plugin, only on the applicable screens.
		 */
		public abstract function notice();

	} // end class
} // end if class_exists check
