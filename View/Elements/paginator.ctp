<?php if ($numbers =  $this->Paginator->numbers(array('separator' => ''))) : ?>
	<div id="paginator">
		<span><?php echo __d('view','Layouts.default.pagination.pages'); ?></span>
		<?php echo $numbers; ?>			
        <br class="clear" />        
    </div>
<?php endif; ?>
