<?php
App::uses('Photo', 'Model');

class ProductPhoto extends Photo
{
	public $name = 'ProductPhoto';
	public $modelAttribute = 'ProductPhoto';
	
	public $actsAs = array(
		'Containable',
		'MeioUpload.MeioUpload' => array(
			'filename' => array(
				'dir' => 'files{DS}product{DS}photo',
				'uploadName' => 'hash',
				'allowedMime' => array('image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'),
				'allowedExt' => array('.jpg', '.jpeg', '.png', '.gif'),
				'fixedAspectRatio' => 'C',
				'thumbnailQuality' => 100,
				'thumbsizes' => array(
					'small'  => array('width' => 77, 'height' => 77),
					'medium' => array('width' => 100, 'height' => 100),
					'large'  => array('width' => 175, 'height' => 175),
					'extralarge'  => array('width' => 425, 'height' => 425)
				)
			)
		)
	);
	
	public $belongsTo = array(
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'foreign_key'
		)
	);
}
