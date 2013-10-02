<style>
	.center {
		padding: 0;
	}
</style>
<div style='width:420px;float:left;text-align:center;'>
<br>
	<h2>Obrigado por comprar o produto <strong><?php echo $transaction['StoreProduct']['Product']['title']?></strong> do vendedor <strong><?php echo $transaction['Store']['User']['name'];?></strong> pelo Bazzapp!!</h2><br>
	Divulgue esta compra em sua timeline!
	<br><br><br>
        	<div class='buttonWrapper follow' style='width:200px; margin: 0 auto'>
        		<a class="spriteButton type2" href="#" onclick='parent.FB.api("/me/<?php echo Configure::read('Facebook.Namespace'); ?>:buy", "post", { product: "<?php echo Router::url(array('controller' => 'products', 'action' => 'view', 'admin' => false, 'product' => $transaction['StoreProduct']['slug']), true)?>" },function(e){if (e.error){console.log(e.error.message);}});parent.$(".modal-overlay").remove();parent.$(".modal-window").remove()' rel="nofollow"><span>Divulgue para seus amigos</span></a>
        	</div>
				
	<br><br><br>				


</div>