<fieldset>
	<div class="l leftshare">

		<h2>Obrigado por anunciar o produto <?php echo $product['Product']['title']?>!</h2><br/><br/>
		Você recebeu um email de confirmação do anuncio em <?php echo $userEmail?>
		<br><br>
		Agora você precisa ir em <a href="<?php echo Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => true))?>">Minha loja</a> e nos informar seu CEP, assim calcularemos o frete dos produtos para você. Aproveite e se cadastre no <strong><a href="https://www.paypal.com/br/webapps/mpp/merchant" target=_blank rel="nofolow">PayPal</a></strong> para receber de forma mais rápida e fácil seus pagamentos!
		
		<br/>	
	</div>
	<div class="r rightshare">

		<h2>Quer que as pessoas vejam sua loja?</h2>Compartilhe com seus amigos
		<br><br>
			<ul style='width:400px'>
				<li class="breadcrumb-store-invitefriends" style='width:120px; float:left;'>
					<a href='<?php echo Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $product['Store']['slug']), true)?>' onclick='FB.ui({method: "send", link: this.href, picture: "https://<?php echo $_SERVER['SERVER_NAME'] . '/img/logo.jpg';?>", name: "<?php echo addslashes($product['Store']['title'].__d('view','Invitefriends.header.name'))?>", description: "<?php echo addslashes(__d('view','Invitefriends.header.description'))?>" }); return false'><span></span>Divulgue sua loja</a>
				</li>
				<li class="breadcrumb-store-sharefriends"  style='width:140px'>
					<a href='<?php echo Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $product['Store']['slug']), true)?>' onclick='FB.ui({method: "feed", link: this.href, picture: "https://<?php echo $_SERVER['SERVER_NAME'] . '/img/logo.jpg';?>", name: "<?php echo addslashes($product['Store']['title'].__d('view','Invitefriends.header.name'))?>", description: "<?php echo addslashes(__d('view','Invitefriends.header.description'))?>" }); return false'><span></span>Compartilhe sua loja</a>
				</li>
			</ul>
		<br/>

		<br class="clear" />
		<h2>Ou então recomende <?php echo $product['Product']['title']?> para seus amigos!</h2>
		
		<br class="clear">
		
		<ul>
			<?php // PINTEREST
			if (isset($product['Product']['Photo']))
			{
				$linkPinit = 'http://pinterest.com/pin/create/button/?url='.Router::url(array('controller' => 'products', 
					'action' => 'view', 'admin' => false, 'product' => $product['StoreProduct']['slug']), true).
					'&media='.Router::url(	'/'.$product['Product']['Photo'][0]['dir'].'/thumb/large/'.$product['Product']['Photo'][0]['filename'],true).
					'&description='.$product['Product']['title'];
						
				echo "<li>".$this->Html->link(
					$this->Html->image('//assets.pinterest.com/images/PinExt.png', array('alt' => 'Pin It', 'border' => '0')),
					$linkPinit, array('escape' => false, 'class'=> 'pin-it-button', 'target' => '_blank', 'title' => 'Pin It!', 'count-layout' => 'horizontal'))."&nbsp;&nbsp;</li>";
			} 
			?>	
			
			<li><?php // FACEBOOK
			echo $this->Facebook->like(array('href' => Router::url(array('controller' => 'products', 
			'action' => 'view', 'admin' => false, 'product' => $product['StoreProduct']['slug']), true), 'send' => false, 'show_faces' => false, 
			'layout' => 'button_count', 'width' => 110)); ?></li>
		
		</ul>

	</div>
</fieldset>

<script>
	$.modal({'src': '?pId=<?php echo $product['StoreProduct']['id']?>', 'height':'130px'}).open()
</script>