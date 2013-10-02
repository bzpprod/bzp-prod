<?php // print_r($delivery); ?>
<?php if(!empty($delivery)): ?>

	<input type="hidden" name="data[Delivery][price]" id="deliveryServicePrice" value="<?php echo $delivery[1]['price']; ?>">
	<li class="l"><input type="radio" class="Delivery-Service-type nostyle" name="data[Delivery][service]" value="<?php echo $delivery[1]['id']; ?>" checked onClick="$('#deliveryServicePrice').val('<?php echo $delivery[1]['price']; ?>')" /> 
	PAC <span class="price PAC">R$<?php echo str_replace(".",",",$delivery[1]['price']); ?></span></li>
	
	<li><input type="radio" class="Delivery-Service-type nostyle" name="data[Delivery][service]" value="<?php echo $delivery[0]['id']; ?>"  onClick="$('#deliveryServicePrice').val('<?php echo $delivery[0]['price']; ?>')" /> 
	Sedex <span class="price SEDEX">R$<?php  echo str_replace(".",",",$delivery[0]['price']); ?></span>

<?php endif; ?>