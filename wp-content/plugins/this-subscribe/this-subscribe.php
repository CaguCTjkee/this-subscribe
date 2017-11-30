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

if( defined('PL_ROOT') === false )
{
	define('PL_ROOT', realpath(dirname(__FILE__)));
}
if( defined('PL_URL') === false )
{
	define('PL_URL', plugin_dir_url(__FILE__));
}
if( defined('DS') === false )
{
	define('DS', DIRECTORY_SEPARATOR);
}

include_once PL_ROOT . DS . 'autoload.php';

// Register short code
add_shortcode('thisSubscribe', array('ThisSubscribe\Init', 'shortCode'));

// Register send mail when post insert
add_action('wp_insert_post', array('ThisSubscribe\Init', 'insertPost'), 10, 3);

// Register plugin scripts
add_action('wp_enqueue_scripts', array('ThisSubscribe\Init', 'addScripts'));

// Register ajax event for subscribe
add_action('wp_ajax_add_mail', array('ThisSubscribe\Init', 'addMail'));
add_action('wp_ajax_nopriv_add_mail', array('ThisSubscribe\Init', 'addMail'));

// Add admin menu
add_action('admin_menu', array('ThisSubscribe\Init', 'pluginMenu'));

// Add "ts_mails" table to WP when plugin activating
register_activation_hook(__FILE__, array('ThisSubscribe\Init', 'install'));