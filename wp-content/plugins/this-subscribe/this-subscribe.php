<?php
/**
 * Plugin Name: This subscribe
 * Plugin URI: https://caguct.com/this-subscribe
 * Description: Add subscribes from email to wordpress
 * Version: 1.0.0
 * Text Domain: caguct-com
 * Author: Oleksii Biriukov
 * Author URI: https://caguct.com/u
 * License: none
 */

if ( defined( 'PL_ROOT' ) === false ) {
	define( 'PL_ROOT', realpath( dirname( __FILE__ ) ) );
}
if ( defined( 'PL_URL' ) === false ) {
	define( 'PL_URL', plugin_dir_url( __FILE__ ) );
}
if ( defined( 'DS' ) === false ) {
	define( 'DS', DIRECTORY_SEPARATOR );
}
if ( defined( 'PL_TEMPLATES' ) === false ) {
	define( 'PL_TEMPLATES', PL_ROOT . DS . 'templates' );
}

include_once PL_ROOT . DS . 'autoload.php';

$ThisSubscribe = new \ThisSubscribe\PluginController();
$ThisSubscribe->registers();