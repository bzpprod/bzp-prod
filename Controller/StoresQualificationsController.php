<?php
App::uses('AppController', 'Controller');


class StoresQualificationsController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'StoresQualifications';
	
/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('Store', 'Qualification', 'ViewStoreQualification', 'Transaction');
	
/**	
 * Controller actions for which authorization is not required.
 *
 * @var array
 */
	public $allowedActions = array('index', 'fanpage_index');
	
/**	
 * Paginator settings.
 *
 * @var array
 */
	public $paginate = array(
		'ViewStoreQualification' => array(
			'contain' => array(
				'Store'
			),
			'order' => array(
				'ViewStoreQualification.created' => 'desc'
			),
			'limit' => 10
		)
	);
	
	
	
/**
 * List the stores qualifications according to applied filter.
 *
 * @param mixed $store
 * @return array
 */
	public function index($store)
	{
		// Apply 'store' layout.
		
		$this->layout = 'store';
		
		
		// Checks if the store exists.
		
		$store = $this->__store($store);
		
		if (empty($store))
		{
			$this->redirect($this->referrer(array('controller' => 'products', 'action' => 'home', 'admin' => false)), null, true);
		}else{
			$store['Store']['totalFollowers'] = $this->StoreProduct->getTotalFollowers($store['Store']['id']);
		}
		
		
		// Default conditions.
		
		$qualificationsConditions = array('Store.id' => $store['Store']['id'], 'ViewStoreQualification.is_deleted' => false);
		
		
		// Filter conditions.
		
		if (!empty($this->params['named']['filter']))
		{
			$filter = $this->params['named']['filter'];
			
			if (strcasecmp($filter, 'positive') == 0)
			{
				$qualificationsConditions['ViewStoreQualification.qualification'] = 'positive';
			}
			else if (strcasecmp($filter, 'negative') == 0)
			{
				$qualificationsConditions['ViewStoreQualification.qualification'] = 'negative';
			}
		}
		
		
		// Finds the qualifications applying conditions and extracts the qualifications ids.
		
		$qualifications = $this->paginate('ViewStoreQualification', $qualificationsConditions);
		$qualificationsIds = Set::extract('/ViewStoreQualification/id', $qualifications);
		
		
		// Gets the qualifications according to extracted ids, but now formatted with the necessary data.
		
		$qualifications = array();
		
		foreach ($qualificationsIds as $key => $value)
		{
			$qualifications[] = $this->Qualification->find('first',
				array(
					'contain' => array(
						'Transaction',
						'User' => array(
							'FacebookUser'
						)
					),
					'conditions' => array(
						'Qualification.id' => $value
					)
				)
			);	
		}
		
		
		// Count all transactions.
		
		$transactions = $this->Transaction->find('count',
			array(
				'conditions' => array(
					'Store.id' => $store['Store']['id']
				)
			)
		);
		
		
		$this->set(compact('store', 'qualifications', 'transactions'));
		$this->set('title_for_layout', String::insert(__d('controller', 'StoresQualifications.index.title'), array('store' => $store['Store']['title'])));
		
		return array($store, $qualifications, $transactions);
	}
	
	
/**
 * List the stores qualifications according to applied filter.
 *
 */
	public function fanpage_index($store)
	{
		// Checks if the specified store is valid.
		
		$store = $this->__fanpageStore($store);
		
		if (empty($store))
		{
			$this->redirect(array('controller' => 'products', 'action' => 'home', 'fanpage' => true), null, true);
		}
		
		
		// Executes the index action.
		
		list($store, $qualifications, $transactions) = $this->index($store['Store']['id']);
		
		
		// Apply 'fanpage_default' layout.
		
		$this->layout = 'fanpage_default';
		
		
		$this->set('title_for_layout', String::insert(__d('controller', 'StoresQualifications.fanpage_index.title'), array('store' => $store['Store']['title'])));
	}
}
