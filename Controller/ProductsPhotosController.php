<?php
App::uses('AppController', 'Controller');


class ProductsPhotosController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'ProductsPhotos';
	
/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('Product');
	
	
/**	
 * Deletes the specified photo.
 *
 * @param string $photo
 * @param string $product
 * @param string $store  
 */
	public function admin_delete($photo, $product, $store)
	{
		// Checks if the specified product belongs to administered stores.
		
		list($product, $store) = $this->__administeredStoreProduct($product, $store);
		
		if (empty($store))
		{
			$this->redirect(array('controller' => 'stores', 'action' => 'view', 'admin' => true), null, true);
		}
		else if (empty($product))
		{
			$this->redirect(array('controller' => 'stores', 'action' => 'view', 'admin' => true, 'store' => $store['Store']['hash']), null, true);
		}
		
		
		// Checks if specified photos is valid and belongs to product.
		
		$photo = $this->Product->Photo->find('first',
			array(
				'conditions' => array(
					'Photo.model' => 'ProductPhoto',
					'Photo.foreign_key' => $product['Product']['id'],
					'Photo.hash' => $photo,
					'Photo.is_deleted' => false
				)
			)
		);
		
		if (!empty($photo) && sizeof($product['Product']['Photo']) > 1)
		{
			$this->Product->Photo->delete($photo['Photo']['id']);
		}
		
		
		$this->redirect(array('controller' => 'products', 'action' => 'edit', 'admin' => true, 'store' => $store['Store']['slug'], 'product' => $product['StoreProduct']['slug']), null, true);
	}
}
