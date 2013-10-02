<?php if (!empty($notifications)) : ?>
	<div id="notifications">
		<?php foreach ($notifications as $key => $value) echo $this->element('notifications' . DS . $value['Notification']['template']); ?>
	</div>
<?php else : ?>
	<p>Você não possui nenhuma notificação.</p>
<?php endif; ?>