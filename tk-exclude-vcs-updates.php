<?php
/**
 * Plugin Name:       TK Exclude VCS Updates
 * Description:       Exclude plugins whose folders contain a VCS directory from appearing in the list of updates.
 * Version:           1.1.0
 * GitHub Plugin URI: https://github.com/cliffordp/tk-exclude-vcs-updates
 * Author:            TourKick LLC (Clifford Paulick)
 * Author URI:        https://tourkick.com/
 * License:           GPL version 3 or any later version
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       tk-exclude-vcs-updates
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

namespace TK_Exclude_VCS_Updates;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( 'src/class-Core.php' );
require_once( 'src/class-Plugins.php' );

new Plugins();
