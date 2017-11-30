<?php
/**
 * Created by PhpStorm.
 * User: CaguCT
 * Date: 11/29/17
 * Time: 13:30
 */

namespace ThisSubscribe;

class Api {

	// Privates
	private $templateRoot = null;
	private $templateExtension = '.php';
	private $tsMailsTableName = 'ts_mails';
	private $dbVersion = '1.0';
	private $subs_cookie_name = 'this_subscriber_id';

	public function __construct() {
		$this->templateRoot = PL_ROOT . DS . 'html';
	}

	/**
	 * @return string
	 */
	public function getDbVersion() {
		return $this->dbVersion;
	}

	/**
	 * @return string
	 */
	public function getSubsCookieName() {
		return $this->subs_cookie_name;
	}

	/**
	 * @return string
	 */
	public function getTsMailsTableName() {
		return $this->tsMailsTableName;
	}

	/**
	 * Return html template
	 *
	 * @param       $template
	 * @param array $vars
	 *
	 * @return bool|string
	 */
	public function getTemplate( $template, $vars = array() ) {
		$pathToTemplate = $this->templateRoot . DS . $template . $this->templateExtension;

		if ( is_file( $pathToTemplate ) ) {
			// extract vars
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
	 * Add new subscriber
	 *
	 * @param string $mail Format mail@mail.com
	 * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 *
	 * @return array|bool
	 */
	public function addNewSubscriber( $mail, $output = OBJECT ) {
		global $wpdb;

		$table_name = $wpdb->prefix . $this->tsMailsTableName;

		// Get subscriber or null
		$subscriber = $this->getSubscriber( array( 'mail' => $mail ), $output );

		if ( $subscriber === null ) {
			// Add new subscriber
			$wpdb->insert( $table_name, array(
				'time' => current_time( 'mysql' ),
				'mail' => $mail,
			) );

			// Get subscriber
			$subscriber = $this->getSubscriber( array( 'mail' => $mail ), $output );
		}

		return $subscriber;
	}

	/**
	 * Get subscriber
	 *
	 * @param array $where
	 * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 *
	 * @return array|null|object
	 */
	public function getSubscriber( $where = array(), $output = OBJECT ) {
		global $wpdb;

		$table_name = $wpdb->prefix . $this->tsMailsTableName;

		// If we have conditional for sql request
		if ( $where ) {
			$where_temp = array();
			foreach ( $where as $key => $value ) {
				$where_temp[] = '`' . $key . '` = "' . sanitize_text_field( $value ) . '"';
			}
			$where = ' WHERE ' . implode( ', ', $where_temp );
			unset( $where_temp );
		}

		$subscriber = $wpdb->get_row( 'SELECT * FROM ' . $table_name . $where, $output );

		return $subscriber;
	}

	/**
	 * Get subscribers
	 *
	 * @param array $where
	 * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 *
	 * @return array|null|object
	 */
	public function getSubscribers( $where = array(), $output = OBJECT ) {
		global $wpdb;

		$table_name = $wpdb->prefix . $this->tsMailsTableName;

		// If we have conditional for sql request
		if ( $where ) {
			$where_temp = array();
			foreach ( $where as $key => $value ) {
				$where_temp[] = '`' . $key . '` = "' . sanitize_text_field( $value ) . '"';
			}
			$where = ' WHERE ' . implode( ', ', $where_temp );
			unset( $where_temp );
		}

		$subscribers = $wpdb->get_results( 'SELECT * FROM ' . $table_name . $where, $output );

		return $subscribers;
	}

	/**
	 * Get subscribers mails
	 *
	 * @return array
	 */
	public function getSubscribersMails() {
		$mails = array();

		$subscribers = $this->getSubscribers();

		if ( $subscribers ) {
			foreach ( $subscribers as $subscriber ) {
				$mails[ $subscriber->id ] = $subscriber->mail;
			}
		}

		return $mails;
	}

	/**
	 * Send mail wen we insert post
	 *
	 * @param \WP_Post $post
	 *
	 * @return bool
	 */
	public function sendInsertPost( \WP_Post $post ) {
		$blogName = get_option( 'blogname' );
		$post_url = get_permalink( $post->ID );
		$subject  = 'A post has been updated';

		$message = 'A post has been updated on site ' . $blogName . ':' . PHP_EOL . PHP_EOL;
		$message .= $post->post_title . ": " . $post_url;

		// Send email to admin
		return wp_mail( $this->getSubscribersMails(), $subject, $message );
	}
}