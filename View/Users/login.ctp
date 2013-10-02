<style>
	html, body, #global, #global .center, #content { height:99%; }
</style>
<table width=100% height=100%>
	<td valign=middle align=center>
				<div>
					<a href="<?php echo $fb_login_url?>" rel="nofollow"><?php echo $this->Html->image('logo.jpg', array('alt' => __d('view','Layouts.fb_default.bazzapp'), 'border'=>0)); ?></a>
				</div>					

				<p><?php echo __d('view','Layout.Redirect.Text'); ?></p>
				<?php echo $this->Html->scriptBlock('top.location.href = "' . $fb_login_url . '";'); ?>	
	</td>
</table>
