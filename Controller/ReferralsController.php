<?php
App::uses('AppController', 'Controller');


class ReferralsController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'Referrals';
	
/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('UserReferral');
		
/**	
 * Controller actions for which authorization is not required.
 *
 * @var array
 */
	public $allowedActions = array('admin_index');
	
	
	
/**
 * Called before the controller action.
 *
 */
	public function admin_index($limit = 0)
	{
		// Apply 'admin' layout.
		
		$this->layout = 'admin';
		
		
		// Referred users.
		
		if (!empty($this->loggedUser['User']['id']))
		{
			$referrals = $this->api_index($limit);	
		}
		else
		{
			$referrals = array();
		}
		
		
		// Count referred users.
		
		if (!empty($this->loggedUser['User']['id']))
		{
			$count = $this->api_count();
		}
		else
		{
			$count = 0;
		}
		
		
		$this->set(compact('count', 'referrals'));
		$this->set('title_for_layout', __d('controller', 'Referrals.admin_index.title'));
		
		return array($referrals, $count);
	}
	
	
	
/**
 * Counts the referred users.
 *
 * @return array
 */
	public function api_count()
	{
		$referrals = $this->UserReferral->find('count',
			array(
				'conditions' => array(
					'UserReferral.referral_id' => $this->loggedUser['User']['id'],
					'UserReferral.is_enabled' => true
				)
			)
		);
		
		
		$this->set(compact('referrals'));
		$this->set('_serialize', array('referrals'));
		
		return $referrals;
	}
	
	
/**
 * Lists the referred users.
 *
 * @return array
 */
	public function api_index($limit = 0)
	{
		$referrals = $this->UserReferral->find('all',
			array(
				'contain' => array(
					'User' => array(
						'FacebookUser'
					)
				),
				'conditions' => array(
					'UserReferral.referral_id' => $this->loggedUser['User']['id'],
					'UserReferral.is_enabled' => true
				),
				'limit' => $limit
			)
		);
		
		
		$this->set(compact('referrals'));
		$this->set('_serialize', array('referrals'));
		
		return $referrals;
	}
}
