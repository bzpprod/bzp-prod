<?php if ($numbers =  $this->Paginator->numbers(array('separator' => ''))) : ?>
	<div class="paginat">
		<span><?php echo __d('view','Layout.pagination.pages'); ?></span>
		<?php echo $numbers; ?>			
        <br class="clear" />        
    </div>
<?php endif; ?>
