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

	public function __construct()
	{
		$this->templateRoot = PL_ROOT . DS . 'html';
	}

	/**
	 * @param $template
	 *
	 * @return string|void
	 */
	public function getTemplate( $template )
	{
		if($template === 'subs-form')
		{

			$pathToTemplate = $this->templateRoot . DS . $template . $this->templateExtension;

			if(is_file( $pathToTemplate ))
			{
				ob_start();

				include $pathToTemplate;

				return ob_get_clean();
			}
		}

		return;
	}
}