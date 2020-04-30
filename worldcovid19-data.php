<?php
/*
Plugin Name: World Covid 19 Statistical charts
Plugin URI: https://protechthemes.com/
Description: Covid 19 virus data for your wordpress sites
Version: 1.0.0
Requires at least: 5.3.2
Requires PHP: 5.4
Author: ProtechThemes
Author URI: https://protechthemes.com
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: protech-covid19
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


class PThemes_Covid19 {
	public function __construct() {
		include_once plugin_dir_path(__FILE__) . 'vendor/pthemes/Admin_Settings.php';
		include_once plugin_dir_path(__FILE__) . 'inc/Plugin.php';
		include_once plugin_dir_path(__FILE__) . 'inc/Remote.php';
		include_once plugin_dir_path(__FILE__) . 'inc/admin/init.php';
        new \PThemes_Covid19\Plugin(__FILE__);
    }
}
new PThemes_Covid19();
