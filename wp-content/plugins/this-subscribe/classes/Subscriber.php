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

	const TABLE = 'ts_mail';
	const COOKIE = 'this_subscriber_id';

	// fields
	public $mail;

	public function __construct( $id_or_mail = null ) {

		$api = new SubscriberApi();

		if ( $id_or_mail !== null ) {

			$subscriber = $api->getSubscriber( array( 'id' => $id_or_mail, 'mail' => $id_or_mail ), 'OR' );

			if ( $subscriber !== null ) {
				$this->setter( $subscriber );
			}
		}
	}

	/**
	 * Save subscriber
	 */
	public function save() {
		global $wpdb;

		if ( $this->mail !== null ) {
			// Get subs
			$subscriber = new self( $this->mail );
			// If not found subscriber - add
			if ( $subscriber->id === null ) {

				// Current time
				$this->time = current_time( 'mysql' );

				// Add new subscriber
				$insert = $wpdb->insert( $wpdb->prefix . self::TABLE, array(
					'time' => $this->time,
					'mail' => sanitize_text_field( $this->mail )
				) );

				if ( $insert !== false ) {
					// Add ID
					$this->id = $wpdb->insert_id;
				}

			} else {
				$this->setter( $subscriber );
				// todo: update?
			}
		} else {
			// todo: Exception
		}
	}

	/**
	 * @param object|array $object_or_array
	 */
	private function setter( $object_or_array ) {
		if ( is_object( $object_or_array ) ) {
			$this->id   = $object_or_array->id;
			$this->time = $object_or_array->time;
			$this->mail = $object_or_array->mail;
		}
		if ( is_array( $object_or_array ) ) {
			$this->id   = $object_or_array['id'];
			$this->time = $object_or_array['time'];
			$this->mail = $object_or_array['mail'];
		}
	}
}