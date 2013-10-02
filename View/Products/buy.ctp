<?php 
	$this->set('body_class', 'product buy');
	$this->set('body_page', 'buy');	

	// ### BREADCRUMB
	if(!empty($product['Product']['Category']['ParentCategory']['title'])){
		$this->Html->addCrumb($product['Product']['Category']['ParentCategory']['title'], 
		array('controller' => 'products', 'action' => 'index', 'category' => $product['Product']['Category']['ParentCategory']['slug'], 'admin' => false)
		);
	}	
	$this->Html->addCrumb($product['Product']['Category']['title'], array('controller' => 'products', 'action' => 'index', 
		'category' => $product['Product']['Category']['slug'], 'admin' => false));
	$this->Html->addCrumb($product['Product']['title'], array('controller' => 'products', 'action' => 'view', 
		'product' => $product['StoreProduct']['slug']));	

	$this->Html->addCrumb(__d('view','Products.buy.title'), '');

	if(!empty($product['Product']['Photo'][0]['is_external'])){ $url_photo = Configure::read('Amazon.S3.public_path')."/"; }else{ $url_photo = "/"; }																					
?>

<fieldset>
	<?php echo $this->Form->create('Product', array('onsubmit'=>'BZ.showLoadingScreen("'. ($hasPaypal ? 'Você foi redirecionado em outra janela para pagar via PayPal, complete o pagamento para prosseguir!' : __d('view','Products.buy.form.loading')).'")')); ?>
		<?php echo $this->Form->hidden('Product.quantity', array('class' => 'ProductQuantities')); ?>			
	
	<h2><?php echo __d('view', 'Products.buy.title'); ?> <span>(<?php echo __d('view', 'Products.buy.subtitle'); ?>)</span></h2>
	<div class="divisorDouble"></div>

	<div class="buy-product-view">
		<table>
			<tbody>
				<tr>
					<th class="v1"><?php echo __d('view', 'Products.buy.view.product.title'); ?></th>
					<th class="v2"><?php echo __d('view', 'Products.buy.view.name.title'); ?></th>
					<th class="v3"><?php echo __d('view', 'Products.buy.view.quantity.title'); ?></th>
					<th class="v4"><?php echo __d('view', 'Products.buy.view.price.title'); ?></th>			
				</tr>
				<tr>
					<td class="v1" valign="top"><?php echo $this->Html->image($url_photo.$product['Product']['Photo'][0]['dir'].'/thumb/small/'.$product['Product']['Photo'][0]['filename'], 
						array('alt' =>  $product['Product']['title'])); ?></td>
					<td class="v2" valign="top">
						<p><?php echo $product['Product']['title']; ?></p>
						<div class="type new"><?php echo __d('view', 'Products.admin_view.form.field_condition.' . $product['Product']['condition']); ?></div>				
					</td>
					<td class="v3" valign="top">
						<div><?php echo $quantity; ?></div>
					</td>
					<td class="v4" valign="top"><p><?php echo $this->Number->currency($product['Product']['price'], 'BRR'); ?></p></td>				
				</tr>			
			</tbody>			
		</table>
	</div>

	<?php // PERSONAL DATA ####  ?>
		<?php echo $this->element('buy/personal_data'); ?>
			
				
	<?php // PRODUCT DELIVERY ####  ?>
	<br class="clear" />		
	<div class="buy-product-delivery">
		<?php echo $this->element('buy/delivery_data'); ?>
		<?php echo $this->element('buy/delivery'); ?>
	</div>		

	<br class="clear" />
	<div class="divisorDouble"></div>
	<div id="buy-product-total">
		<?php echo __d('view','Products.buy.view.total'); ?>
		<span><?php echo $this->Number->currency($product['Product']['price'], 'BRR'); ?></span>
	</div>
	
	<br class="clear" />
	<div class="buy-product-btnactions">
		<?php echo $this->Html->link(__d('view','Products.buy.form.cancel.title'), 
							array('controller' => 'products', 'action' => 'view', 'admin' => false, 'product' => $product['StoreProduct']['slug']), 
							array('title' => __d('view','Products.buy.form.cancel.title'), 'class' => 'btncancel btn btn1')); ?>		
	
		<?php if ($hasPaypal):?>
			<a href="javascript:void(1)" onClick="$('#ProductBuyForm').attr('action','?action=ppPay').attr('target','_blank');$('#ProductBuyForm').submit();"><img src="<?php echo Configure::read('staticUrl')?>/img/btn_paynowCC_LG.gif" alt="Pagar com PayPal" align="right" style="margin-right:80px" boerder="0"></a>
		<?php else:?>
			<?php echo $this->Form->submit(__d('view', 'Products.admin_view.form.submit.buy'), array('class' => 'btnbuy btn btn3 btnc', 'div' => false, 'name' => 'confirm', 'onclick' => 'if($("INPUT[name=data\\[Delivery\\]\\[Address\\]\\[zipcode\\]]").val() == "" || $("INPUT[name=data\\[Delivery\\]\\[Address\\]\\[zipcode\\]]").val() == "undefined" || $("INPUT[name=data\\[Delivery\\]\\[Address\\]\\[zipcode\\]]").val()=="_____-___"){alert(1);return false};')); ?>
		<?php endif; ?>
	</div>
	
	<?php echo $this->Form->end(); ?>		
</fiedset>	

<?php 
$buy_msg_notavail = __d('view','Products.admin_view.form.buyqtdmsg');
$buy_currency_money = __d('view','Layouts.fb_default.curencymoney');

if(!empty($store['Address']['zipcode'])){ $zipcode = "1"; }else{ $zipcode ="";}

echo $this->Html->scriptBlock('
	var store_zipcode = "'.$zipcode.'";
	
	$(document).ready(function(){
		window.setInterval(buyTotalRefresh, 600);	
		$(".Delivery-Service-type").change(function(){ buyTotalRefresh(); });
		$(".buy-product-delivery-type ul li input[type=radio]").click(function(e){ e.preventDefault(); buyselectDelivery(); });
		$("select.changExistent").change(function() { buyDeliveryCostCheckedAddress(); });
		buyDeliveryCep()
	});	
	
	function buyTotalRefresh(){
		var currency = "'.$buy_currency_money.' ";	
		 var prodQtd = $(".ProductQuantities").val();
		 var total; 
		 var price = null;
			 price = $(".buy-product-delivery-service input:checked").parent().find("span.price").html();
			if(price!=null){ deliveryV = parseFloat(price.replace(",", ".").replace("R$", "")); }else{ deliveryV = "0.00"; }

		   var prodV = parseFloat('.$product['Product']['price'].').toFixed(2);
		 var prodTot = parseFloat(prodV * prodQtd).toFixed(2);
	
		 if((!isNaN(deliveryV)) && (deliveryV!=null)) {
			 total = parseFloat(Number(prodTot) + Number(deliveryV)).toFixed(2);
		 }else{
			 total = parseFloat(prodTot).toFixed(2);
		 }
		 total = total.replace(".", ",");
		$("#buy-product-total span").html(currency + total);
	}	
	
	function buyselectDelivery(){
		buyselectDeliveryclear();
			
		if($(".buy-product-delivery-type ul li input.adressNew").is(":checked")) { 
			$(".buy-product-delivery-type-new").show();
			$("select.changExistent").attr("disabled", "true");
			buyDeliveryCep();

		}else if($(".buy-product-delivery-type ul li input.adressRetreat").is(":checked")) { 
			$(".buy-product-delivery-type-new").hide();
			$(".buy-product-delivery-type-new").find(":input").each(function() { $(this).val(""); });			
			$("select.changExistent").attr("disabled", "true");		
			$(".buy-product-delivery-service").hide();
		}else{
			$(".buy-product-delivery-type-new").hide();
			$("select.changExistent").removeAttr("disabled");			
			$(".buy-product-delivery-type-new").find(":input").each(function() { $(this).val(""); });
		}	
	}
	
	function buyselectDeliveryclear(){	
		$(".buy-product-delivery-service > ul > li > span.price").html("");
	}	

	function buyDeliveryCep(){
		$(".AddressZipcode").blur(function() {
			var userCep = $(this).val(); userCep = userCep.replace("-", "");
			if((userCep!="") && (userCep.length==8)){
					/*
					BZ.showLoadingScreen("'.__d('view','Products.buy.form.loading').'");
					var getlink = "/web_cep.php?cep="+userCep+"&formato=javascript";							
					$.getScript(getlink, function(){
						if(resultadoCEP["resultado"]){
							var endereco = unescape(resultadoCEP["logradouro"])
							var numero = endereco.split(",");
							endereco = endereco.replace(", "+numero,"");
							numero = numero[numero.length-1]
							if (numero == endereco)
							{
								numero = "";
							}

							$(".AddressAddress").val(unescape(resultadoCEP["tipo_logradouro"])+" "+endereco);
							$(".AddressAddressNumber").val(numero);
							$(".AddressDistrict").val(unescape(resultadoCEP["bairro"]));
							$(".AddressCity").val(unescape(resultadoCEP["cidade"]));
							$(".AddressState").val(unescape(resultadoCEP["uf"]));
						}
						BZ.hideLoadingScreen();
					});
					*/
					if (store_zipcode!="") {
						buyDeliveryCost(userCep); 
						ShowDeliveryCost();
					}
			}
		});
	}
	
	function buyDeliveryCost(cep){
		var link = "/products/'.$product['StoreProduct']['hash'].'/delivery";
		var currency = "'.$buy_currency_money.' ";
		
		$.get(link, {zipcode: cep, quantity: '.$quantity.' }, function(data){
			if(data){ 
				$(".buy-product-delivery-service > ul").html(data); 
			}
			buyTotalRefresh();			
		});		
	}	
	
	function buyDeliveryCostCheckedAddress(){
		if ($("select.changExistent").val() !=""){
			var it = "address_"+($("select.changExistent").val()); 
			var value = eval(it); 
			buyDeliveryCost(value); 
			ShowDeliveryCost();			
		}	
	}	
	
	function ShowDeliveryCost(){ 
		// existsData = $(".buy-product-delivery-service > ul > li > span.price").html();
		// if (existsData !="") {
			$(".buy-product-delivery-service").show();	
		// }
	}
	
	function HideDeliveryCost(){ 
		// existsData = $(".buy-product-delivery-service > ul > li > span.price").html();
		// if (existsData !="") {
			$(".buy-product-delivery-service").hide();	
		// }
	} 

'); 

?>