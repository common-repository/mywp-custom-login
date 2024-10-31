<?php
/**
 * Plugin Name: MyWP Custom Login
 * Plugin URI: https://wordpress.org/plugins/mywp-custom-login/
 * Description: Customise WordPress login page
 * Version: 0.4
 * Author: Whodunit Agency
 * Author URI: https://whodunit.fr/
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mywp-custom-login
 * Domain Path: /languages
 */

defined( "ABSPATH" ) or die( "don't." );
require __DIR__."/vendor/autoload.php";
\whodunit\mywpCustomLogin\MyWPCustomLogin::set_instance( __FILE__ );

