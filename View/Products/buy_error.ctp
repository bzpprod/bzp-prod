<?php 
	$this->set('body_class', 'product buy');
	$this->set('body_page', 'buy');	

	// ### BREADCRUMB
	if(!empty($product['Product']['Category']['ParentCategory']['title'])){
		$this->Html->addCrumb($product['Product']['Category']['ParentCategory']['title'], 
		array('controller' => 'products', 'action' => 'index', 'category' => $product['Product']['Category']['ParentCategory']['slug'], 'admin' => false)
		);
	}	

	$this->Html->addCrumb(__d('view','Products.buy.title'), '');

?>

<fieldset>
	
	<?php if (isset($errorMsg)):?>
		<?php echo $errorMsg?>
		
	<?php else:?>
		Desculpe,  houve um erro com o processamento do seu pagamento junto ao PayPal.  Por favor, <a href="<?php echo Configure::read('Paypal.Flow.endpoint')?>/webscr?cmd=_ap-payment&country.x=br&locale=pt_BR&change_locale=1&paykey=<?php echo $payKey?>">clique aqui</a> para tentar efetuar novamente o pagamento.  Obrigado.
		
	<?php endif;?>
	
	<br><br><br>
	<div class="btner buttonWrapper">
		<a class="spriteButton type1 accepted" href="<?php echo Configure::read('baseUrl');?>" rel="nofollow"><span>Sair</span></a>
	</div>
	
</fiedset>	
