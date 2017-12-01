<?php
/**
 * Created by PhpStorm.
 * User: CaguCT
 * Date: 12/1/17
 * Time: 09:17
 */

namespace ThisSubscribe;

/**
 * Api for subscribers
 *
 * Class SubscriberApi
 * @package ThisSubscribe
 */
class SubscriberApi {

	const DB_VERSION_OPTION_NAME = 'ts_db_version';
	const DB_VERSION = '1.1';

	/**
	 * Creating Tables with Plugins (https://codex.wordpress.org/Creating_Tables_with_Plugins)
	 */
	public function install() {
		global $wpdb;

		$table_name = $wpdb->prefix . Subscriber::TABLE;

		if ( $wpdb->get_var( 'show tables like "' . $table_name . '"' ) != $table_name ) {
			$sql = 'CREATE TABLE ' . $table_name . ' (
					  id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
					  time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
					  mail TINYTEXT NOT NULL,
					  hash VARCHAR(255) NOT NULL,
					  PRIMARY KEY (id)
					);';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql );

			add_option( self::DB_VERSION_OPTION_NAME, self::DB_VERSION );
		}
	}

	/**
	 * Update Tables with Plugins (https://codex.wordpress.org/Creating_Tables_with_Plugins)
	 */
	public function update() {
		global $wpdb;

		$sql           = null;
		$installed_ver = get_option( self::DB_VERSION_OPTION_NAME );
		$table_name    = $wpdb->prefix . Subscriber::TABLE;

		if ( $installed_ver != self::DB_VERSION ) {

			if ( self::DB_VERSION === '1.1' ) {
				$sql = 'ALTER TABLE ' . $table_name . ' ADD `hash` VARCHAR(255) NOT NULL AFTER `mail`;';
			}

			if ( $sql !== null ) {

				$wpdb->query( $sql );

				update_option( self::DB_VERSION_OPTION_NAME, self::DB_VERSION );

			}
		}
	}

	/**
	 * Get subscriber
	 *
	 * @param array $where
	 * @param string $glue Optional. Any of AND | OR
	 * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 *
	 * @return array|null|object
	 */
	public function getSubscriber( $where = null, $glue = 'AND', $output = OBJECT ) {
		global $wpdb;

		$table_name = $wpdb->prefix . Subscriber::TABLE;

		// If we have conditional for sql request
		$where = $this->whereConditional( $where, $glue );

		$subscriber = $wpdb->get_row( 'SELECT * FROM ' . $table_name . $where, $output );

		return $subscriber;
	}


	/**
	 * Get subscribers
	 *
	 * @param array|string $where
	 * @param string $glue Optional. Any of AND | OR
	 *
	 * @return array|null
	 */
	public function getSubscribers( $where = null, $glue = 'AND' ) {
		global $wpdb;

		$table_name = $wpdb->prefix . Subscriber::TABLE;

		// If we have conditional for sql request
		$where = $this->whereConditional( $where, $glue );

		$get_results = $wpdb->get_results( 'SELECT * FROM ' . $table_name . $where );

		if ( $get_results ) {

			$subscribers = array();

			foreach ( $get_results as $get_result ) {
				$subscriber    = new Subscriber( $get_result->id );
				$subscribers[] = $subscriber;
			}

			return $subscribers;

		} else {
			return null;
		}
	}

	/**
	 * Get subscribers mails
	 *
	 * @return array|null
	 */
	public function getSubscribersMails() {

		$subscribers = $this->getSubscribers();

		if ( $subscribers !== null ) {
			$result = array();

			foreach ( $subscribers as $subscriber ) {
				$result[ $subscriber->id ] = $subscriber->mail;
			}

			return $result;
		}

		return null;
	}

	/**
	 * Helper function
	 *
	 * @param null $where
	 * @param string $glue Optional. Any of AND | OR
	 *
	 * @return null|string
	 */
	private function whereConditional( $where = null, $glue = 'AND' ) {
		if ( $where !== null ) {
			if ( is_array( $where ) ) {
				$where_temp = array();
				foreach ( $where as $key => $value ) {
					$where_temp[] = '`' . $key . '` = "' . sanitize_text_field( $value ) . '"';
				}
				$where = ' WHERE ' . implode( ' ' . $glue . ' ', $where_temp );
				unset( $where_temp );
			} else {
				$where = ' WHERE ' . $where;
			}
		}

		return $where;
	}

}