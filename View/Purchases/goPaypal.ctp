<?php 
	$this->set('body_class', 'product buy');

?>

<fieldset>
	
	<h2><?php echo __d('view', 'Products.buy.title'); ?></h2>
	<div class="divisorDouble"></div>

	<div class="buy-product-view" align="center">
		
		Pronto, agora para terminar a sua compra basta efetuar o pagamento via PayPal.
		<br>
		<a href="<?php echo Configure::read('Paypal.Flow.endpoint') ?>/webscr?cmd=_ap-payment&country.x=br&locale=pt_BR&change_locale=1&paykey=<?php echo $paypalPayKey?>" target="_blank">
			<img src="/img/paypal-buyNow-pt_Br.jpg" alt="Pague agora com PayPal">
		</a>
	</div>

</fiedset>	

