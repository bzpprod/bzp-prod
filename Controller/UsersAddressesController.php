<?php
App::uses('AppController', 'Controller');


class UsersAddressesController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'UsersAddresses';
	
/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('User');
	
	
	
/**
 * Lists the user addresses.
 *
 * @return array
 */	
	public function api_index()
	{
		$addresses = $this->User->Address->find('all',
			array(
				'conditions' => array(
					'Address.model' => 'User',
					'Address.foreign_key' => $this->loggedUser['User']['id'],
					'Address.is_deleted' => false,
				),
				'order' => array(
					'Address.is_default' => 'desc',
					'Address.created' => 'asc'
				)
			)
		);
		
		
		$this->set(compact('addresses'));
		$this->set('_serialize', array('addresses'));
		
		return $addresses;
	}
	
	
/**
 * Creates a address entry.
 *
 * @return array
 */	
	public function api_add()
	{
		// Indicates if was executed successfully.
		
		$success = false;
		
		
		// Execution errors.
		
		$error = null;
		
		
		// Checks if the new address has been submitted.
		
		if (($this->request->is('post') || $this->request->is('put')) && !empty($this->request->data['Address']))
		{
			$data = Sanitize::clean($this->request->data, array('encode' => false, 'escape' => false));
			
			
			$this->User->Address->create();
			
			if ($this->User->Address->save($data, array('validate' => 'first')))
			{
				$success = true;
			}
			else
			{
				$error = $this->User->Address->invalidFields();
			}
		}
		
		
		// Gets the updated list of addresses.
		
		$addresses = $this->api_index();
		
		
		$this->set(compact('success', 'error', 'addresses'));
		$this->set('_serialize', array('success', 'error', 'addresses'));
		
		return compact('success', 'error', 'addresses');
	}
	
	
/**
 * Updates the specified address.
 *
 * @return array
 */	
	public function api_edit($address)
	{
		// Indicates if was executed successfully.
		
		$success = false;
		
		
		// Execution errors.
		
		$error = null;
		
		
		// Checks if the address updated data has been submitted.
		
		if (($this->request->is('post') || $this->request->is('put')) && !empty($this->request->data['Address']))
		{
			// Checks if the specified address exists and belongs to logged user.
			
			$address = $this->User->Address->find('first',
				array(
					'conditions' => array(
						'Address.model' => 'User',
						'Address.foreign_key' => $this->loggedUser['User']['id'],
						'Address.hash' => $address,
						'Address.is_deleted' => false
					)
				)
			);
			
			if (!empty($address))
			{
				$data = Sanitize::clean($this->request->data, array('encode' => false, 'escape' => false));
				$data['Address']['id'] = $address['Address']['id'];
				
				if ($this->User->Address->save($data, array('validate' => 'first')))
				{
					$success = true;
				}
				else
				{
					$error = $this->User->Address->invalidFields();
				}	
			}
		}
		
		
		// Gets the updated list of addresses.
		
		$addresses = $this->api_index();
		
		
		$this->set(compact('success', 'error', 'addresses'));
		$this->set('_serialize', array('success', 'error', 'addresses'));
		
		return compact('success', 'error', 'addresses');
	}
	
	
/**
 * Deletes the specified address.
 *
 * @return array
 */
	public function api_delete($address)
	{
		// Indicates if was executed successfully.
		
		$success = false;
		
		
		// Checks if the specified address exists and belongs to logged user.
		
		$address = $this->User->Address->find('first',
			array(
				'conditions' => array(
					'Address.model' => 'User',
					'Address.foreign_key' => $this->loggedUser['User']['id'],
					'Address.hash' => $address,
					'Address.is_deleted' => false
				)
			)
		);
		
		if (!empty($address))
		{
			$success = true;
			
			
			// Deletes the address.
			
			$this->User->Address->delete($address['Address']['id']);
		}
		
		
		// Gets the updated list of addresses.
		
		$addresses = $this->api_index();
		
		
		$this->set(compact('success', 'addresses'));
		$this->set('_serialize', array('success', 'addresses'));
		
		return compact('success', 'addresses');
	}
}
