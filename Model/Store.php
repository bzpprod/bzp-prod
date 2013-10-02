<?php
App::uses('AppModel', 'Model');

class Store extends AppModel
{
	public $name = 'Store';
	public $useTable = 'stores';
	public $whitelist = array('user_id', 'title', 'description', 'is_personal', 'is_enabled');
	public $actsAs = array('Containable');
	public $slugAttribute = ':id-:title';
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		)
	);
	
	public $hasOne = array(
		'Address' => array(
			'className' => 'StoreAddress',
			'foreignKey' => 'foreign_key',
			'conditions' => array('Address.model' => 'Store', 'Address.is_deleted' => false),
			'dependent' => false
		),
		
		'Phone' => array(
			'className' => 'StorePhone',
			'foreignKey' => 'foreign_key',
			'conditions' => array('Phone.model' => 'Store', 'Phone.is_deleted' => false),
			'dependent' => false
		),
		
		'BankAccount' => array(
			'className' => 'StoreBankAccount',
			'foreignKey' => 'foreign_key',
			'conditions' => array('BankAccount.model' => 'Store', 'BankAccount.is_deleted' => false),
			'dependent' => false
		),
		
		'PaypalAccount' => array(
			'className' => 'StorePaypalAccount',
			'foreignKey' => 'foreign_key',
			'conditions' => array('PaypalAccount.model' => 'Store', 'PaypalAccount.is_deleted' => false),
			'dependent' => false
		),
		
		'Banner' => array(
			'className' => 'StoreBanner',
			'foreignKey' => 'foreign_key',
			'conditions' => array('Banner.model' => 'StoreBanner', 'Banner.is_deleted' => false),
			'dependent' => false
		),
		
		'FacebookPage' => array(
			'className' => 'StoreFacebookPage',
			'foreignKey' => 'store_id',
			'conditions' => array('FacebookPage.is_deleted' => false),
			'dependent' => false
		),
	);
		
	public $validate = array(
		'title' => array(
			'rule' => array('minLength', 5),
			'message' => 'Store.validate.title',
			'allowEmpty' => false,
			'required' => true
		)
	);
	
	public function findHomeList($filter = null, $offset = 0, $limit = 15)
	{

		if ($filter == null)
		{
			$sql = 'SELECT S.id AS id FROM bazzapp_stores AS S, bazzapp_stores_products SP, bazzapp_products P WHERE S.id=SP.store_id AND SP.product_id=P.id AND P.is_deleted=0 AND S.is_deleted=0 AND S.is_enabled=1 GROUP BY S.id ORDER BY SP.created DESC';
		}
		else
		{
			$sqlDefault = 'SELECT S.id AS id FROM bazzapp_stores AS S, bazzapp_stores_products SP, bazzapp_products P WHERE S.id=SP.store_id AND SP.product_id=P.id AND P.is_deleted=0 AND  S.is_deleted=0 AND S.is_enabled=1 GROUP BY S.id ORDER BY ';
			
			if (strpos($filter, ':') > 0)
			{
				list($filter, $userId) = explode(':',$filter);
			}
			switch ($filter)
			{
				case 'friends-like':
				{
					$sql = "SELECT S.id FROM bazzapp_likes L, bazzapp_stores S, bazzapp_stores_products SP, bazzapp_products WHERE S.id=SP.store_id AND SP.product_id=P.id AND P.is_deleted=0 AND  L.model='Store' AND L.user_id=%s AND L.foreign_key=S.id AND S.is_enabled=1 AND S.is_deleted=0  GROUP BY S.id";
					$sql = sprintf($sql, $userId);

				} break;
				
				case 'friends-products':
				{
					$sql = "SELECT S.id FROM bazzapp_stores S, bazzapp_stores_products SP, bazzapp_products P WHERE S.id=SP.store_id AND SP.product_id=P.id AND P.is_deleted=0 AND  S.is_personal=1 AND S.is_enabled=1 AND S.is_deleted=0 AND S.user_id IN (SELECT friend_facebook_user_id FROM bazzapp_friends WHERE facebook_user_id=%s)  GROUP BY S.id";
					$sql = sprintf($sql, $userId);
					
				} break;
				
				case 'follow-stores':
				{
					$sql = "SELECT S.id FROM bazzapp_follows F, bazzapp_stores S, bazzapp_stores_products SP, bazzapp_products P WHERE S.id=SP.store_id AND SP.product_id=P.id AND P.is_deleted=0 AND  F.model='Store' AND F.user_id=%s AND F.foreign_key=S.id AND S.is_enabled=1 AND S.is_deleted=0  GROUP BY S.id ";
					$sql = sprintf($sql, $userId);
				} break;

				/*
				case 'cheaper':
				{
						$sql = $sqlDefault . 'S.price ASC';
							
				} break;
					
				case 'expensive':
				{
						$sql = $sqlDefault . 'S.price DESC';
							
				} break;
				case 'likes':
				{
						$sql = $sqlDefault . 'L.likes DESC';
							
				} break;
				*/
					
				default:
				{
						$sql = $sqlDefault . ' SP.created DESC';
				}
			}
			
		}
		$sql.= ' LIMIT ' . $offset . ', ' . $limit;
		
		$result = $this->query($sql);
		return Set::extract('/S/id', $result);
		
	}
	
	public function getTotalFollowers($storeId = null)
	{
		$sql = "SELECT count(*) total FROM bazzapp_follows F WHERE model='Store' AND foreign_key='$storeId'";
		$result = Set::extract('/0/total', $this->query($sql));
		
		return (int) $result[0];
		
	}
	
}
