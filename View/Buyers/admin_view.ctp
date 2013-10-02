<div class="store_data" style="width:400px;">
	
	<div class="l">
		<?php echo $this->Html->link($this->Html->image('https://graph.facebook.com/'.$buyer['FacebookUser']['fb_user_id'].'/picture?access_token='.$fb_access_token, 
			array('alt' => $buyer['Buyer']['name'])), 'http://facebook.com/'.$buyer['FacebookUser']['fb_user_id'], 
			array('target' => 'blank', 'escape' => false, 'title' => $buyer['Buyer']['name'])); ?>
	</div>
	
	<div class="l">
		<div class="line">
			<h2><?php echo $buyer['Buyer']['name']; ?></h2>
			<h3><?php echo $buyer['Buyer']['location']; ?></h3>
		</div>

		<div class="line">		
			<b>Email:</b><br/>
			<p><b><?php echo $this->Html->link($buyer['Buyer']['email'], 'mailto:' . $buyer['Buyer']['email'], array('title' => $buyer['Buyer']['email'])); ?></b></p>
		</div>
		
		<br/>
		<p><?php echo $this->Html->link($buyer['Buyer']['name'], 'http://facebook.com/'.$buyer['FacebookUser']['fb_user_id'], 
			array('target' => 'blank', 'class' => 'btn profile', 'title' => $buyer['Buyer']['name'])); ?></p>

	</div>
	<br class="clear" />
	
</div>
