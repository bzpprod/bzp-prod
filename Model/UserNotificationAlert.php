<?php
App::uses('AppModel', 'Model');

class UserNotificationAlert extends AppModel
{
	public $name	 		= __CLASS__;
	public $useTable		= 'userNotificationAlert';
	public $primaryKey		= array('type','users_id');
	public $logEnabled		= false;

	public $_schema = array(
						'users_id' => array(
										'type' => 'integer',
										'length' => 11
									),
						'type' => array(
										'type' => 'integer',
										'length' => 1
									),
						'total' => array(
									'type' => 'integer',
									'length' => 5
									)
					);
	
	
	static $userId			= null;
	// Not used now
	const TYPE_STORE 		= 1;
	// For every sell it's made
	const TYPE_TRANSACTION	= 2;
	// for every invite that is converted
	const TYPE_INVITATION	= 3;
	// for something you follow
	const TYPE_FEED			= 4;
	
	
	private function _add($type)
	{
		if (self::$userId == null || !is_int(self::$userId)) {
			return false;	
		}
		
		$db = ConnectionManager::getDataSource('default');
		$prefix = $db->config['prefix'];
				 
		// total=total+1 may generate some incosistency in an environment of multiple master database servers, not in on a dayly workflow but when a DBA manualy restore backups.
		$sql = "INSERT INTO $prefix".$this->useTable." (users_id, type,total) VALUES (".self::$userId.",$type,1) ON DUPLICATE KEY UPDATE total=total+1;";
		
		return self::query($sql);
	}
	
	private function _purge($type)
	{
		$db = ConnectionManager::getDataSource('default');
		$prefix = $db->config['prefix'];

		# FIXME: When CakePHP will support multiple collumn primary keys??
		/*
		$result = $this->deleteAll(array(
											$this->name.'.type' 	 => $type,
											$this->name.'.users_id' => self::$userId
										)
								  );
		return $result;
		*/
		$sql = "DELETE FROM $prefix".$this->useTable." WHERE users_id=".self::$userId." AND type=$type;";
		
		return $this->query($sql);
	}
	
	private function _get($type)
	{
		$result = $this->find('first',array(
											'conditions'=>array(
																$this->name.'.type' 	 => $type,
																$this->name.'.users_id' => self::$userId
																)
								 			));
		
		return $result;
	}
	
	public function setUser($userId = null)
	{
		if ($userId === null || !is_numeric($userId) || $userId < 0)
		{
			return false;
		} 
		else
		{
			self::$userId = (int)$userId;
			return true;
		}
		
	}
	
	public function getConstantValue($name = null)
	{
		return constant(sprintf('self::%s', $name));
	}
	
	public function addStore()
	{
		
		return self::_add(self::TYPE_STORE);
	}
	
	public function purgeStore()
	{

		return self::_purge(self::TYPE_STORE);
	}
	
	public function addTransaction()
	{
		
		return self::_add(self::TYPE_TRANSACTION);
	}
	
	public function purgeTransaction()
	{

		return self::_purge(self::TYPE_TRANSACTION);
	}
	
	public function addInvitation()
	{
		
		return self::_add(self::TYPE_INVITATION);
	}
	
	public function purgeInvitation()
	{

		return self::_purge(self::TYPE_INVITATION);
	}
	
	public function addFeed()
	{
		
		return self::_add(self::TYPE_FEED);
	}
	
	public function purgeFeed()
	{

		return self::_purge(self::TYPE_FEED);
	}

	public function getStore()
	{
		$result = self::_get(self::TYPE_STORE);
		$result = Set::extract('/UserNotificationAlert/total', $result);
		if (is_array($result) && !empty($result))
		{
			return (int)$result[0];
		}
		else
		{
			return 0;
		}
	}
	
	public function getTransaction()
	{
		$result = self::_get(self::TYPE_TRANSACTION);
		$result = Set::extract('/UserNotificationAlert/total', $result);
		if (is_array($result) && !empty($result))
		{
			return (int)$result[0];
		}
		else
		{
			return 0;
		}
	}
	
	public function getInvitation()
	{
		$result = self::_get(self::TYPE_INVITATION);
		$result = Set::extract('/UserNotificationAlert/total', $result);
		if (is_array($result) && !empty($result))
		{
			return (int)$result[0];
		}
		else
		{
			return 0;
		}
	}
	
	public function getFeed()
	{
		$result = self::_get(self::TYPE_FEED);
		$result = Set::extract('/UserNotificationAlert/total', $result);
		if (is_array($result) && !empty($result))
		{
			return (int)$result[0];
		}
		else
		{
			return 0;
		}
	}
	
}