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

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';

// Register short code
add_shortcode( 'thisSubscribe', array('ThisSubscribe', 'shortCode') );
//

// preferences