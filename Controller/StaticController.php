<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 */
class StaticController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Static';

	/**
	 * 
	 * Disable auth for notifications
	 */
	function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allowedActions = array('js');
	}
		
/**
 * Returns Javascript variables
 *
 * @return string
 */
	public function js() {
		
		$output =  Array();
		
		$output["appName"] 	= Configure::read("appName");
		$output["appUrl"] 	= Configure::read("appUrl");
		$output["baseUrl"] 	= Configure::read("baseUrl");
		$output["staticUrl"] = Configure::read("staticUrl");
		$output["accessTokenFB"] = $this->FB->getAccessToken();
		
		
		Configure::write('debug', 0);
		header("content-type: text/javascript");
		// TODO: Write a template that prints that
		echo 'BZ = $.extend(BZ,JSON.parse(\''.json_encode($output).'\'));';
		exit;
	}
}
