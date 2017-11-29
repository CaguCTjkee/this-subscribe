<?php
/**
 * Created by PhpStorm.
 * User: CaguCT
 * Date: 11/29/17
 * Time: 12:27
 */

namespace ThisSubscribe;

class Init {
	//	private $html_root = THEME_ROOT . DS . 'html';
	static $templateRoot = 'html';
	static $templateExtension = '.php';

	/**
	 * [thisSubscribe]
	 *
	 * @param $attributes
	 *
	 * @return string|void
	 */
	public static function shortCode( $attributes ) {
		$atts = shortcode_atts( array(), $attributes );

		return self::getTemplate( 'subs-form' );
	}

	/**
	 * @param $template
	 *
	 * @return string|void
	 */
	public static function getTemplate( $template ) {
		if ( $template === 'subs-form' ) {

			$pathToTemplate = THEME_ROOT . DS . 'html' . DS . $template . self::$templateExtension;

			if ( is_file( $pathToTemplate ) ) {
				ob_start();

				include $pathToTemplate;

				return ob_get_clean();
			}
		}

		return;
	}
}
//