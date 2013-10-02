	<br class="clear" />
	<div class="buy-product-personal">
		<h3><?php echo __d('view', 'Products.admin_add.form.personal_data.title'); ?></h3>
		<div class="divisorDouble"></div>
		<ul>
			<li class="l name">
				<?php echo $this->Form->input('Buyer.name', 
				array('type' => 'text', 'value' => $logged_user['User']['name'], 'label' =>  __d('view', 'Products.buy.form.field_name.title'))); ?></li>
			<li class="l email">
				<?php echo $this->Form->input('Buyer.email', array('type' => 'text', 'value' => $logged_user['User']['email'], 'label' =>  __d('view', 'Products.buy.form.field_email.title'))); ?></li>

			<!-- <li class="r"><a href="#" title="Editar Dados"><span></span>Editar Dados</a></li> -->
		</ul>
		<br class="clear" />
	</div>