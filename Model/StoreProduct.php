<?php
App::uses('AppModel', 'Model');

class StoreProduct extends AppModel
{
	public $name = 'StoreProduct';
	public $useTable = 'stores_products';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'Store' => array(
			'className' => 'Store',
			'foreignKey' => 'store_id'
		),
		
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id'
		)
	);
	
	
	
	public function afterSave($created)
	{
		$record = $this->findById($this->id);
		
		
		$slug = String::insert(':id-:title', array('id' => $record[$this->alias]['id'], 'title' =>$record['Product']['title']));
		
		$this->updateAll(
			array($this->alias . '.slug' => sprintf('"%s"', $this->__toAscii($slug))),
			array($this->alias . '.id' => $this->id)
		);		
		
		
		parent::afterSave($created);
	}
	
	
	public function getTotalFollowers($storeId = null)
	{
		$sql = "SELECT count(*) total FROM bazzapp_follows F WHERE model='Store' AND foreign_key='$storeId'";
		$result = Set::extract('/0/total', $this->query($sql));
		
		return $result[0];
		
	}

	public function findList($filter = null, $offset = 0, $limit = 15, $category = null, $search = null)
	{
		
		if ($search !== null)
		{
			$searchWhere = " AND (P.title LIKE '%$search%' OR P.description LIKE '%$search%' OR S.title LIKE '%$search%') ";
		}
		else
		{
			$searchWhere = null;
		}
		
		if ($category !== null)
		{
			$categoryIds = '';
			if (empty($category['ChildCategory'][0]))
			{
				$categoryIds.= $category['Category']['id'] . ',';
			}
			else
			{
				foreach ($category['ChildCategory'] as $child)
				{
					$categoryIds.= $child['id'] . ',';
				}
				
			}
			$categoryIds = substr($categoryIds, 0, -1);
			
			$categoryWhere = " AND P.category_id IN ($categoryIds) ";
		}
		else
		{
			$categoryWhere = null;
		}
		
		if ($filter == null)
		{
			$sql = "SELECT SP.id AS id FROM bazzapp_stores_products AS SP LEFT JOIN bazzapp_products AS P ON (SP.product_id=P.id) ".($searchWhere != null ? ', bazzapp_stores S ' : '')."  WHERE ".($searchWhere != null ? ' SP.store_id=S.id AND' : '')." SP.is_deleted=false AND (P.quantity - P.quantity_sold) > 0 AND P.is_deleted=false $searchWhere $categoryWhere ORDER BY P.created DESC";
		}
		else
		{
			if (strpos($filter, 'friends-like:') === 0)
			{
				$userId = explode(':',$filter);
				$userId = $userId[1];
				$sql = "SELECT SP.id AS id FROM bazzapp_view_friends_liked_products AS SP LEFT JOIN bazzapp_products AS P ON (SP.product_id=P.id) ".($searchWhere != null ? ', bazzapp_stores S ' : '')." WHERE ".($searchWhere != null ? ' SP.store_id=S.id AND' : '')." SP.is_deleted=false AND SP.friend_facebook_user_id=$userId AND (P.quantity - P.quantity_sold) > 0 AND P.is_deleted=false  $searchWhere $categoryWhere  ORDER BY P.created DESC";

			}
			else if (strpos($filter,'friends-products:') === 0)
			{
				$userId = explode(':',$filter);
				$userId = $userId[1];
				$sql = "SELECT SP.id AS id FROM bazzapp_view_friends_products AS SP LEFT JOIN bazzapp_products AS P ON (SP.product_id=P.id) ".($searchWhere != null ? ', bazzapp_stores S ' : '')." WHERE ".($searchWhere != null ? ' SP.store_id=S.id AND' : '')." SP.is_deleted=false AND SP.friend_facebook_user_id=$userId AND (P.quantity - P.quantity_sold) > 0 AND P.is_deleted=false $searchWhere $categoryWhere  ORDER BY P.created DESC";

			}
			else if (strpos($filter,'follow-stores:') === 0)
			{
				$userId = explode(':',$filter);
				$userId = $userId[1];
				$sql = "SELECT SP.id AS id FROM bazzapp_view_followed_stores_products AS SP LEFT JOIN bazzapp_products AS P ON (SP.product_id=P.id) ".($searchWhere != null ? ', bazzapp_stores S ' : '')." WHERE ".($searchWhere != null ? ' SP.store_id=S.id AND' : '')." SP.is_deleted=false AND SP.follow_user_id=$userId AND (P.quantity - P.quantity_sold) > 0 AND P.is_deleted=false $searchWhere $categoryWhere  ORDER BY P.created DESC";

			}
			else
			{
				switch ($filter)
				{
					default:
					{
						$order = 'P.created DESC';	
					} break;
					
					case 'cheaper':
					{
						$order = 'P.price ASC';	
					} break;
					
					case 'expensive':
					{
						$order = 'P.price DESC';	
					} break;
					
					case 'likes':
					{
						$order = 'P.likes DESC';	
					} break;
				}
				
				$sql = "SELECT SP.id AS id FROM bazzapp_stores_products AS SP LEFT JOIN bazzapp_products AS P ON (SP.product_id=P.id) ".($searchWhere != null ? ', bazzapp_stores S ' : '')." WHERE ".($searchWhere != null ? ' SP.store_id=S.id AND' : '')." SP.is_deleted=false AND (P.quantity - P.quantity_sold) > 0 AND P.is_deleted=false  $searchWhere $categoryWhere  ORDER BY " . $order;
			}
			
		}
		$sql.= ' LIMIT ' . $offset . ', ' . $limit;

		$result = $this->query($sql);
		return Set::extract('/SP/id', $result);
	}
}
