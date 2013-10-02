<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head prefix="og: http://ogp.me/ns# [<?php echo Configure::read('Facebook.Namespace'); ?>]:
                 http://ogp.me/ns/apps/[<?php echo Configure::read('Facebook.Namespace'); ?>]#">
	<?php echo $this->Html->charset(); ?>
	<title> <?php echo __d('view', 'Layouts.fb_default.bazzapp'); ?> | <?php echo $title_for_layout; ?></title>
	<?php
                echo $this->Html->meta('icon');
                echo $this->element('meta');

                
                echo $this->Html->css('reset-min.css?' . $__version__);
                echo $this->Html->css('style.css?' . $__version__);
                echo $this->Html->css('style_pages.css?' . $__version__);
                                        
                echo $this->Html->script('html5.js');
                echo $this->Html->script('third/jquery-1.7.2.min.js');
                
                echo $this->Html->script('uniform/jquery.uniform.min.js');
                echo $this->Html->css('uniform/uniform.default');

                echo $this->Html->script('mask/jquery.maskMoney.js');
				echo $this->Html->script('mask/jquery.numeric.js');		
				echo $this->Html->script('mask/jquery.maskedinput.js');
                
                echo $this->Html->script('jquery.modal.js');
				echo $this->Html->script('jquery.currency.js');
        		echo $this->Html->script('jquery.validate.min.js');
                
                echo $this->Html->script('app.js?' . $__version__);
        		echo $this->Html->script('appVar.js?' . $__version__);
                echo $this->Html->script('functions.js?' . $__version__);
                
                echo $this->Html->script('https://www.paypalobjects.com/js/external/apdg.js');
                echo $this->fetch('meta');
                echo $this->fetch('css');
                echo $this->fetch('script');
                
                echo $this->element('google_analytics');
		?>
</head>


<body>
	<?php echo $this->Facebook->init(); ?>
	<div id="global" class="empty <?php print !empty($body_class) ? $body_class : ''; ?>" >
		<div class="center">
			<div id="content">	
					<?php echo $this->fetch('content'); ?>
					<br class="clear" />
			</div> 
		</div>	
	</div>
</body>
</html>
