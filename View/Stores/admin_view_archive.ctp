<?php
	$this->set('body_class', 'mystore');
	$this->set('body_page', 'mystore_archive');
	$this->Html->addCrumb(__d('view', 'Layouts.fb_default.stores'), array('controller' => 'stores', 'action' => 'view'));
	$this->Html->addCrumb(__d('view', 'Stores.fb_admin_view.products_off'), array('controller' => 'stores', 'action' => 'sold_out'));
?>
<br />
<div id="store-view-mode">
				<?php echo __d('view', 'Stores.fb_admin_view.view.title')?><br />

                <div class='buttonWrapper left' style='margin-right:5px;'>
    	    		<a class="spriteButton type3 button" href="<?php echo Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => true, 'store' => $store['Store']['slug']))?>" rel="nofollow">
    	    			<span><?php echo __d('view', 'Stores.fb_admin_view.products_on')?></span>
    	    		</a>
	        	</div>
                <div class='buttonWrapper left'>
    	    		<a class="spriteButton type3 button selected" rel="nofollow">
    	    			<span><?php echo __d('view', 'Stores.fb_admin_view.products_off')?></span>
    	    		</a>
	        	</div>	        		
</div>


<div id="store-filter">
	<?php echo $this->element('paginator'); ?>
</div>
<div class="clear"></div>

<div id="product-list">
	<?php foreach ($products as $key => $value){ print $this->element('products' . DS . 'view', array('product' => $value, 'edit' => true)); } ?>  
</div>
<div class="clear"></div>
<?php echo $this->element('paginator'); ?>