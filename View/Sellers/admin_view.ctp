<div class="store_data" style="width:400px;">
	<div class="l">
		<?php
		if (empty($seller['FacebookPage']['id']))
		{
			echo $this->Html->link($this->Html->image('https://graph.facebook.com/' . $seller['User']['FacebookUser']['fb_user_id'] . '/picture?access_token=' . $fb_access_token, array('alt' =>  $seller['Store']['title'])), array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $seller['Store']['slug']), array('escape' => false, 'title' => $seller['Store']['title'], 'target' => '_blank'));
		}
		else
		{
			echo $this->Html->link($this->Html->image('https://graph.facebook.com/' . $seller['FacebookPage']['fb_page_id'] . '/picture?access_token=' . $fb_access_token, array('alt' => $seller['Store']['title'])), $seller['FacebookPage']['link'] . '&app_data=/'. $seller['Store']['slug'], array('escape' => false, 'title' => $seller['Store']['title'], 'target' => '_blank'));
		}
		?>
	</div>
	
	<div class="l">
		<div class="line">
			<h2><?php echo $seller['Store']['title']; ?></h2>
			<?php if (empty($seller['FacebookPage']['id'])) : ?>
				<h3><?php echo $seller['User']['location']; ?></h3>
			<?php endif; ?>
		</div>
		
		<?php if (empty($seller['FacebookPage']['id'])) : ?>
			<div class="line">		
				<b>Email:</b><br/>
				<p><b><?php echo $this->Html->link($seller['User']['email'], 'mailto:' . $seller['User']['email'], array('title' => $seller['User']['email'])); ?></b></p>
			</div>
		<?php endif; ?>
		
		<?php if(!empty($seller['Phone']['number'])){ ?>			
		<div class="line">			
			<b>Telefone:</b><br/>
			<p><b><?php echo $seller['Phone']['number']; ?></b></p>
		</div>			
		<?php } ?>				
		
		<div class="line">
			<?php if (empty($seller['FacebookPage']['id'])) : ?>
				<p><b><?php echo $this->Html->link('Ver outros produtos à venda', Configure::read('Facebook.redirect') . Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $seller['Store']['slug'])), array('escape' => false, 'target' => '_blank', 'class' => 'others_products', 'title' => $seller['Store']['title'])); ?></b></p>
			<?php else : ?>
				<p><b><?php echo $this->Html->link('Ver outros produtos à venda', $seller['FacebookPage']['link'] . '&app_data=/'. $seller['Store']['slug'], array('escape' => false, 'target' => '_blank', 'class' => 'others_products', 'title' => $seller['Store']['title'])); ?></b></p>
			<?php endif; ?>			
		</div>

		<br/>
		
		<?php if (empty($seller['FacebookPage']['id'])) : ?>
			<p><?php echo $this->Html->link($seller['Store']['title'], 'http://facebook.com/'.$seller['User']['FacebookUser']['fb_user_id'], array('target' => 'blank', 'class' => 'btn profile', 'title' => $seller['Store']['title'])); ?></p>
		<?php endif; ?>	
	</div>
	<br class="clear" />
	
</div>