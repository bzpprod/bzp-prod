<?php // if(){
	echo $this->Html->scriptBlock(' 
		$(document).ready(function(){
			$.colorbox.close(); 	
		});	
	'); 
// } ?>

<div class="store_data" style="width:400px;">
	<?php echo $this->Form->create('Delivery'); ?>
		<div class="line">
			<p><?php echo $this->Form->input('Delivery.tracking', array('type' => 'text', 'label' =>  __d('view', 'Transactions.sales.delivery.form.field_delivery_tracking'))); ?></p>
		</div>			

		<p><?php echo $this->Form->submit(__d('view', 'Transactions.sales.delivery.form.submit'), array('class' => 'btnsave', 'div' => false, 'name' => 'confirm')); ?>
	<br class="clear" />
</div>
