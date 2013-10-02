<?php
$this->set('body_class', 'transactions');
$this->set('body_page', 'purchases_view');
$this->Html->addCrumb(__d('view', 'Layouts.default.purchases.Title'), array('controller' => 'purchases', 'action' => 'index', 'admin' => true));

// echo var_dump($this->params->User);

?>

<?php // echo $this->element('seller' . DS . 'profile', array('store' => $store));?>

<div id="myaccount-view-mode">
	<?php echo $this->Html->link(__d('view', 'Layouts.default.sales.Title'), 
        array('controller' => 'sales', 'action' => 'index', 'store' => $store['Store']['slug'], 'admin' => true), 
        array('class' => 'btn btn1', 'title' => __d('view', 'Layouts.default.sales.Title') 
    )); ?>    
    
	<?php if (!empty($store['Store']['is_personal'])){ ?>   
		<?php echo $this->Html->link(__d('view', 'Layouts.default.purchases.Title'), 
            array('controller' => 'purchases', 'action' => 'index', 'admin' => true), 
            array('class' => 'btn btn1 btn1_3 selected', 'title' => __d('view', 'Layouts.default.purchases.Title')
        )); ?>
	<?php } ?>    
</div>

<div id="myaccount-filter">
	<!-- <select><option>Mais recentes</option></select> -->
	<?php echo $this->element('paginator'); ?>
</div>
<div class="clear"></div>

<div id="myaccount-items">

	<?php foreach ($transactions as $key => $value){ ?>
    
	<!-- ITEM -->
	<div class="myaccount-item" id="<?php echo $value['Transaction']['hash']; ?>">
		<?php echo $this->Form->hidden('Transaction.hash', array('class' => 'hash', 'value' => $value['Transaction']['hash'])); ?>
        
        <div class="product-image">
			<?php echo $this->Html->link($this->Html->image((!empty($value['StoreProduct']['Product']['Photo'][0]['is_external']) ? Configure::read('Amazon.S3.public_path') : '') . '/' .$value['StoreProduct']['Product']['Photo'][0]['dir'].'/thumb/small/'.$value['StoreProduct']['Product']['Photo'][0]['filename'], 			
			array('alt' =>  $value['StoreProduct']['Product']['title'])), 
			array('controller' => 'products', 'action' => 'view', 'admin' => false, 'product' => $value['StoreProduct']['Product']['slug']), 
			array('escape' => false, 'title' =>  $value['StoreProduct']['Product']['title'])); ?>
        </div>    

        <div class="product-description">
            <h2><?php echo $value['StoreProduct']['Product']['title']; ?></h2>
            <table>
				<tr>
                	<td>
					<?php if (empty($value['Store']['FacebookPage']['id'])) : ?>
					<?php echo $this->Html->link(
						$this->Html->image('https://graph.facebook.com/'.$value['Store']['User']['FacebookUser']['fb_user_id'].'/picture?access_token='.$fb_access_token, 				
						array('alt' =>  $value['Store']['title'], 'class'=> 'thumb-seller')), 
						array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $value['Store']['slug']), 
						array('escape' => false, 'target' => 'blank', 'title' => $value['Store']['title'])); ?>
					<?php else : ?>
						<?php echo $this->Html->link(
                        $this->Html->image('https://graph.facebook.com/'.$value['Store']['FacebookPage']['fb_page_id'].'/picture?access_token='.$fb_access_token, 
                        array('alt' => $value['Store']['title'], 'class'=> 'thumb-seller')), $value['Store']['FacebookPage']['link'] . '&app_data=/'. $value['Store']['slug'], 
                        array('escape' => false, 'target' => 'blank', 'title' => $value['Store']['title'], 'target' => '_blank')); ?>
					<?php endif; ?>	                    
                    
                    
                    
                    </td>
                    <td valign="middle"><div class="bl1"><?php echo $value['Store']['title']; ?></div>
                    
						<?php if (empty($value['Store']['FacebookPage']['id'])) : ?>
                            <?php echo $this->Html->link(__d('view', 'Transactions.profile.title'), array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $value['Store']['slug']), array('escape' => false, 'target' => 'blank', 'title' => $value['Store']['title'])); ?>
						<?php else : ?>
                            <?php echo $this->Html->link(__d('view', 'Transactions.profile.title'), $value['Store']['FacebookPage']['link'] . '&app_data=/'. $value['Store']['slug'], array('escape' => false, 'title' => $value['Store']['title'], 'target' => '_blank')); ?>
                        <?php endif; ?>		

                    </td>
                    <td valign="middle" class="bl2">
                        <?php echo __d('view', 'Transactions.purchases.qualification.title'); ?>
                    <?php if ($value['Transaction']['is_canceled'] === false):?>
						<?php  // QUALIFICATION SENDED
                            if(empty($value['PurchaseQualification']['id'])){
                                echo $this->Html->link('fazer', 
								array('controller' => 'purchases', 'action' => 'qualify', 'admin' => true,  
								'transaction' => $value['Transaction']['hash']), 
                                array('escape' => false, 'title' => 'Qualificar', 'class' => 'lightbox qualifier_lk',
                                'id' => $value['Transaction']['hash']));
                                $class=' style="display:none"'; 
                            }else{ $class=""; }
                            
                            echo '<span id="icon-qualifications-sender"'.$class.'
                            class="qualification-icon '.$value['PurchaseQualification']['qualification'].'">&nbsp;</span>';
                            
                        ?>
                    <?php else:?>
                    -
                    <?php endif; ?>
                    </td>
                    <td valign="middle" class="bl2">
						<?php  // TRANSACTION INFO
                        echo $this->Html->link(__d('view', 'Transactions.purchases.info.transactiondata.title'),				
                                array('controller' => 'purchases', 'action' => 'view', 'admin' => true, 'transaction' => $value['Transaction']['hash']), 
                                array('escape' => false, 'title' => __d('view', 'Transactions.purchases.info.transactiondata.title'), 'class' => 'lightbox')
                        ); ?>
                    </td>
               	</tr>
            </table> 
        </div>    

        <div class="product-price">
            <span class="price"><?php echo $this->Number->currency($value['Transaction']['price'], 'BRR'); ?></span>

            <?php if ($value['Transaction']['is_canceled'] === false):?>
            <span><?php echo __d('view', 'Transactions.purchases.deliveryqualification.title')." 
			".$this->Number->currency($value['Transaction']['delivery'], 'BRR', array('before' => '')); ?></span>
            
            <div class="pay">
				<?php if(!empty($value['Store']['PaypalAccount']['email'])){ ?>
		            <div class="paypal">&nbsp;</div>                
                <?php }else{ ?>
		            <div>&nbsp;</div>                
                <?php } ?>
                
				<?php  // PAYPAL ICON
				if((!empty($value['Store']['PaypalAccount']['email'])) && (empty($value['PurchaseQualification']['id'])) && ($value['Transaction']['is_paid'] == 0))
				{
					echo $this->Html->link('Pagar agora', '#', array('escape' => false, 'title' => 'Pagar com Paypal', 'class' => 'btnpay btn btn3'));
				} 
				?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="product-info">
            <p><strong><?php echo __d('view', 'Transactions.info.transactionid.title'); ?></strong>
            	<?php echo $value['Transaction']['id']; ?></p>

            <p><strong><?php echo __d('view', 'Transactions.info.payment.title'); ?></strong>
				<?php if (!empty($transaction['Transaction']['payment']['method'])){ 
						echo $transaction['Transaction']['payment']['method']; 
					}else{ 
						echo __d('view', 'Transactions.payment.deposit.title');
					} ?>
			</p>

            <p><strong><?php echo __d('view', 'Transactions.date.title'); ?></strong>
            	<?php echo date('d/m/Y H:i:s', strtotime($value['Transaction']['created'])); ?></p>
        </div>            
    	<div class="clear"></div>
    </div>
    <!-- END#ITEM -->    
    
	<?php }	?>  
	<div class="clear"></div>
    <?php echo $this->element('paginator'); ?>    
</div>

<?php
echo $this->Html->scriptBlock('
	var paypalDGFlowMini = new PAYPAL.apps.DGFlowMini({ trigger: null, expType: "mini" });
	
	$("a.btnpay").click(
		function(e)
		{
			e.preventDefault();
			var payKey = "";
			
			$.ajax({
				url: "' . Router::url(array('controller' => 'payments_paypal', 'action' => 'add', 'admin' => true, 'ext' => 'json')) . '",
				data: { transaction: $(this).parents("div").find("input.hash").val() },
				dataType: "json",
				async: false,
				success: function(response)
				{
					payKey = response.payment_key;
				}
			});
			
			if (payKey)
			{
				paypalDGFlowMini.startFlow("' . Configure::read('Paypal.Flow.endpoint') . '/webapps/adaptivepayment/flow/pay?expType=mini&country.x=br&locale=pt_BR&change_locale=1&paykey=" + payKey);
			}
		}
	);	

	$(document).ready(function(){
		$(".qualifier_lk").click(function(){
			qualify_link = $(this).attr("href");
			  qualify_id = $(this).attr("id");
		});
	});		
	
');	
?>