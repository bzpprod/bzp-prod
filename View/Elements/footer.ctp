<!-- # FOOTER -->
<div id="footer">
	
	<ul id="rs">
		<li class="rs rs1"><a href="http://www.facebook.com/BazzApp" title="Fan page" target="_blank">Fan page</a></li>
		<li class="rs rs2"><a href="https://twitter.com/BazzApp" title="Twitter" target="_blank">Twitter</a></li>
		<li class="like">
			<?php echo $this->Facebook->like(array('href' => 'http://www.facebook.com/BazzApp', 
			'send' => false, 'show_faces' => false, 'layout' => 'button_count', 'width' => 10)); ?>		
		</a>
	</ul>
	
	<ul class="links">
		<li><?php echo $this->Html->link(__d('view', 'Layouts.footer.how_work.title'), 
				array('controller' => 'pages', 'action' => 'display', 'how_works', 'admin' => false), array('title' => __d('view', 'Layouts.footer.how_work.title'))); ?>
		</li>

		<li><?php echo $this->Html->link('Termos de Uso', 
				array('controller' => 'pages', 'action' => 'display', 'terms_of_use', 'admin' => false), array('title' => 'Termos de Uso')); ?></li>

		<li><?php echo $this->Html->link(__d('view', 'Layouts.footer.privacy_policy.title'), 
				array('controller' => 'pages', 'action' => 'display', 'privacy_policy', 'admin' => false), array('title' =>
				__d('view','Layouts.footer.privacy_policy.title'))); ?>
		</li>
	</ul>		
		
</div>
<!-- END#FOOTER -->