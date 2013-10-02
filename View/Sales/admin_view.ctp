<?php // print_r($transaction); ?>

<div class="transaction_data" style="width:400px;">
	<div class="list">
		<h3>Dados da Transação/Produto</h3>
		<div class="line">
			<div>Data da Compra:</div>
			<div class="i2"><?php echo date('d/m/Y H:i', strtotime($transaction['Transaction']['created'])); ?></div>
			<span class="clear"></span>
		</div>


		<div class="line">
			<div>Cód da Transação:</div>
			<div class="i2"><?php echo $transaction['Transaction']['id']; ?></div>
			<span class="clear"></span>
		</div>
		
		<div class="line">
			<div>Meio de Pagamento:</div>
			<div class="i2">
				<?php if (!empty($transaction['Transaction']['payment']['method'])){ echo $transaction['Transaction']['payment']['method']; }else{ echo "Depósito Bancário"; } ?>
			</div>
			<span class="clear"></span>			
		</div>
		
		<div class="line">
			<table width="100%">
				<tr>
					<td width="33%"><?php echo $transaction['StoreProduct']['Product']['title']; ?></td>
					<td width="33%">-</td>			
					<td width="33%" align="right"><b><?php echo $this->Number->currency($transaction['Transaction']['price'], 'BRR'); ?></b></td>
				</tr>
				<tr>
					<td width="33%">Frete&nbsp;</td>
					<td width="33%">-</td>
					<td width="33%" align="right"><b><?php echo $this->Number->currency($transaction['Transaction']['delivery'], 'BRR'); ?></b></td>
				</tr>
				<tr>
					<td width="33%"><b>Total</b></td>
					<td width="33%">-</td>
					<td width="33%" align="right"><b><?php echo $this->Number->currency($transaction['Transaction']['total_price'], 'BRR'); ?></b></td>
				</tr>	
			</table>
		</div>
	</div>

	<div class="list">
		<h3>Dados do Envio/Frete</h3>

		<?php if(!empty($transaction['Delivery']['service'])){ ?>				
		<div class="line">
			<div>Tipo:</div>
			<div class="i2"><?php echo $transaction['Delivery']['service']; ?>&nbsp;</div>
			<span class="clear"></span>			
		</div>			
		<?php } ?>
		
		<div class="line">
			<div>Cód de Rastreio:</div>
			<div class="i2">
				<?php echo $transaction['Delivery']['tracking']; ?>&nbsp;
			</div>
			<span class="clear"></span>			
		</div>	
				
		<?php if(!empty($transaction['Delivery']['Address']['address'])){ ?>
		<div class="line">
			<div>End. de entrega:</div>
			<div class="i2">
				<?php echo $transaction['Delivery']['Address']['address'].",".$transaction['Delivery']['Address']['address_line2']." - ".
				$transaction['Delivery']['Address']['district']." - ".$transaction['Delivery']['Address']['zipcode']." - ".
				$transaction['Delivery']['Address']['city'].",".$transaction['Delivery']['Address']['state']; ?>&nbsp;
			</div>
			<span class="clear"></span>			
		</div>		
		<?php } ?>
		
	</div>

	<div class="list">
		<h3>Dados do Comprador</h3>
		<div class="line">
			<div>Nome:</div>
			<div class="i2"><?php echo $transaction['Buyer']['name']; ?></div>
			<span class="clear"></span>			
		</div>
		
		<div class="line">
			<div>Email:</div>
			<div class="i2"><?php echo $this->Html->link($transaction['Buyer']['email'], 'mailto:' . $transaction['Buyer']['email']); ?></div>
			<span class="clear"></span>			
		</div>	
			
		<div class="line">
			<div>Cidade/Estado:</div>
			<div class="i2"><?php echo $transaction['Buyer']['location']; ?>&nbsp;</div>
			<span class="clear"></span>			
		</div>	
	</div>

	<div class="list">
		<h3>Dados do Vendedor</h3>
		<div class="line">
			<div>Nome:</div>
			<div class="i2"><?php echo $transaction['Store']['title']; ?></div>
			<span class="clear"></span>			
		</div>
	
		<div class="line">
			<div>Email:</div>
			<div class="i2"><?php echo $this->Html->link($transaction['Store']['User']['email'], 'mailto:' . $transaction['Store']['User']['email']); ?>&nbsp;</div>
			<span class="clear"></span>			
		</div>	
	
		<div class="line">
			<div>Cidade/Estado:</div>
			<div class="i2"><?php echo $transaction['Store']['User']['location']; ?>&nbsp;</div>
			<span class="clear"></span>			
		</div>		
		
		<?php if(!empty($transaction['Store']['Phone']['number'])){ ?>
		<div class="line">
			<div>Telefone:</div>
			<div class="i2"><?php echo $transaction['Store']['Phone']['number']; ?>&nbsp;</div>
			<span class="clear"></span>			
		</div>
		<?php } ?>		
	
		<?php if(!empty($transaction['Store']['Phone']['number'])){ ?>
		<div class="line">	
			<div>Tel:</div>
			<div class="i2"><?php echo $transaction['Store']['Phone']['number']; ?>&nbsp;</div>
			<span class="clear"></span>					
		</div>	
		<?php } ?>	
	</div>

</div>