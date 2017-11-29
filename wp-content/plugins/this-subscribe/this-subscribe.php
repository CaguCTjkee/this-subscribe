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

if ( defined( 'THEME_ROOT' ) === false ) {
	define( 'THEME_ROOT', realpath(dirname( __FILE__ )) );
}
if ( defined( 'DS' ) === false ) {
	define( 'DS', DIRECTORY_SEPARATOR );
}

include_once THEME_ROOT . DS . 'autoload.php';

// Register short code
add_shortcode( 'thisSubscribe', array( 'ThisSubscribe\Init', 'shortCode' ) );

// preferences