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

// Register short code
add_shortcode( 'thisSubscribe', array( 'ThisSubscribe\Controller', 'shortCode' ) );

// Register send mail when post insert
add_action( 'wp_insert_post', array( 'ThisSubscribe\Controller', 'insertPost' ), 10, 3 );

// Register plugin scripts
add_action( 'wp_enqueue_scripts', array( 'ThisSubscribe\Controller', 'addScripts' ) );

// Register ajax event for subscribe
add_action( 'wp_ajax_add_subscriber_mail', array( 'ThisSubscribe\Controller', 'addMail' ) );
add_action( 'wp_ajax_nopriv_add_subscriber_mail', array( 'ThisSubscribe\Controller', 'addMail' ) );

// Register ajax event for change mail
add_action( 'wp_ajax_change_subscriber_mail', array( 'ThisSubscribe\Controller', 'changeMail' ) );
add_action( 'wp_ajax_nopriv_change_subscriber_mail', array( 'ThisSubscribe\Controller', 'changeMail' ) );

// Register ajax event for abort subscriber
add_action( 'wp_ajax_abort_subscriber', array( 'ThisSubscribe\Controller', 'abortSubscriber' ) );
add_action( 'wp_ajax_nopriv_abort_subscriber', array( 'ThisSubscribe\Controller', 'abortSubscriber' ) );

// Add admin menu
add_action( 'admin_menu', array( 'ThisSubscribe\Controller', 'pluginMenu' ) );

// Add "ts_mails" table to WP when plugin activating
register_activation_hook( __FILE__, array( 'ThisSubscribe\Controller', 'install' ) );

// Update DB
add_action( 'plugins_loaded', array( 'ThisSubscribe\Controller', 'update' ) );