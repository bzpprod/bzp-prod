<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php echo $this->Facebook->html(); ?>
<head prefix="og: http://ogp.me/ns# [<?php echo Configure::read('Facebook.Namespace'); ?>]: 
                  http://ogp.me/ns/apps/[<?php echo Configure::read('Facebook.Namespace'); ?>]#">

	<?php echo $this->Html->charset(); ?>
    <title><?php echo $title_for_layout; ?> <?php echo __d('view', 'Layouts.fb_default.bazzapp'); ?></title>
	<?php
	
		echo $this->Html->script('html5.js');
		
		echo $this->Html->meta('icon');
		echo $this->element('meta');
						
		echo $this->Html->css('reset-min.css?' . $__version__);
		echo $this->Html->css('style.css?' . $__version__);
		echo $this->Html->css('style_pages.css?' . $__version__);				
		echo $this->Html->script('https://www.paypalobjects.com/js/external/apdg.js');
		echo $this->Html->script('third/jquery-1.7.2.min.js');
		
		echo $this->Html->script('uniform/jquery.uniform.min.js');
		echo $this->Html->css('uniform/uniform.default');
		
		echo $this->Html->script('tooltip/jquery.tipTip.minified.js');
		echo $this->Html->css('tooltip/tipTip');

		echo $this->Html->script('mask/jquery.maskMoney.js');
		echo $this->Html->script('mask/jquery.numeric.js');		
		echo $this->Html->script('mask/jquery.maskedinput.js');

        echo $this->Html->script('jquery.modal.js');
		echo $this->Html->script('jquery.currency.js');
        echo $this->Html->script('jquery.validate.min.js');
        		
		echo $this->Html->script('colorbox/jquery.colorbox.js');
		echo $this->Html->css('colorbox/colorbox');

        echo $this->Html->script('jquery.modal.js');

		echo $this->Html->css('//fonts.googleapis.com/css?family=Source+Sans+Pro:600');		
                
        echo $this->Html->script('app.js?' . $__version__);
		echo $this->Html->script('appVar.js?' . $__version__);
        echo $this->Html->script('functions.js?' . $__version__);		

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');

		echo $this->element('google_analytics');
	?>
</head>

<body lang="br">
	<div id="global" class="adm <?php print !empty($body_class) ? $body_class : ''; ?> <?php print !empty($body_page) ? $body_page : ''; ?>" >

		<?php print $this->element('header'); ?>
        <!-- CONTENT -->        
        <div id="content" class="<?php print !empty($body_class) ? $body_class : ''; ?>" >
	        <div id="cont">        
				<?php echo $this->element('breadcrumb'); ?>
                <?php if (isset($body_page) && $body_page !="buy" && $body_page !="product_add"){ ?>
	                <?php echo $this->element('seller' . DS . 'profile', array('store' => $store)); ?>                
					<?php echo $this->element('store_menu'); ?>
	                <div id="adm-content">
						<?php echo $this->fetch('content'); ?>
		                <div class="clear"></div>
	                </div>
                <?php } else{ ?>
	                <?php echo $this->fetch('content'); ?>
                <?php } ?>           
                <div class="clear"></div>                 
                
            </div>
        </div>   
        <!-- END#CONTENT -->
        
	</div>   
	
	<?php echo $this->Facebook->init(); ?>	
	<?php echo $this->Html->scriptBlock('
	  function facebook_edge_create(response)
	  {
	   $.get("' . Router::url(array('controller' => 'system', 'action' => 'like', 'admin' => false)) . '", { url: response } );
	  }
	  
	  function facebook_edge_remove(response)
	  {
	   $.get("' . Router::url(array('controller' => 'system', 'action' => 'unlike', 'admin' => false)) . '", { url: response } );
	  }
	');?>		    
    
    
	<?php	
	if (Configure::read('debug') == 0 && !$is_robot)
	{
		echo $this->Html->scriptBlock('
			if (top === self) { window.top.location.href = "'.Configure::read('Facebook.redirect') . env('REQUEST_URI').'"; }
						
			function facebook_login_status(response)
			{
				if (response.status != "connected" || (response.status == "connected" && response.authResponse.userID != ' . $logged_user['FacebookUser']['fb_user_id'] . '))
				{
					$.getJSON("' . Router::url(array('controller' => 'users', 'action' => 'logout', 'admin' => false, 'ext' => 'json')) . '",
						function(data)
						{
							window.top.location.href = "' . $fb_login_url . '";
						}
					);
				}
			}		
		');
		
		if ($facebook_friends) echo $this->Html->scriptBlock('$.get("' . Router::url(array('controller' => 'system', 'action' => 'facebook_friends', 'admin' => false, 'ext' => 'json')) . '");');
	}
	
	echo $this->Html->scriptBlock('		
		$(document).ready(function () {
			$("a.lightbox").live("click", function () {
				var caption = this.title;
				var link = this.href;
				FB.Canvas.getPageInfo(function (fbCanvasInfoObject) {
					var fbPosition = fbCanvasInfoObject.scrollTop + 100;
					$.colorbox({ href:link, title:caption, top: fbPosition});
				});
				return false;
			})
		});
	');			

	?>
	</body>
</html>
