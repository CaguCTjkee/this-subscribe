<?php
/**
 * Created by PhpStorm.
 * User: CaguCT
 * Date: 11/29/17
 * Time: 12:27
 */

namespace ThisSubscribe;

/**
 * Plugin Controller
 *
 * Class Controller
 * @package ThisSubscribe
 */
class Controller {

	static $inst = null;

	public $api;

	public function __construct() {
		$this->api = new PluginApi();
	}

	/**
	 * Get instance
	 *
	 * @return null|Controller
	 */
	public static function getInstance() {

		if ( self::$inst == null ) {
			self::$inst = new self();
		}

		return self::$inst;
	}

	/**
	 * Creating Tables with Plugins (https://codex.wordpress.org/Creating_Tables_with_Plugins)
	 */
	public static function install() {

		$subscribersApi = new SubscriberApi();
		$subscribersApi->install();
	}

	/**
	 * @param $attributes
	 *
	 * @return bool|string
	 */
	public static function shortCode( $attributes ) {

		return self::getInstance()->api->shortCode( $attributes );
	}

	/**
	 * Add plugin scripts
	 */
	public static function addScripts() {

		self::getInstance()->api->addScripts();
	}

	/**
	 * Add subscriber action
	 */
	public static function addMail() {

		self::getInstance()->api->addMail();
	}

	/**
	 * Action wp_insert_post
	 *
	 * @param $post_id
	 * @param \WP_Post $post
	 */
	public static function insertPost( $post_id, \WP_Post $post ) {

		self::getInstance()->api->sendInsertPost( $post );
	}

	/**
	 * Action admin_menu
	 */
	public static function pluginMenu() {

		self::getInstance()->api->pluginMenu();
	}
}