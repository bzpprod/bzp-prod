<?php 
	$this->set('body_class', 'buy thanks'); 
	$this->set('body_page', 'buy');
	
// FOLLOW LINK
$store_follow = $this->requestAction(array('controller' => 'stores', 'action' => 'following', 'store' => $transaction['Store']['slug']));
if($store_follow == true){ $start_follow = "unfollow"; }else{ $start_follow = "follow"; }
$follow['unfollow']['action'] = "unfollow";
$follow['unfollow']['text'] = __d('view','Stores.unfollow.title');
$follow['unfollow']['link'] = Router::url(array('controller' => 'stores', 'action' => 'unfollow', 'store' => $transaction['Store']['slug']));
$follow['follow']['action'] = "follow";
$follow['follow']['text'] = __d('view','Stores.follow.title');
$follow['follow']['link'] = Router::url(array('controller' => 'stores', 'action' => 'follow', 'store' => $transaction['Store']['slug']));				
$store_c = "1"; 
?>



<fieldset>
<div class="l leftshare">

		<h2>Parabéns pela compra.<br/><br/></h2>
		<?php if ((!empty($transaction['Store']['PaypalAccount']['id'])) && (!empty($transaction['Store']['PaypalAccount']['email'])) && $transaction['Transaction']['status'] != 'payed') : ?>
			<p>Não perca tempo, pague agora via<br>
			<a href="#" title="Pagar via Paypal" class="btnpaypal">&nbsp;</a></p>
				<style>
		.btnpaypal {margin:0 auto;background-image:url('/img/paypal-buyNow-pt_Br.jpg');width:114px;height:114px;} 
	</style>
			</p>
		<?php endif; ?>
		
		<p>Você pode acompanhar suas compras e vendas em <?php echo $this->Html->link(__d('view', 'Layouts.default.purchases.Title'), Router::url(array('controller' => 'purchases', 'action' => 'index', 'admin' => true), array('title' => __d('view', 'Layouts.fb_default.purchases')))); ?>.<br/>
		<br/>	
</div>

<div class="r rightshare">

		<h2 style="padding:0;">Gostou deste Produto? <br/>Compartilhe com seus amigos</h2>

		<br class="clear" />
		<br/>
		<ul>
			<?php // PINTEREST
			$linkPinit = 'http://pinterest.com/pin/create/button/?url='.Router::url(array('controller' => 'products', 
					'action' => 'view', 'admin' => false, 'product' => $transaction['StoreProduct']['Product']['slug']), true).
					'&media='.Router::url(	'/'.$transaction['StoreProduct']['Product']['Photo'][0]['dir'].'/thumb/large/'.$transaction['StoreProduct']['Product']['Photo'][0]['filename'],true).
					'&description='.$transaction['StoreProduct']['Product']['title'];
						
			echo "<li>".$this->Html->link(
					$this->Html->image('//assets.pinterest.com/images/PinExt.png', array('alt' => 'Pin It', 'border' => '0')),
					$linkPinit, array('escape' => false, 'style' => 'margin-right:10px;', 'class'=> 'pin-it-button', 'target' => '_blank', 'title' => 'Pin It!', 'count-layout' => 'horizontal'))."&nbsp;&nbsp;</li>"; 
			?>	

			<li><?php // FACEBOOK LIKE
			echo $this->Facebook->like(array('href' => Router::url(array('controller' => 'products', 
			'action' => 'view', 'admin' => false, 'product' => $transaction['StoreProduct']['Product']['slug']), true), 'send' => false, 'show_faces' => false, 
			'layout' => 'button_count', 'width' => 110)); ?></li>
		
		</ul>

		<br class="clear" />
		<br/>
		<p>Siga esta loja e seja o primeiro a saber de novos produtos!</p><br/>

			<ul>
				<li id="store-follow">
					<div class='buttonWrapper follow'>
        				<a class="spriteButton type2 btnFollow <?php echo $follow[$start_follow]['action']?>  <?php if ($store_follow == true):?>selected<?php endif;?>" href="<?php echo $follow[$start_follow]['link']?>" rel="nofollow"><span><?php echo $follow[$start_follow]['text']?></span></a>
        			</div>
				</li>		
			</ul>				

</div>

</fieldset>


<?php
if(!empty($product['Product']['Photo'][0]['is_external'])){ $url_photo = Configure::read('Amazon.S3.public_path')."/"; }else{ $url_photo = "/"; }
		
echo $this->Html->scriptBlock('

	$.modal({"src": "?v=popup", "height":"150px","top":"215px"}).open()

	var paypalDGFlowMini = new PAYPAL.apps.DGFlowMini({ trigger: null, expType: "mini" });
	
	$("a.btnpaypal").click(
		function(e)
		{
			e.preventDefault();
			
			BZ.showLoadingScreen("Aguarde...");
			$.getJSON("' . Router::url(array('controller' => 'payments_paypal', 'action' => 'add', 'admin' => true, 'ext' => 'json')) . '",
					  { transaction: "' . $transaction['Transaction']['hash'] . '" },
					  function(response) {
						var pk = response.payment_key
						if (pk != "") {
							paypalDGFlowMini.startFlow("' . Configure::read('Paypal.Flow.endpoint') . '/webapps/adaptivepayment/flow/pay?expType=mini&country.x=br&locale=pt_BR&change_locale=1&paykey=" + pk);
						}
					  }
			).complete(
						function() {
							BZ.hideLoadingScreen();
						}
			);
			
		}
	);	

');



if(!empty($store_c)){ 	

	echo $this->Html->scriptBlock('	
		$("#store-header-follow > a").live("click", function(e) { 		
			e.preventDefault();			
			var linkz = $(this).attr("href")+".json";
			var actionz = $(this).attr("id");
				obj = this;
				
			if(actionz=="follow"){
				flw_id = "'.$follow['unfollow']['action'].'";
			   flw_txt = "'.$follow['unfollow']['text'].'";
			  flw_link = "'.$follow['unfollow']['link'].'";
			}else{
				flw_id = "'.$follow['follow']['action'].'";
			   flw_txt = "'.$follow['follow']['text'].'";
			  flw_link = "'.$follow['follow']['link'].'";
			}
	
			$.getJSON(linkz, function(data){
				if ($.trim(data)){
					$(obj).attr("id", flw_id);
					$(obj).attr("href", flw_link);
					$(obj).attr("title", flw_txt);
					$(obj).text(flw_txt);					
					$(obj).removeClass(actionz);
					$(obj).addClass(flw_id);					
				}
			});	
		
	
		});		
	');	
}	

?>