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

if(defined('PL_ROOT') === false)
{
	define('PL_ROOT', realpath(dirname(__FILE__)));
}
if(defined('PL_URL') === false)
{
	define('PL_URL', plugin_dir_url(__FILE__));
}
if(defined('DS') === false)
{
	define('DS', DIRECTORY_SEPARATOR);
}

include_once PL_ROOT . DS . 'autoload.php';

// Register short code
add_shortcode('thisSubscribe', array('ThisSubscribe\Init', 'shortCode'));

// Register plugin scripts
add_action('wp_enqueue_scripts', array('ThisSubscribe\Init', 'addScripts'));

// Register ajax event
add_action('wp_ajax_add_mail', array('ThisSubscribe\Init', 'addMail'));

// Add new table to WP
register_activation_hook(__FILE__, array('ThisSubscribe\Init', 'install'));


// preferences