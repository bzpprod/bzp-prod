<?php
App::uses('CakeNumber', 'Utility');
App::uses('String', 'Utility');


class CancelTransactionShell extends AppShell
{
	public $uses = array('Transaction', 'Product');
	
	
	public function main()
	{
		CakeNumber::addFormat('BRR', array('before' => 'R$', 'thousands' => '', 'decimals' => ','));
		
		
		$transactionsToCancel = $this->Transaction->find('all',
				array(
					'conditions' => array(
						'Transaction.created <=' => 'SUBDATE(NOW(), INTERVAL 1 DAY)',
						'Transaction.created >' => 'SUBDATE(NOW(), INTERVAL 2 DAY)',
						'Transaction.status' => 'awaiting payment'
					)
				)
			);
		
		
		foreach ($transactionsToCancel as $t) {
			
			$productqty = $t['Transaction']['quantity'];
			$transactionId = $t['Transaction']['id'];
			$productId = $t['StoreProduct']['product_id'];
			
			
					
			$product = $this->Product->read(null, $productId);

			$qty = $product['Product']['quantity_sold'] - $productqty;
			$sql = "UPDATE bazzapp_products SET quantity_sold=$qty WHERE id=" . $productId;
			$this->Product->query($sql);

			$transaction = $this->Transaction->read(null, $transactionId);
			$sql = "UPDATE bazzapp_transactions SET status='canceled', is_canceled=1, finished=NOW() WHERE id=" . $transactionId;
			$this->Transaction->query($sql);					
			
		}
		exit;
	
	}
}