<style>
	.center {
		padding: 0;
	}
</style>
<div style='width:390px;float:left;text-align:center;'>
<br>
	<h1>Obrigado por anunciar o produto "<?php echo $product['Product']['title']?>"!</h1><br>
	Divulgue este produto em sua timeline!
	<br><br><br>
        	<div class='buttonWrapper follow' style='width:200px; margin: 0 auto'>
        		<a class="spriteButton type2" href="#" onclick='parent.FB.api("/me/<?php echo Configure::read('Facebook.Namespace'); ?>:sell", "post", { product: "<?php echo Router::url(array('controller' => 'products', 'action' => 'view', 'admin' => false, 'product' => $product['StoreProduct']['slug']), true)?>" },function(e){if (e.error){console.log(e.error.message);};parent.$(".modal-overlay").remove();parent.$(".modal-window").remove()});' rel="nofollow"><span>Divulgue para seus amigos</span></a>
        	</div>

	<br><br><br>				


</div>