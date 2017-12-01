<?php
/**
 * Created by PhpStorm.
 * User: CaguCT
 * Date: 11/30/17
 * Time: 20:35
 */

namespace ThisSubscribe;

/**
 * Model for subscriber
 *
 * Class Subscriber
 * @package ThisSubscribe
 */
class Subscriber extends AbstractModel {

	const TABLE = 'ts_mails';
	const COOKIE = 'this_subscriber_id';

	// fields
	public $mail;
	public $hash;

	public function __construct( $id_or_mail = null ) {

		$api = new SubscriberApi();

		if ( $id_or_mail !== null ) {

			$subscriber = $api->getSubscriber( array( 'id' => $id_or_mail, 'mail' => $id_or_mail ), 'OR' );

			if ( $subscriber !== null ) {
				$this->setter( $subscriber );
			}
		}
	}

	public function remove() {
		global $wpdb;
	}

	/**
	 * Save subscriber
	 */
	public function save() {

		$update_flag = false;

		// Anyway we need mail, for update or add
		if ( $this->mail === null ) {
			// todo: Exception
			return false;
		}

		if ( $this->id === null ) {
			// Get subscriber
			$subscriber = new self( $this->mail );
			if ( $subscriber->id === null ) {
				// Add subscriber
				$this->add();
			} else {
				$update_flag = true;
			}
		} else {
			$update_flag = true;
		}

		if ( $update_flag ) {
			$this->update();
		}
	}

	private function add() {
		global $wpdb;

		// Current time
		$this->time = current_time( 'mysql' );

		// Hash
		if ( $this->hash === null ) {
			$this->hash = wp_hash_password( $this->mail . SECURE_AUTH_SALT );
		}

		// Add new subscriber
		$insert = $wpdb->insert( $wpdb->prefix . self::TABLE, array(
			'time' => $this->time,
			'mail' => sanitize_text_field( $this->mail ),
			'hash' => sanitize_text_field( $this->hash )
		) );

		if ( $insert !== false ) {
			$this->id = $wpdb->insert_id;
		}
	}

	private function update() {
		global $wpdb;

	}

	/**
	 * @param object|array $object_or_array
	 */
	private function setter( $object_or_array ) {
		if ( is_object( $object_or_array ) ) {
			$this->id   = $object_or_array->id;
			$this->time = $object_or_array->time;
			$this->mail = $object_or_array->mail;
			$this->hash = $object_or_array->hash;
		}
		if ( is_array( $object_or_array ) ) {
			$this->id   = $object_or_array['id'];
			$this->time = $object_or_array['time'];
			$this->mail = $object_or_array['mail'];
			$this->hash = $object_or_array['hash'];
		}
	}
}