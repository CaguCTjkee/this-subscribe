<?php
/**
 * Created by PhpStorm.
 * User: CaguCT
 * Date: 11/29/17
 * Time: 13:30
 */

namespace ThisSubscribe;

/**
 * Plugin Api
 *
 * Class Api
 * @package ThisSubscribe
 */
class PluginApi {

	const TEMPLATE_EXT = 'php';

	public $subscribersApi;

	public function __construct() {
		$this->subscribersApi = new SubscriberApi();
	}

	/**
	 * Add plugin scripts
	 */
	public static function addScripts() {

		wp_enqueue_script( 'this-subscribe', PL_URL . 'assets/js/this-subscribe.js', array( 'jquery' ), 1.0, true );
		wp_localize_script( 'this-subscribe', 'ThisSubscribeAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	/**
	 * Add plugin menu
	 */
	public function pluginMenu() {
		global $_wp_last_object_menu;

		$_wp_last_object_menu ++;

		add_menu_page( __( 'This subscribe', 'this-subscribe' ), __( 'This subscribe', 'this-subscribe' ), 'customize',
			'wpts', null, 'dashicons-email-alt', $_wp_last_object_menu );

		add_submenu_page( 'wpts', __( 'Subscribers', 'this-subscribe' ), __( 'Subscribers', 'this-subscribe' ), 'customize',
			'wpts', function () {
				echo 'world';
			} );

		add_submenu_page( 'wpts', __( 'Templates This subscribe', 'this-subscribe-templates' ),
			__( 'Templates', 'this-subscribe-templates' ), 'customize', 'wpts-templates', function () {
				echo 'templates';
			} );

		add_submenu_page( 'wpts', __( 'Preference This subscribe', 'this-subscribe-preference' ),
			__( 'Preference', 'this-subscribe-preference' ), 'customize', 'wpts-preference', function () {
				echo 'pref';
			} );
	}

	/**
	 * Add subscriber from mail
	 */
	public function addMail() {

		$mail   = ! empty( $_REQUEST['mail'] ) ? $_REQUEST['mail'] : null;
		$result = array();

		if ( $mail !== null ) {

			$subscriber = new Subscriber( $mail );
			// If subscriber not found
			if ( $subscriber->id === null ) {

				// Add new Subscriber
				$subscriber->mail = $mail;
				$subscriber->save();
			}

			if ( $subscriber->id !== null ) {

				// Send template
				$result['html'] = $this->getTemplate( 'subscribed', $subscriber );

				// Save subscriber to cookies one year
				$one_year_timestamp = time() + 3600 * 24 * 365;
				setcookie( Subscriber::COOKIE, $subscriber['id'], $one_year_timestamp, '/' );
			}
		}

		echo json_encode( $result );
		wp_die();
	}

	/**
	 * [thisSubscribe] short code
	 *
	 * @param $attributes
	 *
	 * @return bool|string
	 */
	public function shortCode( $attributes ) {
		global $wpdb;

		// $atts       = shortcode_atts(array(), $attributes);

		$subscriberId = ! empty( $_COOKIE[ Subscriber::COOKIE ] ) ? (int) $_COOKIE[ Subscriber::COOKIE ] : null;

		// If user already subscribed
		if ( $subscriberId !== null ) {

			// Get subscribe
			$subscriber = new Subscriber( $subscriberId );

			if ( $subscriber->id !== null ) {
				return $this->getTemplate( 'subscribed', $subscriber );
			}
		}

		// View subscribe template
		return $this->getTemplate( 'subs-form' );
	}

	/**
	 * Return html template
	 *
	 * @param              $template
	 * @param array|object $vars
	 *
	 * @return bool|string
	 */
	public function getTemplate( $template, $vars = array() ) {
		$pathToTemplate = PL_TEMPLATES . DS . $template . '.' . self::TEMPLATE_EXT;

		if ( is_file( $pathToTemplate ) ) {

			// extract vars
			if ( is_object( $vars ) ) {
				$vars = get_object_vars( $vars );
			}
			if ( $vars ) {
				extract( $vars );
			}

			ob_start();

			include $pathToTemplate;

			$template = ob_get_clean();

			return $template;
		}

		return false;
	}

	/**
	 * Send mail wen we insert post
	 *
	 * @param \WP_Post $post
	 *
	 * @return void
	 */
	public function sendInsertPost( \WP_Post $post ) {

		if ( wp_is_post_revision( $post->ID ) ) {
			return;
		}

		$blogName = get_option( 'blogname' );
		$post_url = get_permalink( $post->ID );
		$subject  = 'A post has been updated';

		$message = 'A post has been updated on site ' . $blogName . ':' . PHP_EOL . PHP_EOL;
		$message .= $post->post_title . ": " . $post_url;

		// Send email to admin
		wp_mail( $this->getSubscribersMails(), $subject, $message );
	}
}