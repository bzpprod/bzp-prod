<?php
if(!empty($product['ViewFriendLikedProduct'])){ $product['Product'] = $product['ViewFriendLikedProduct']; }
else if(!empty($product['ViewFriendProduct'])){ $product['Product'] = $product['ViewFriendProduct'];}
else if(!empty($product['ViewLikedStoreProduct'])){ $product['Product'] = $product['ViewLikedStoreProduct']; }

	if($product['Store']['is_personal'] == 1): 
		$link = $product['StoreProduct']['url']; 
	else :
		$link = $product['StoreProduct']['appUrl']; 
	endif;
	
?>
<?php if(!$product['Product']['quantity_available'] > 0){ $classeE = " lkturnon"; }else{$classeE = "";} ?>

<div class="product" id="<?php echo $product['StoreProduct']['hash']; ?>">
    
    <div class="product-image">

		<?php 
		// PRODUCT IMAGE #####
			echo $this->Html->link(
                    $this->Html->image(Configure::read('staticUrl') . '/files/fx/230x230/product/photo/'.$product['Product']['Photo'][0]['filename'], array(
                    'alt' =>  $product['Product']['title'], 'width'=> '230px', 'height'=>'230px')),
                    $link,							
                    array('escape' => false, 'title' => $product['Product']['title'], 'target'=>($product['Store']['is_personal'] == 1 || strpos($this->request->params['action'],'fanpage_') === 0 ? '_self' : '_blank'))
            );		
        ?>

	    <div class="like">
	    	<fb:like href="<?php echo $product['StoreProduct']['url']?>" send="0" show_faces="0" layout="button_count" width="100"></fb:like>
        </div>        
    </div>
    
    <h3>
		<?php 
             echo $this->Html->link($product['Product']['title'],
				$link,
                array('title' => $product['Product']['title']));				
        ?>
    </h3>

    <div>
        <h4><?php 
                echo $this->Html->link(				
                        $this->Html->image($product['Store']['pictureUrl'],
                        array('alt' => $product['Store']['title'], 'class' => 'thumb-seller')).$product['Store']['title'], 
						$link, 
                    	array('escape' => false, 'title' => $product['Store']['title'])
                ); 
            ?></h4>
            
        <div><h4>
				<?php
                     echo $this->Html->link(sprintf('R$ <span>%s</span>', $this->Number->currency($product['Product']['price'], 'BRR', array('before' => ''))), 
					$link,
                    array('escape' => false, 'title' => $product['Product']['title'])); 
				?>
        </h4></div>
    </div>    
	<div class="clear"></div>

</div>