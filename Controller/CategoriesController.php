<?php
App::uses('AppController', 'Controller');


class CategoriesController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'Categories';
	
/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('Category');
	
/**	
 * Controller actions for which authorization is not required.
 *
 * @var array
 */
	public $allowedActions = array('api_index');
	
	
	
/**
 * Lists the categories according to applied filters.
 *
 * @return array
 */
	public function api_index($store = null)
	{
		// Checks if the store exists.
		
		$store = $this->__store($store);
		
		
		// Checks if the category was specified.
		
		if (!empty($this->request->params['named']['category']))
		{
			$category = $this->request->params['named']['category'];
		}
		else if (!empty($this->request->query['category']))
		{
			$category = $this->request->query['category'];
		}
		
		
		// Checks if the search term was specified.
		
		if (!empty($this->request->params['named']['search']))
		{
			$search = $this->request->params['named']['search'];
		}
		else if (!empty($this->request->query['search']))
		{
			$search = $this->request->query['search'];
		}
		
		
		// Checks if is to display just direct children.
		
		if (!empty($this->request->params['named']['direct_children']))
		{
			$directChildren = (bool)$this->request->params['named']['direct_children'];
		}
		else if (!empty($this->request->query['direct_children']))
		{
			$directChildren = (bool)$this->request->query['direct_children'];
		}
		else
		{
			$directChildren = false;
		}
		
		
		// Checks if is to display just categories that has products.
		
		if (!empty($this->request->params['named']['has_products']) || !empty($this->request->query['has_products']))
		{
			$hasProducts = true;
		}
		else
		{
			$hasProducts = false;
		}
		
		// Default categories find conditions.
		
		$categoriesConditions = array('Category.is_deleted' => false);
		
		
		// Category find conditions.
		
		if (!empty($category))
		{
			// Checks if the specified category exists.
			
			if (is_numeric($category))
			{
				$category = $this->Category->find('first',
					array(
						'conditions' => array(
							'Category.id' => $category,
							'Category.is_deleted' => false
						)
					)
				);
			}
			else
			{
				$category = $this->Category->find('first',
					array(
						'conditions' => array(
							'OR' => array(
								'Category.hash' => $category,
								'Category.slug LIKE' => $category
							),
							'Category.is_deleted' => false
						)
					)
				);
			}
			
			if (!empty($category))
			{
				// Checks if found category has children.
				
				if (empty($category['ChildCategory']))
				{
					// If it has no children, return just categories starting at the same level from found category.
					
					if ($directChildren)
					{
						// If were asked just for direct children, filters categories belonging to parent from found category.
						
						$categoriesConditions['Category.parent_id'] = $category['Category']['parent_id'];
					}
					else
					{
						// Otherwise, filters categories that are within the limit of the fields 'lft' and 'rght'.
						
						if (!empty($category['ParentCategory']))
						{
							$categoriesConditions['Category.lft'] = $category['ParentCategory']['lft'];
							$categoriesConditions['Category.rght'] = $category['ParentCategory']['rght'];	
						}
						else
						{
							$categoriesConditions['Category.lft'] = $category['Category']['lft'];
							$categoriesConditions['Category.rght'] = $category['Category']['rght'];
						}
					}
				}
				else
				{
					// If it has children, return just children from found category.
					
					if ($directChildren)
					{
						// If were asked just for direct children, filters categories belonging to found category.
						
						$categoriesConditions['Category.parent_id'] = $category['Category']['id'];
					}
					else
					{
						// Otherwise, filters categories that are within the limit of the fields 'lft' and 'rght'.
						
						$categoriesConditions['Category.lft'] = $category['Category']['lft'];
						$categoriesConditions['Category.rght'] = $category['Category']['rght'];
					}
				}
			}
		}
		else if ($directChildren)
		{
			$categoriesConditions['Category.parent_id'] = 0;
		}
		
		
		// Filter just categories that has products.
		
		if ($hasProducts)
		{
			if (empty($store) && empty($seach))
			{
				$sql = 'SELECT Category.id, Category.parent_id FROM bazzapp_products P, bazzapp_categories Category WHERE (Category.id = P.category_id) AND  Category.is_deleted=0 AND P.is_deleted=0 AND (P.quantity - P.quantity_sold) > 0 GROUP BY Category.id';
			}
			else if (empty($seach))
			{
				$sql = 'SELECT Category.id, Category.parent_id FROM bazzapp_products P, bazzapp_categories Category, bazzapp_stores_products SP WHERE (Category.id = P.category_id) AND SP.id=' . $store['Store']['id'] . ' AND SP.product_id=P.id AND  Category.is_deleted=0 AND P.is_deleted=0 AND (P.quantity - P.quantity_sold) > 0  GROUP BY Category.id';
			}
			else if (empty($store))
			{
				$sql = 'SELECT Category.id, Category.parent_id FROM bazzapp_products P, bazzapp_categories Category, bazzapp_stores S WHERE (Category.id = P.category_id) AND  Category.is_deleted=0 AND P.is_deleted=0 AND (P.quantity - P.quantity_sold) > 0 P.id=SP.product_id AND S.id=SP.store_id AND (P.title LIKE "%' . Sanitize::clean($search) . '%" OR P.description LIKE "%' . Sanitize::clean($search) . '%" OR S.title LIKE "%' . Sanitize::clean($search) . '%") GROUP BY Category.id';
			}
			else
			{
				$sql = 'SELECT Category.id, Category.parent_id FROM bazzapp_products P, bazzapp_categories Category, bazzapp_stores_products SP, bazzapp_stores S WHERE (Category.id = P.category_id) AND SP.id=' . $store['Store']['id'] . ' AND SP.product_id=P.id AND  Category.is_deleted=0 AND P.is_deleted=0 AND (P.quantity - P.quantity_sold) > 0  P.id=SP.product_id AND S.id=SP.store_id AND (P.title LIKE "%' . Sanitize::clean($search) . '%" OR P.description LIKE "%' . Sanitize::clean($search) . '%" OR S.title LIKE "%' . Sanitize::clean($search) . '%") GROUP BY Category.id';
			}
			
			$categories = $this->Category->query($sql);
			$categoriesId = Set::extract('/Category/id', $categories);
			
			$parentIds = array_unique(Set::extract('/Category/parent_id', $categories));
			$categoriesId = array_merge($categoriesId, $parentIds);
			
			$categoriesConditions['Category.id'] = $categoriesId;
		}
			
		// Find categories applying conditions.
		
		$categories = $this->Category->find('all',
			array(
				'conditions' => $categoriesConditions,
				'order' => array('Category.lft' => 'asc', 'Category.title' => 'asc'),
				'group' => array('Category.id')
			)
		);

	
		// Removing categories that has no products
		for ($i=0, $t=count($categories); $i<$t; $i++)
		{
			if (isset($categoriesConditions['Category.id']) && in_array($categories[$i]['Category']['id'], $categoriesConditions['Category.id']) === false)
			{
				unset($categories[$i]);
			}
			else
			{
				for ($j=0, $t2=count($categories[$i]['ChildCategory']); $j<$t2; $j++)
				{
					if (isset($categoriesConditions['Category.id']) && in_array($categories[$i]['ChildCategory'][$j]['id'], $categoriesConditions['Category.id']) === false)
					{
						unset($categories[$i]['ChildCategory'][$j]);
					}					
				}
			}
			
		}
		$this->set(compact('categories'));
		$this->set('_serialize', array('categories'));
		
		return $categories;
	}
}
