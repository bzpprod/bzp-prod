<div class="l paypal_adv">
        <h2><?php echo __d('view','Products.admin_add_paypal_adv.title'); ?></h2>
		<p><?php echo __d('view','Products.admin_add_paypal_adv.subtitle'); ?></p>
            
	<!-- I HAVE -->
	<fieldset class="l">
	    <div class="title title-font"><?php echo __d('view','Products.admin_add_paypal_adv.haveaccount.title'); ?></div>
		<ul>            
			<li><?php echo $this->Form->input('frm_email', array('type' => 'email', 'style' => 'float:left; width:170px; height:25px;', 'label' => 'Email da sua conta <img src="'.Configure::read('staticUrl').'/img/icon-paypal.jpg" alt="PayPal">', 'value' => $userEmail)); ?>
        		<div class='buttonWrapper follow' style='float:left; margin-top:24px;'>
        			<a class="btn btn3 btnPaypalSubmit" href="javascript:checkPayPal()" rel="nofollow"><span>
					<?php echo __d('view','Products.admin_add_paypal_adv.checkmail.button'); ?></span></a>
        		</div>
        		
			</li>			

			<li id='createPaypalAccount' class='hide'>
				<label class='error'><?php echo __d('view','Products.admin_add_paypal_adv.emailnot.title'); ?>  
                <a href="javascript:modal()" rel="nofollow"><?php echo __d('view','Products.admin_add_paypal_adv.emailnot2.title'); ?></a> no PayPal</label> 
			</li>
		</ul> 

	</fieldset>

	<!-- DONT HAVE -->
	<fieldset class="r">
	    <div class="title title-font"><?php echo __d('view','Products.admin_add_paypal_adv.donthaveaccount.title'); ?></div>
        <ul>
            <div class="btner buttonWrapper" style="margin:24px 0 14px 0">
                <a class="spriteButton type1 accepted" href="javascript:modal()" rel="nofollow">
                	<span><?php echo __d('view','Products.admin_add_paypal_adv.addaccount.title'); ?></span></a>
            </div>
		</ul>            
	</fieldset>
	
    <div class="clear"></div>
</div>

<script>
	function checkPayPal() {
		BZ.showLoadingScreen('<?php echo __d('view','Products.admin_add_paypal_adv.checkingmail.title'); ?>');
		$('#createPaypalAccount').hide()
		
		$.getJSON(BZ.baseUrl + '<?php echo $this->here;?>?action=checkEmail&email='+$('#frm_email').val(), null, function(data) {
			if (data.emailStatus == 1) {
				window.location = '/sell';
			
			} else {
				
				BZ.hideLoadingScreen()
				$('#createPaypalAccount').removeClass('hide').hide().fadeIn();
			}
			
		});
		
	}
	
	function modal() {
		$.modal({'src': '<?php echo str_replace('http://', 'https://', Router::url(array('controller' => 'products', 'action' => 'paypalAdaptiveAccountAction', 'admin' => false), true))?>', 'height':'430px', 'closeable':false}).open()
	}

</script>