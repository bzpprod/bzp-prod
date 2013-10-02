<?php
$address = $this->requestAction(array('controller' => 'users_addresses', 'action' => 'index', 'api' => true));
$form_address = array();
$firstZip = false;
$jsZips = "var zips = new Array();\n";

foreach ($address as $key => $value){
	if(!empty($value['Address']['zipcode'])){
		if ($firstZip === false)
		{
			$firstZip = str_replace('-','', $value['Address']['zipcode']);
		}
		$jsZips.= "zips[".$value['Address']['id']."] = '".str_replace('-','', $value['Address']['zipcode']).";'\n";
		
		$form_address[$value['Address']['id']] = $value['Address']['address'].", ".$value['Address']['zipcode']." - ".$value['Address']['city']." - ".$value['Address']['state'];
		echo $this->Html->scriptBlock('var address_'.$value['Address']['id'].' = "'.$value['Address']['zipcode'].'"; ');
	}
}	
?>

		<h3><?php echo __d('view','Products.buy.deliveryinformation.title'); ?></h3>
		<div class="divisorDouble"></div>	

			<div class="buy-product-delivery-type">
				<ul>
				<?php if (count($form_address) > 0){ ?>
					<li><input type="radio" name="data[Delivery][Address][type]" class="adressExists" value="exists" checked onclick="HideDeliveryCost();buyDeliveryCost(eval('address_'+$('SELECT[name=data\\[Delivery\\]\\[Address\\]\\[id\\]]').val())); ShowDeliveryCost();" /> 
					<span>Endereço existente</span>
					<?php echo $this->Form->input('Delivery.Address.id', array('type' => 'select', 'div' => false, 'label' => false, 
					'class' => 'changExistent', 'options' => $form_address, 'empty' => false, 'onchange'=>'buyDeliveryCost(zips[this.value]);ShowDeliveryCost();')); ?>
					<script><?php echo $jsZips;?>;onload=function(){buyDeliveryCost('<?php echo $firstZip;?>'); ShowDeliveryCost();}</script>
					</li>
				<?php } ?>			
				<li><input type="radio" name="data[Delivery][Address][type]" class="adressNew" value="new" 
					<?php if (count($form_address) == 0){ echo "checked"; } ?> onclick="HideDeliveryCost()" /> Novo Endereco</li>
				<li><input type="radio" name="data[Delivery][Address][type]" class="adressRetreat" value="no" onclick="HideDeliveryCost();$('#deliveryServicePrice').val(0);"  /> Retirar o produto diretamente com o vendedor</li>					
				</ul>					
			</div>
			
			<?php // PRODUCT DELIVERY > NEW ####  ?>
			<br class="clear" />
			<div class="buy-product-delivery-type-new" <?php if (count($form_address) > 0){ ?> style="display:none;" <?php } ?> >
				<ul>
					<li class="l"><?php echo $this->Form->input('Delivery.Address.zipcode', 
						array('type' => 'text', 'class' => 'AddressZipcode maskCep', 'style' => 'width: 135px;', 'label' =>  __d('view', 'Stores.admin_add.form.field_cep.title')
						)); ?></li>				
					
					<li class="l"><?php echo $this->Form->input('Delivery.Address.address', 
						array('type' => 'text', 'class' => 'AddressAddress', 'style' => 'width: 272px;', 'label' =>  __d('view', 'Stores.admin_add.form.field_address.title')
						)); ?></li>
                        
					<li class="l"><?php echo $this->Form->input('Delivery.Address.addressNumber', 
						array('type' => 'text', 'class' => 'AddressAddressNumber', 'style' => 'width: 50px;', 'label' =>  __d('view', 'Stores.admin_add.form.field_addressNumber.title')
						)); ?></li>

					<li class="l"><?php echo $this->Form->input('Delivery.Address.address_line2', 
						array('type' => 'text', 'class' => 'AddressLine2', 'style' => 'width: 130px;', 'label' =>  __d('view', 'Stores.admin_add.form.field_address_line2.title')
						)); ?></li>
		
					<li><?php echo $this->Form->input('Delivery.Address.district', 
					array('type' => 'text', 'class' => 'AddressDistrict', 'label' => __d('view', 'Products.buy.form.field_district.title'))); ?></li>	
					
					<li class="l"><?php echo $this->Form->input('Delivery.Address.city', 
						array('type' => 'text', 'class' => 'AddressCity', 'label' =>  __d('view', 'Stores.admin_add.form.field_city.title')
						)); ?></li>
						
					<li class="l"><?php echo $this->Form->input('Delivery.Address.state', 
						array('type' => 'text', 'class' => 'AddressState', 'label' =>  __d('view', 'Stores.admin_add.form.field_estate.title')
						)); ?></li>		
					
					<li class="clear"></li>
				</ul>	
			</div>

		<br class="clear" />