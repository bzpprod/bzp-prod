<?php $this->set('body_class', 'buy thanks'); ?>

<fieldset>

	<h2>Parabéns pela aquisição.</h2>
	
	<?php if (!empty($transaction['Store']['PaypalAccount']['id'])) : ?>
		<p>Não perca tempo, pague agora via <a href="#" title="Pagar via Paypal" class="btnpaypal">PAYPAL</a>.</p>
	<?php endif; ?>
	
	<p>Você pode acompanhar suas compras e vendas em <?php echo $this->Html->link(__d('view', 'Layouts.fb_default.transactions'), Router::url(array('controller' => 'purchases', 'action' => 'index', 'admin' => true), array('title' => __d('view', 'Layouts.fb_default.purchases')))); ?>.<br/>
	<br/>	

</fieldset>
<?php
echo $this->Html->scriptBlock('
//$(function(){
	var paypalDGFlowMini = new PAYPAL.apps.DGFlowMini({ trigger: null, expType: "mini" });
	
	$("a.btnpaypal").click(
		function(e)
		{
			e.preventDefault();
			
			$.getJSON("' . Router::url(array('controller' => 'payments_paypal', 'action' => 'add', 'admin' => true, 'ext' => 'json')) . '", { transaction: "' . $transaction['Transaction']['hash'] . '" },
				function(response)
				{
					if (response.payment_key != "")
					{
						paypalDGFlowMini.startFlow("https://www.sandbox.paypal.com/webapps/adaptivepayment/flow/pay?expType=mini&country.x=br&locale=pt_BR&change_locale=1&paykey=" + response.payment_key);
					}
				}
			);						
		}
	);	
//});
');
?>