<?php
App::uses('AppController', 'Controller');


class UsersController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'Users';
	
/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('User', 'Log');

/**	
 * Controller actions for which authorization is not required.
 *
 * @var array
 */
	public $allowedActions = array('login', 'logout');
	
	
	
/**
 * Called before the controller action.
 *
 */
	public function beforeFilter()
	{
		// If the requested action has been 'login', disable the login request.
		
		if (in_array(strtolower($this->request->params['action']), array('login')))
		{
			$this->loginDisabled = true;
		}
		
		parent::beforeFilter();
	}
	
	
	
/**	
 * Proceeds with user login.
 *
 */
	public function login()
	{
		// Apply 'empty' layout.
		
		$this->layout = 'empty';
		
		
		// Indicates if was executed successfully.
		
		$success = true;
		
		
		// Sets the Facebook login URL.
		
		if (!empty($this->request->query['source']))
		{
			$source = $this->request->query['source'];
		}
		else
		{
			$source = '/';
		}
		
		$this->set('fb_login_url', $this->FB->getLoginUrl(array('scope' => Configure::read('Facebook.Permissions'), 'redirect_uri' => Configure::read('Facebook.redirect') . $source)));	
		
		
		$this->set(compact('success'));
		$this->set('_serialize', array('success'));
		
		$this->Session->delete('loginRedirect');
		return $success;
	}
	
	
/**	
 * Proceeds with user logout.
 *
 */
	public function logout()
	{
		// Indicates if was executed successfully.
		
		$success = true;
		
		
		// Creates a log entry
		
		#$this->Log->create();
		#$this->Log->save(
		#	array(
		#		'Log' => array(
		#			'action' => 'logout'
		#		)
		#	)
		#);
		
		
		// Terminates current session.
		
		$this->Auth->logout();
		$this->Session->delete('loginRedirect');
		
		$this->set(compact('success'));
		$this->set('_serialize', array('success'));
		
		return $success;
	}
}
