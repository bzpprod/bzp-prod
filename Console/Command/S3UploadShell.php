<?php
App::import('Vendor', 'Amazon', array('file' => 'AWSSDKforPHP/sdk.class.php'));


class S3UploadShell extends AppShell
{
	public $uses = array('File');
	public $executionTime = 60;
	
	
	public function main()
	{
		set_time_limit($this->executionTime);
		
		
		$s3 = new AmazonS3(array('key' => Configure::read('Amazon.S3.accessKey'), 'secret' => Configure::read('Amazon.S3.secretKey')));
		$timestamp_begin = strtotime('now');
		
		
		while (strtotime('now') - $timestamp_begin <= $this->executionTime)
		{
			$file = $this->File->find('first',
				array(
					'conditions' => array(
						'File.is_external' => false
					)
				)
			);
			
			if (!empty($file))
			{
				if (!class_exists($file['File']['model']))
				{
					App::uses($file['File']['model'], 'Model');
				}
				
				$model = new $file['File']['model']();
				
				
				// Uploads the original file.
				
				if (file_exists(WWW_ROOT . '/' . $file['File']['dir'] . '/' . $file['File']['filename']))
				{
					$response = $s3->create_object('bazzapp', $file['File']['dir'] . '/' . $file['File']['filename'],
						array(
							'fileUpload' => WWW_ROOT . '/' . $file['File']['dir'] . '/' . $file['File']['filename'],
							'acl' => AmazonS3::ACL_PUBLIC
						)
					);
				}
				
				// Uploads the generated thumbs.
				
				if (!empty($model->actsAs['MeioUpload.MeioUpload']['filename']['thumbsizes']))
				{
					foreach ($model->actsAs['MeioUpload.MeioUpload']['filename']['thumbsizes'] as $key => $value)
					{
						if (file_exists(WWW_ROOT . '/' . $file['File']['dir'] . '/thumb/' . $key . '/' . $file['File']['filename']))
						{
							$response = $s3->create_object('bazzapp', $file['File']['dir'] . '/thumb/' . $key . '/' . $file['File']['filename'],
								array(
									'fileUpload' => WWW_ROOT . '/' . $file['File']['dir'] . '/thumb/' . $key . '/' . $file['File']['filename'],
									'acl' => AmazonS3::ACL_PUBLIC
								)
							);
						}
					}
				}
				
				
				// Updates the file record.
				
				$this->File->id = $file['File']['id'];
				$this->File->saveField('is_external', true);
			}
			
			
			sleep(1);
		}
	}
}