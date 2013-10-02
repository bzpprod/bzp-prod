<?php
App::uses('Model', 'Model');
App::uses('Log', 'Model');
App::uses('String', 'Utility');
App::uses('Sanitize', 'Utility');

class AppModel extends Model
{
	public $modelAttribute = '';
	public $slugAttribute = '';
	public $logEnabled = true;


/**
 *
 * Generate HASH and CREATED / UPDATED date & time depending on the type of operation.
 *
 */
	public function beforeSave($options = array())
	{
		if (empty($this->id) && empty($this->data[$this->alias]['id']))
		{
			$this->data[$this->alias]['hash'] = String::uuid();
			$this->data[$this->alias]['created'] = date('Y-m-d H:i:s');
			
			if (!empty($this->modelAttribute))
			{
				$this->data[$this->alias]['model'] = $this->modelAttribute;
			}
			
			if (!empty($this->whitelist))
			{
				array_push($this->whitelist, 'model', 'foreign_key', 'hash', 'created');
			}
		}
		else
		{
			$this->data[$this->alias]['updated'] = date('Y-m-d H:i:s');
			
			if (!empty($this->whitelist))
			{
				array_push($this->whitelist, 'updated');
			}
		}
		
		return parent::beforeSave($options);
	}

/**
 *
 * Create a log entry with provided data.
 * Generate model slug if necessary.
 *
 */
	public function afterSave($created)
	{
		$record = $this->findById($this->id);
		
		if ($this->logEnabled)
		{
			$logModel = new Log();
			$logModel->save(
				array(
					'Log' => array(
						'model'			=> $this->alias,
						'foreign_key'	=> $this->id,
						'action'		=> ($created ? 'add' : 'edit'),
						'serialize'		=> serialize($record)
					)
				)
			);
		}
		
		if (!empty($this->slugAttribute))
		{
			$slug = String::insert($this->slugAttribute, $record[$this->alias]);
			
			$this->updateAll(
				array($this->alias . '.slug' => sprintf('"%s"', $this->__toAscii($slug))),
				array($this->alias . '.id' => $this->id)
			);
		}
		
		parent::afterSave($created);
	}

/**
 *
 * Create a log entry.
 *
 */
	public function afterDelete()
	{
		if ($this->logEnabled)
		{
			$logModel = new Log();
			$logModel->save(
				array(
					'Log' => array(
						'model'			=> $this->alias,
						'foreign_key'	=> $this->id,
						'action'		=> 'delete'
					)
				)
			);
		}
				
		parent::afterDelete();
	}

/**
 *
 * If model has "is_deleted" field, update its value to true, otherwise delete the record
 *
 */
	public function delete($id = null, $cascade = true)
	{
		if ($this->hasField('is_deleted'))
		{
			if (!empty($id)) $this->id = $id;
			if (!$this->exists()) return false;
				
			if ($this->saveField('is_deleted', true))
			{
				$this->getEventManager()->dispatch(new CakeEvent('Model.afterDelete', $this));
				$this->id = false;
				
				return true;
			}
		}
		else
		{
			return parent::delete($id, $cascade);
		}
		
		return false;
	}

/**
 *
 * Translate model validation message.
 *
 */
	public function invalidate($field, $value = true)
	{
		if (!is_array($this->validationErrors))
		{
			$this->validationErrors = array();
		}
		
		$this->validationErrors[$field][] = __d('model', $value);
	}
	
	
	
/**
 *
 * Converts provided string to ascii.
 *
 */
	protected function __toAscii($string, $replace = null, $delimiter = '-')
	{
		if (!empty($replace))
		{
			$string = str_replace((array)$replace, ' ', $string);
		}
		
		// replace non letter or digits by -
		$string = preg_replace('~[^\\pL\d]+~u', $delimiter, $string);

		// trim
		$string = trim($string, '-');

		// transliterate
		$string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);

		// lowercase
		$string = strtolower($string);

		// remove unwanted characters
		$string = preg_replace('~[^-\w]+~', '', $string);
		
		if (empty($string))
		{
			return 'n-a';
		}
		return $string;
	}
	
/**
 *
 * Generate a random string.
 *
 */
	protected function __randomString($length, $charset='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
	{
		$random_string = '';
		$charset_length = strlen($charset);
	
		for ($i = 0; $i < $length; $i++)
		{
			$random_string .= substr($charset, mt_rand(0, $charset_length) - 1, 1);
		}
	
		return $random_string;
	}
}
