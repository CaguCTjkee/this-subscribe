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
	 * Get instance
	 *
	 * @return null|Init
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
		global $wpdb;

		$table_name = $wpdb->prefix . self::getInstance()->api->getTsMailsTableName();

		if ( $wpdb->get_var( 'show tables like "' . $table_name . '"' ) != $table_name ) {
			$sql = 'CREATE TABLE ' . $table_name . ' (
					  id mediumint(9) NOT NULL AUTO_INCREMENT,
					  time datetime DEFAULT "0000-00-00 00:00:00" NOT NULL,
					  mail tinytext NOT NULL,
					  PRIMARY KEY (id)
					);';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql );

			add_option( 'ts_db_version', self::getInstance()->api->getDbVersion() );
		}

	}

	/**
	 * [thisSubscribe] short code
	 *
	 * @param $attributes
	 *
	 * @return bool|string
	 */
	public static function shortCode( $attributes ) {
		global $wpdb;

		// $atts       = shortcode_atts(array(), $attributes);

		$api        = self::getInstance()->api;
		$table_name = $wpdb->prefix . $api->getTsMailsTableName();

		$subscriberId = ! empty( $_COOKIE[ $api->getSubsCookieName() ] ) ? (int) $_COOKIE[ $api->getSubsCookieName() ] : null;

		// If user already subscribed
		if ( ! empty( $subscriberId ) ) {
			// Get subscribe
			$subscriber = $wpdb->get_row( 'SELECT * FROM ' . $table_name . ' 
										  WHERE id = ' . $subscriberId, 'ARRAY_A' );

			if ( $subscriber ) {
				return $api->getTemplate( 'subscribed', $subscriber );
			}
		}

		// View subscribe template
		return $api->getTemplate( 'subs-form' );
	}

	/**
	 * Add plugin scripts
	 */
	public static function addScripts() {
		wp_enqueue_script( 'this-subscribe', PL_URL . 'assets/js/this-subscribe.js', array( 'jquery' ), 1.0, true );
		wp_localize_script( 'this-subscribe', 'ThisSubscribeAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	/**
	 * Add subscriber action
	 */
	public static function addMail() {
		global $wpdb;

		$api  = self::getInstance()->api;
		$json = array();
		$mail = ! empty( $_POST['mail'] ) ? sanitize_text_field( $_POST['mail'] ) : null;

		if ( ! empty( $mail ) ) {
			$subscriber = $api->addNewSubscriber( $mail, ARRAY_A );

			if ( $subscriber ) {
				// Send template
				$json['html'] = $api->getTemplate( 'subscribed', $subscriber );

				// Save subscriber to cookies one year
				setcookie( $api->getSubsCookieName(), $subscriber['id'], time() + 3600 * 24 * 365, '/' );
			}
		}

		echo json_encode( $json );
		wp_die();
	}

	/**
	 * Action wp_insert_post
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param bool $update
	 *
	 * return void
	 */
	public static function insertPost( $post_id, \WP_Post $post, $update ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		self::getInstance()->api->sendInsertPost( $post );
	}

	/**
	 * Action admin_menu
	 */
	public static function pluginMenu() {
		global $_wp_last_object_menu;

		$_wp_last_object_menu ++;

		add_menu_page( __( 'This subscribe', 'this-subscribe' ), __( 'This subscribe', 'this-subscribe' ), 'customize',
			'wpts', null, 'dashicons-email-alt', $_wp_last_object_menu );

		add_submenu_page( 'wpts', __( 'Subscribers', 'this-subscribe' ), __( 'Subscribers', 'this-subscribe' ), 'customize',
			'wpts', function () {
				echo 'world';
			} );

		add_submenu_page( 'wpts', __( 'Preference This subscribe', 'this-subscribe-preference' ),
			__( 'Preference', 'this-subscribe-preference' ), 'customize', 'wpts-preference', function () {
				echo 'pref';
			} );
	}
}