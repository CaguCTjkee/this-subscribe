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
	const UNSUBSCRIBER_PAGE_SLUG = 'this-unsubscribe';

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
			'wpts', array( 'ThisSubscribe\PluginController', 'subscribersAdmin' ) );

//		add_submenu_page( 'wpts', __( 'Templates This subscribe', 'this-subscribe-templates' ),
//			__( 'Templates', 'this-subscribe-templates' ), 'customize', 'wpts-templates', function () {
//				echo 'templates';
//			} );

		add_submenu_page( 'wpts', __( 'Settings', 'this-subscribe-setting' ),
			__( 'Settings', 'this-subscribe-setting' ), 'manage_options', SettingsPage::MENU_SLUG, array(
				'ThisSubscribe\SettingsPage',
				'createAdminPage'
			) );
	}

	public function pluginAdminInit() {

		// SettingsPage
		$settingsPage = new SettingsPage();
		$settingsPage->pageInit();

		// Subscriber page backend
		$adminFrontEnd = new AdminFrontEnd();
		$adminFrontEnd->subscribersPageAction();
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
				$subscriber->hash = wp_hash_password( $mail . SECURE_AUTH_SALT );
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

	public function changeMail() {
		$result = array();

		// Remove cookie
		setcookie( Subscriber::COOKIE, '', time() - 3600, '/' );

		// Return def template
		$result['html'] = $this->getTemplate( 'subs-form' );

		echo json_encode( $result );
		wp_die();
	}

	public function abortSubscriber() {
		$result = array();

		$subscriberId = ! empty( $_COOKIE[ Subscriber::COOKIE ] ) ? (int) $_COOKIE[ Subscriber::COOKIE ] : null;

		if ( $subscriberId !== null ) {

			// Get subscriber
			$subscriber = new Subscriber( $subscriberId );
			if ( $subscriber->id !== null ) {

				// Send mail with instructions
				$blogName      = get_option( 'blogname' );
				$subsAbortLink = 'https://caguct.com/abortSubscriber/' . $subscriber->hash;
				$subject       = 'Abort your subscriber';

				$message = 'If you want abort subscriber on site ' . $blogName . ':' . PHP_EOL . PHP_EOL;
				$message .= 'just click on this link - <a href="' . $subsAbortLink . '">' . $subsAbortLink . '</a>';

				// Send email to admin
				wp_mail( $subscriber->mail, $subject, $message );

				// Return abort info
				$result['html'] = $this->getTemplate( 'abort-info' );

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

		$atts = shortcode_atts( array(), $attributes );

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
	 * [thisUnSubscribe] short code
	 *
	 * @param $attributes
	 *
	 * @return bool|string
	 */
	public function thisUnSubscribeShortCode( $attributes ) {
		global $wpdb;

		$atts = shortcode_atts( array(), $attributes );

		$subscriberHash = ! empty( $_GET[ Subscriber::HASH ] ) ? $_GET[ Subscriber::HASH ] : null;

		// If user already subscribed
		if ( $subscriberHash !== null ) {

			// Sanitize and encode
			$subscriberHash = sanitize_text_field( urldecode( $subscriberHash ) );

			// Get subscribe
			$subscriber = new Subscriber( $subscriberHash );

			if ( $subscriber->id !== null ) {

				// Unsubscribe
				if ( $subscriber->api->unSubscribe( $subscriber ) === true ) {
					return $this->getTemplate( 'unsubscribed', $subscriber );
				}
			}
		}

		// View subscribe template
		return $this->getTemplate( 'unsubscribed-fail' );
	}

	/**
	 * Return html template
	 *
	 * @param $template
	 * @param array|object $vars
	 *
	 * @return null|string
	 */
	public function getTemplate( $template, $vars = array() ) {
		$pathToTemplate = PL_TEMPLATES . DS . $template . '.' . self::TEMPLATE_EXT;

		if ( is_file( $pathToTemplate ) ) {

			// extract vars from object
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

		return null;
	}

	/**
	 * Get class name without namespace
	 *
	 * @param $object
	 *
	 * @return mixed|null
	 */
	public function getClassNameFromObject( $object ) {
		if ( is_object( $object ) ) {

			$classNameWithNamespace = get_class( $object );
			$classNameWithNamespace = explode( "\\", $classNameWithNamespace );
			$className              = array_pop( $classNameWithNamespace );

			return $className;
		}

		return null;
	}

	/**
	 * Add un subscriver page to wordpress if not exist
	 */
	public function addUnSubscriberPage() {
		$unSubscriberPage = get_page_by_path( self::UNSUBSCRIBER_PAGE_SLUG );
		if ( $unSubscriberPage === null ) {
			// add page
			$page = array(
				'post_author'  => 1,
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => __( 'Unsubscribe' ),
				'post_name'    => self::UNSUBSCRIBER_PAGE_SLUG,
				'post_content' => '[thisUnSubscribe]',
			);
			wp_insert_post( $page );
		}
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

		$replace = array(
			'[blogname]'   => get_option( 'blogname' ),
			'[post-url]'   => get_permalink( $post->ID ),
			'[abort-link]' => '',
		);

		$unsubscriberLink = get_bloginfo( 'wpurl' ) . '/' . self::UNSUBSCRIBER_PAGE_SLUG . '?' . Subscriber::HASH . '=';

		$subscribersApi = new SubscriberApi();
		$subscribers    = $subscribersApi->getSubscribers();
		if ( $subscribers !== null ) {
			foreach ( $subscribers as $subscriber ) {
				if ( $subscriber->signed > 0 ) {
					$replace['[abort-link]'] = $unsubscriberLink . urlencode( $subscriber->hash );

					$subject = strtr( SettingsPage::getOption( 'post_subject' ), $replace );
					$message = strtr( SettingsPage::getOption( 'post_message' ), $replace );
					wp_mail( $subscriber->mail, $subject, $message );
				}
			}
		}
	}
}