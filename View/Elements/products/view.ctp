<?php
if(!empty($product['ViewFriendLikedProduct'])){ $product['Product'] = $product['ViewFriendLikedProduct']; }
else if(!empty($product['ViewFriendProduct'])){ $product['Product'] = $product['ViewFriendProduct'];}
else if(!empty($product['ViewLikedStoreProduct'])){ $product['Product'] = $product['ViewLikedStoreProduct']; }

if (isset($product['StoreProduct']['url'])) {
	if($product['Store']['is_personal'] == 1): 
		$link = $product['StoreProduct']['url']; 
		$link_store = $product['Store']['url']; 
	else :
		$link = $product['StoreProduct']['appUrl']; 
		$link_store = $product['Store']['appUrl']; 
	endif;
	
} else {
	$link_store = Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $seller['Store']['slug']));
	$link		= Router::url(array('controller' => 'products', 'action' => 'view', 'admin' => false, 'store' => $seller['StoreProduct']['slug']));

}
	
?>

<?php if(!$product['Product']['quantity_available'] > 0){ $classeE = " lkturnon"; }else{$classeE = "";} ?>

<div class="product" id="<?php echo $product['StoreProduct']['hash']; ?>">
    
    <div class="product-image">
	        <div class="info_seller">
				 <div class="star" <?php if($product['StoreProduct']['highlight_level'] =="0"){ ?> style="display:none" <?php } ?>>&nbsp;</div>
				
		<?php if(!empty($edit)): ?>
            <div class="edit">
                <div class='buttonWrapper left'>
                       <a class="spriteButton type3 button delete" href="<?php echo '/my-store/' .  $product['Store']['slug'] . '/' . $product['StoreProduct']['slug']?>/delete" rel="nofollow">
                               <span><?php echo __d('view', 'Layouts.default.controls.delete')?></span>
                       </a>
                </div>

                <div class='buttonWrapper left'>
                       <a class="spriteButton type1 button edit" href="<?php echo '/my-store/' .  $product['Store']['slug'] . '/' . $product['StoreProduct']['slug']?>/edit" rel="nofollow">
                               <span><?php echo __d('view', $product['Product']['quantity_available'] > 0 ? 'Layouts.default.controls.edit' : 'Layouts.default.controls.live')?></span>
                       </a>
                </div>                
            </div>
		<?php endif; ?>
		
		<?php 
		// PRODUCT IMAGE #####
        
            echo $this->Html->link(
                    $this->Html->image(Configure::read('staticUrl') . '/files/fx/230x230/product/photo/'.$product['Product']['Photo'][0]['filename'], array(
                    'alt' =>  $product['Product']['title'], 'width'=> '230px', 'height'=>'230px')),
                    $link,							
                    array('escape' => false, 'title' => $product['Product']['title'], 'target'=> ($product['Store']['is_personal'] == 1 || strpos($this->request->params['action'],'fanpage_') === 0 ? '_self' : '_blank'))
            );
        ?>

	    </div>
    </div>
		    <div class="like">
		    	<?php /* o appUrl leva a página do parceiro na frente e a url do produto como parametro, então o FB acha que o like é para a página do parceiro e não para o produto, por isso usar o url ao inves do appUrl */?>
				<fb:like href="<?php echo $product['StoreProduct']['url']?>" send="0" show_faces="0" layout="button_count" width="100"></fb:like>
        	</div>        
            
    <h3>
		<?php 
             echo $this->Html->link($product['Product']['title'],
                $link,
                array('title' => $product['Product']['title'], 'target'=>($product['Store']['is_personal'] == 1 || strpos($this->request->params['action'],'fanpage_') === 0 ? '_self' : '_blank')));				
        ?>
    </h3>


    <div class="txt">
        <h4><?php 
             echo $this->Html->link(				
                        $this->Html->image($product['Store']['pictureUrl'], array('alt' => $product['Store']['title'], 'class' => 'thumb-seller')).$product['Store']['title'], 
                    	$link_store,
                    	array('escape' => false, 'title' => $product['Store']['title'], 'target'=>($product['Store']['is_personal'] == 1 || strpos($this->request->params['action'],'fanpage_') === 0 ? '_self' : '_blank'))
                	); 
            ?></h4>         
        <div><h4 class="bx-price">
        			<?php 
						 echo $this->Html->link(sprintf('R$ <span>%s</span>', $this->Number->currency($product['Product']['price'], 'BRR', array('before' => ''))), 
						$link,
						array('escape' => false, 'title' => $product['Product']['title'], 'target'=>($product['Store']['is_personal'] == 1 || strpos($this->request->params['action'],'fanpage_') === 0 ? '_self' : '_blank'))); 

					?>
        </h4></div>
		<div class="clear"></div>
    </div>    
	<div class="clear"></div>

</div>
