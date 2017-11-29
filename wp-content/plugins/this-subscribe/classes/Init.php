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
	// Private
	private $tsMailsTableName = 'ts_mails';
	private $dbVersion = '1.0';

	public function __construct()
	{
		$this->api = new Api();
	}

	/**
	 * @return null|Init
	 */
	public static function getInstance()
	{
		if(self::$inst == null)
		{
			self::$inst = new self();
		}

		return self::$inst;
	}

	/**
	 * Creating Tables with Plugins (https://codex.wordpress.org/Creating_Tables_with_Plugins)
	 */
	public static function install()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . self::getInstance()->tsMailsTableName;

		if($wpdb->get_var('show tables like "' . $table_name . '"') != $table_name)
		{
			$sql = 'CREATE TABLE ' . $table_name . ' (
					  id mediumint(9) NOT NULL AUTO_INCREMENT,
					  time datetime DEFAULT "0000-00-00 00:00:00" NOT NULL,
					  mail tinytext NOT NULL,
					  PRIMARY KEY (id)
					);';

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			dbDelta($sql);

			add_option('ts_db_version', self::getInstance()->dbVersion);
		}

	}

	/**
	 * [thisSubscribe]
	 *
	 * @param $attributes
	 *
	 * @return string|void
	 */
	public static function shortCode($attributes)
	{
		$atts = shortcode_atts(array(), $attributes);

		return self::getInstance()->api->getTemplate('subs-form');
	}

	/**
	 * Add plugin scripts
	 */
	public static function addScripts()
	{
		wp_enqueue_script('this-subscribe', PL_URL . 'assets/js/this-subscribe.js', array('jquery'), 1.0, true);
		wp_localize_script('this-subscribe', 'ThisSubscribeAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
	}

	public static function addMail()
	{
		global $wpdb;

		echo $_POST['mail'];

		wp_die();
	}
}

//