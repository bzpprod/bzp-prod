<?php $this->set('body_class', 'edit product_edit refund'); 
$this->Html->addCrumb('Minhas Vendas', array('controller' => 'sales', 'action' => 'index', 'admin' => true));
$this->Html->addCrumb('Reembolsar comprador', array('controller' => 'payments_paypal', 'action' => 'refund', 'admin' => 'true','transaction' => $transaction['Transaction']['hash']));
print $this->element('breadcrumb');
// echo print_r($transaction);
?>

<fieldset>
	<h3>Você deseja mesmo reembolsar <?php echo $transaction['Buyer']['name']; ?> na venda de <br/>"<?php echo $transaction['Product']['title']; ?>" ?</h3>
	<div class="divisor"></div>
		<?php echo $this->Form->create('Transaction'); ?>	
		<div class="l">
			<div style="margin:30px 30px 0 90px; height:50px; width:200px;"></div>
		</div>
		<div class="l" style="margin-top:30px;">
			<div class="text-align:center">
				<div style="float:left;margin-right:10px;">
					<?php echo $this->Form->submit('Sim', array('name' => 'confirm')); ?>
				</div>
				<div style="float:left;">				
					<?php echo $this->Html->link('Não', array('controller' => 'sales', 'action' => 'index', 'admin' => true), 
					array('class' => 'btnlink')); ?>
				</div>					
			</div>
		</div>
	<?php echo $this->Form->end(); ?>
</fieldset>
