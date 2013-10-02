<table width="550">
	<tr>
		<td colspan="3"><?php echo $friend['User']['name']; ?>,<br /><br /></td>
	</tr>	
	<tr>
		<td width="50" valign="top"><?php echo $this->Html->link($this->Html->image('https://graph.facebook.com/' . $user['FacebookUser']['fb_user_id'] . '/picture', array('alt' =>  $user['User']['name'], 'border' => '0', 'width' => '50', 'height' => '50')), Configure::read('Facebook.redirect') . Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $store['Store']['slug'])), array('escape' => false, 'target' => '_blank', 'title' => $user['User']['name'])); ?></td>
		<td width="10"></td>
		<td>
			Seu amigo(a) <?php echo $user['User']['name']; ?> começou a participar do BazzApp também!<br />
			<?php echo $this->Html->link('Acesse o perfil dele(a)', Configure::read('Facebook.redirect') . Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $store['Store']['slug'])), array('escape' => false, 'title' => $store['Store']['title'])); ?> e veja já o que ele(a) está vendendo!<br /><br />
			<b>Equipe BazzApp</b><br /><br /><br />
		</td>
	</tr>
	<tr>
		<td align="center" colspan="3"><font size="-4">Este é um email automático não devendo ser respondido.</font></td>
	</tr>
</table>