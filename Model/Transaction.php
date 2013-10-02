<?php
App::uses('AppModel', 'Model');

class Transaction extends AppModel
{
	public $name = 'Transaction';
	public $useTable = 'transactions';
	public $whitelist = array('product_id', 'store_id', 'buyer_id', 'quantity', 'price', 'delivery', 'is_exchange');
	public $actsAs = array('Containable');
	
	public $virtualFields = array(
		'total_price' => '(Transaction.quantity * Transaction.price) + Transaction.delivery'
	);
	
	public $belongsTo = array(
		'Store' => array(
			'className' => 'Store',
			'foreignKey' => 'store_id'
		),
		
		'StoreProduct' => array(
			'className' => 'StoreProduct',
			'foreignKey' => 'product_id'
		),
		
		'Buyer' => array(
			'className' => 'User',
			'foreignKey' => 'buyer_id'
		)
	);
	
	public $hasOne = array(
		'Delivery' => array(
			'className' => 'TransactionDelivery',
			'foreignKey' => 'foreign_key',
			'conditions' => array('Delivery.model' => 'Transaction', 'Delivery.is_deleted' => false),
			'dependent' => false
		),
		
		'Payment' => array(
			'className' => 'TransactionPayment',
			'foreignKey' => 'foreign_key',
			'conditions' => array('Payment.model' => 'Transaction', 'Payment.is_canceled' => false, 'Payment.is_deleted' => false),
			'dependent' => false
		),
		
		'PurchaseQualification' => array(
			'className' => 'Qualification',
			'foreignKey' => 'transaction_id',
			'conditions' => array('PurchaseQualification.method' => 'purchase'),
			'dependent' => false
		),
		
		'SaleQualification' => array(
			'className' => 'Qualification',
			'foreignKey' => 'transaction_id',
			'conditions' => array('SaleQualification.method' => 'sale'),
			'dependent' => false
		)
	);
}
