<?php
$this->set('body_class', 'transactions');
$this->set('body_page', 'sales_view');
$this->Html->addCrumb(__d('view', 'Layouts.default.sales.Title'), array('controller' => 'sales', 'action' => 'index', 'admin' => true));
?>


<?php // echo $this->element('seller' . DS . 'profile', array('product' => $value));?>
<div id="myaccount-view-mode">
	<?php echo $this->Html->link(__d('view', 'Layouts.default.sales.Title'), 
        array('controller' => 'sales', 'action' => 'index', 'store' => $store['Store']['slug'], 'admin' => true), 
        array('class' => 'btn btn1 btn1_3 selected', 'title' => __d('view', 'Layouts.default.sales.Title') 
    )); ?>    
    
	<?php if (!empty($store['Store']['is_personal'])){ ?>    
		<?php echo $this->Html->link(__d('view', 'Layouts.default.purchases.Title'), 
            array('controller' => 'purchases', 'action' => 'index', 'admin' => true), 
            array('class' => 'btn btn1', 'title' => __d('view', 'Layouts.default.purchases.Title')
        )); ?>
	<?php } ?>    
    
</div>

<div id="myaccount-filter">
	<?php echo $this->element('paginator'); ?>
</div>
<div class="clear"></div>

<div id="myaccount-items">
	<?php foreach ($transactions as $key => $value){ // print_r($value); ?>


	<!-- ITEM -->
	<div class="myaccount-item">
	    <div class="transactionH" style="display:none">
			<?php echo $this->Form->hidden('Transaction.hash', array('class' => 'hash', 'value' => $value['Transaction']['hash'])); ?>
        </div>
        
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
					<?php echo $this->Html->link(
						$this->Html->image('https://graph.facebook.com/'.$value['Buyer']['FacebookUser']['fb_user_id'].'/picture?access_token='.$fb_access_token,
						array('alt' =>  $value['Buyer']['name'], 'class'=> 'thumb-seller')), 'http://facebook.com/'.$value['Buyer']['FacebookUser']['fb_user_id'], 
						array('escape' => false, 'target' => '_blank', 'title' => $value['Buyer']['name'])); ?>
                    </td>
                    <td valign="middle">
                    	<span><?php echo __d('view', 'Transactions.purchases.buyer.title'); ?></span><br/>
                    	<div class="bl1"><?php echo $value['Buyer']['name']; ?></div>
                    
                        <?php echo $this->Html->link(__d('view', 'Transactions.profile.title'), 
						'http://facebook.com/'.$value['Buyer']['FacebookUser']['fb_user_id'], 
						array('escape' => false, 'target' => 'blank', 'title' => __d('view', 'Transactions.profile.title'))); ?>

                    </td>
                    <td valign="middle" class="bl2"><?php echo __d('view', 'Transactions.purchases.qualification.title'); ?>
							
							<?php  // QUALIFICATION SENDED 
								if(empty($value['SaleQualification']['id'])){
									echo $this->Html->link('fazer', array('controller' => 'sales', 'action' => 'qualify', 'admin' => true, 
									'transaction' => $value['Transaction']['hash']), 
									array('escape' => false, 'title' => 'Qualificar', 'class' => 'lightbox qualifier_lk', 'id' => $value['Transaction']['hash']));
									$class=' style="display:none"'; 
								}else{ $class=""; }							

							echo '<span id="icon-qualifications-sender"'.$class.'
							class="qualification-icon '.$value['SaleQualification']['qualification'].'">&nbsp;</span>';
                        ?>
                    </td>
                    <td valign="middle" class="bl2">
						<?php  // TRANSACTION INFO
                        echo $this->Html->link(__d('view', 'Transactions.purchases.info.transactiondata.title'), array('controller' => 'sales', 'action' => 'view',
						'admin' => true, 'store' => $value['Store']['slug'], 'transaction' => $value['Transaction']['hash']),
						array('escape' => false, 'title' => __d('view', 'Transactions.purchases.info.transactiondata.title'), 'class' => 'lightbox')
                        ); ?>
                    </td>
               	</tr>
            </table> 
        </div>    

        <div class="product-price">
            <span class="price"><?php echo $this->Number->currency($value['Transaction']['price'], 'BRR'); ?></span>
            <span><?php echo __d('view', 'Transactions.purchases.deliveryqualification.title')." 
			".$this->Number->currency($value['Transaction']['delivery'], 'BRR', array('before' => '')); ?></span>
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
	$(document).ready(function(){
		$(".qualifier_lk").click(function(){
			qualify_link = $(this).attr("href");
			  qualify_id = $(this).attr("id");
		});
	});		
	
'); ?>	