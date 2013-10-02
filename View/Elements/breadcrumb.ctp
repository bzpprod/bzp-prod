<div id="breadcrumb">
	<div>
		<?php echo $this->Html->getCrumbs(' > ','Home'); ?>	
	</div>		

	<?php if (!empty($body_class)):
			 if($body_class=="search"): ?>
					<div id="showmode">
						<ul>
							<li><?php echo __d('view', 'Layouts.fb_default.exibition'); ?></li>
							<li class="sm1"><?php echo $this->Html->link(__d('view', 'Layouts.fb_default.exibition_list'), '#'); ?></a></li>				
							<li class="sm2 selected"><?php echo $this->Html->link(__d('view', 'Layouts.fb_default.exibition_box'), '#'); ?></a></li>
						</ul>    
					</div>
		<?php 
			endif; 
		endif; ?> 
	
	<br class="clear" />
</div>
<br class="clear" />