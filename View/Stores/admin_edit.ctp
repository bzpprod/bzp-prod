<?php 
$this->set('body_class', 'edit store_edit');
$this->set('body_page', 'store_edit');
$this->Html->addCrumb(__d('view', 'Layouts.fb_default.stores'), array('controller' => 'stores', 'action' => 'view'));
$this->Html->addCrumb(__d('view', 'Stores.fb_admin_view.edit_store').' "'.$this->request->data['Store']['title'].'"', 
	array('controller' => 'stores', 'action' => 'edit', 'admin' => true));
?>
<fieldset>
		<br/>        
		<?php echo $this->Form->create('Store', array('type' => 'file')); ?>
		<?php echo $this->Form->hidden('Phone.id'); ?>	
		<?php echo $this->Form->hidden('PaypalAccount.id'); ?>		
		<ul>
			<?php if ($store['Store']['is_personal'] == 0):?>
			<li><?php echo $this->Form->input('Banner.filename', array('type' => 'file', 'label' => "Banner Principal")); ?></li>
			<?php endif; ?>		
			<li><?php echo $this->Form->input('Store.title', 
				array('type' => 'text', 'label' => __d('view', 'Stores.admin_add.form.field_title.title')
				)); ?></li>

			<li class="l"><?php echo $this->Form->input('Phone.number', 
				array('type' => 'text', 'class' => 'masktTel', 'label' => __d('view', 'Stores.admin_add.form.field_tel.title')
				)); ?></li>
			<li class="l"><?php echo $this->Form->input('PaypalAccount.email', 
				array('type' => 'text', 'label' => __d('view', 'Stores.admin_add.form.paypalmail.title')
				)); ?>
				<div class="linkpaypal">
					<div class="iconpp"></div>
					<?php echo $this->Html->link(__d('view', 'Stores.admin_add.paypalregister.title'), 'https://www.paypal.com/br/cgi-bin/webscr?cmd=_registration-run', 
					array('target' => '_blank', 'title' => __d('view', 'Stores.admin_add.paypalregister.title').'?')); ?>	
				</div>				
			</li>
			<li class="clear"></li>
			<li><?php echo $this->Form->input('Store.description', 
				array('type' => 'textarea', 'label' =>  __d('view', 'Stores.fb_admin_edit.form.field_description.title')
				)); ?></li>
			<li class="clear"></li>			
		</ul>

	<br class="clear" />
	<div class="divisor"></div>		
	<h3><?php echo __d('view', 'Stores.admin_add.form.field_bankdata.title'); ?></h3>
		<?php echo $this->Form->hidden('BankAccount.id'); ?>
		<ul>
			<li><?php echo $this->Form->input('BankAccount.name', 
				array('type' => 'text', 'label' =>  __d('view', 'Stores.admin_add.form.field_bank_benefic.title')
				)); ?></li>
			<li class="l"><?php echo $this->Form->input('BankAccount.document', 
				array('type' => 'text', 'label' =>  __d('view', 'Stores.admin_add.form.field_cpf.title')
				)); ?></li>
			<li class="clear"></li>
			<li><?php echo $this->Form->input('BankAccount.bank', 
				array('type' => 'text', 'label' =>  __d('view', 'Stores.admin_add.form.field_bank.title')
				)); ?></li>
			<li class="l"><?php echo $this->Form->input('BankAccount.agency', 
				array('type' => 'text', 'label' =>  __d('view', 'Stores.admin_add.form.field_ag.title')
				)); ?></li>
			<li class="l"><?php echo $this->Form->input('BankAccount.account', 
				array('type' => 'text', 'label' =>  __d('view', 'Stores.admin_add.form.field_cc.title')
				)); ?></li>
			<li class="clear"></li>			
		</ul>
	
	<br class="clear" />
	<div class="divisor"></div>		
	<h3><?php echo __d('view', 'Stores.admin_add.form.field_address.title'); ?></h3>
		<?php echo $this->Form->hidden('Address.id'); ?>	
		<ul>
			<li><?php echo $this->Form->input('Address.zipcode', 
				array('type' => 'text', 'class' => 'maskCep', 'label' =>  __d('view', 'Stores.admin_add.form.field_cep.title')
				)); ?></li>			
			<li class="l"><?php echo $this->Form->input('Address.address', 
				array('type' => 'text', 'style' => 'width: 300px;', 'label' =>  __d('view', 'Stores.admin_add.form.field_address.title')
				)); ?></li>
			<li class="clear"></li>
			<li><?php echo $this->Form->input('Address.district', 
				array('type' => 'text', 'label' =>  __d('view', 'Stores.admin_add.form.field_district.title')
				)); ?></li>
			<li class="l"><?php echo $this->Form->input('Address.city', 
				array('type' => 'text', 'label' =>  __d('view', 'Stores.admin_add.form.field_city.title')
				)); ?></li>
			<li class="l"><?php echo $this->Form->input('Address.state', 
				array('type' => 'text', 'style' => 'width: 166px;', 'label' =>  __d('view', 'Stores.admin_add.form.field_estate.title')
				)); ?></li>			
			<li class="clear"></li>			
		</ul>

	<ul>	
		<li class="clear"></li>
		<li class="clear"></li>        
		<li><?php echo $this->Form->submit(__d('view', 'Stores.fb_admin_edit.form.submit'), array('class'=> 'btn btn3')); ?></li>
		<li class="clear"></li>			
	</ul>	
		
	<?php echo $this->Form->end(); ?>
</fieldset>