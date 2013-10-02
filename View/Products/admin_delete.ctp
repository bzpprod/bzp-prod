<?php $this->set('body_class', 'edit static product_edit'); 

$this->Html->addCrumb(__d('view', 'Layouts.fb_default.stores'), array('controller' => 'stores', 'action' => 'view'));
$this->Html->addCrumb(__d('view', 'Layouts.fb_default.stores.delete.Title'), 
	array('controller' => 'products', 'action' => 'delete', 'admin' => 'true', 'product' => $product['Product']['hash']));

print $this->element('breadcrumb');
?>

<fieldset>
	<h3 class="confirm_question" style="font-size:24px !important; margin-top:10px;">Você deseja excluir "<?php echo $product['Product']['title']; ?>" ?</h3>
		<?php echo $this->Form->create('Product'); ?>	
		<div class="l" style="margin-top:30px;">
			<div class="text-align:center">
		
				<div style="float:left;margin-right:10px;">
					<?php echo $this->Form->submit('Sim', array('name' => 'confirm', 'class' => 'btn btn1', 'rel' => 'nofollow')); ?>
				</div>
				<div style="float:left;">				
					<?php echo $this->Html->link('Não', array('controller' => 'stores', 'action' => 'view'), 
					array('class' => 'btn btn3', 'style' => 'height: 17px; padding: 4px 10px;', 'rel' => 'nofollow')); ?>
				</div>					
		</div>
	</div>        
</fieldset>

<?php echo $this->Form->end(); ?>	