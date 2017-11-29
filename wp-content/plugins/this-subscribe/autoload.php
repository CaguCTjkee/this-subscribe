<?php
/**
 * Created by PhpStorm.
 * User: CaguCT
 * Date: 11/29/17
 * Time: 12:24
 */

spl_autoload_register( 'thisSubscribe_autoloader' );
function thisSubscribe_autoloader( $class_name ) {

	if ( false !== strpos( $class_name, 'ThisSubscribe' ) ) {
		$classes_dir = THEME_ROOT . DS . 'classes' . DS;

		$class_name = explode( '\\', $class_name );

		$class_file = $classes_dir . $class_name[1] . '.php';

		if ( is_file( $class_file ) ) {
			require_once $class_file;
		}
	}
}