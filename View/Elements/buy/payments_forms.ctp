	<div class="buy-product-pay">
		<h3><?php echo __d('view', 'Products.admin_add.form.pay.title'); ?></h3>
		<div class="divisorDouble"></div>				
		<ul>
			<?php if (!empty($store['Store']['Paypal']['email'])){ ?>
				<li class="l"><input type="radio" name="buy-product-pay" class="deliveryService_Correios" value="paypal" checked />
					Pagar com o Paypal <span class="paypal-icons">&nbsp;</span></li>
				<li class="r">
			<?php } else { ?> 
				<li class="l">
			<?php } ?>
					<input type="radio" name="buy-product-pay" class="deliveryService_Correios" value="deposito" 
					 <?php if (empty($store['Store']['Paypal']['email'])) : ?> checked <?php endif; ?>  /> Depósito Bancário</li>
		</ul>
		<br class="clear" />		
	</div>