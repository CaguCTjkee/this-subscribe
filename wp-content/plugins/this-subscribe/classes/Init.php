<?php
/**
 * Created by PhpStorm.
 * User: CaguCT
 * Date: 11/29/17
 * Time: 12:27
 */

namespace ThisSubscribe;

class Init {
	// Statics
	static $inst = null;
	// Publics
	public $api;

	public function __construct() {
		$this->api = new Api();
	}

	/**
	 * @return null|Init
	 */
	public static function getInstance() {
		if ( self::$inst == null ) {
			self::$inst = new self();
		}

		return self::$inst;
	}

	/**
	 * [thisSubscribe]
	 *
	 * @param $attributes
	 *
	 * @return string|void
	 */
	public static function shortCode( $attributes ) {
		$atts = shortcode_atts( array(), $attributes );

		return self::getInstance()->api->getTemplate( 'subs-form' );
	}

	/**
	 * Add plugin scripts
	 */
	public static function addScripts() {
		wp_enqueue_script( 'this-subscribe', PL_URL . 'assets/js/this-subscribe.js', array( 'jquery' ), 1.0, true );
		wp_localize_script( 'this-subscribe', 'ThisSubscribeAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	public static function addMail() {
		global $wpdb;

		echo $_POST['mail'];

		wp_die();
	}
}
//