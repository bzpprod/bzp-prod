<?php 
	if($store['Store']['is_personal'] == 1): 
		$link = $store['Store']['url']; 
	else :
		$link = $store['Store']['appUrl']; 
	endif;
	
?>
	
<div style="width:720px" id="store_<?php echo $store['Store']['hash']; ?>">
 	<div class="star">&nbsp;</div>
	<div class="images">
		<a href="<?php echo $link; ?>" title="<?php echo $store['Store']['title']; ?>">
	        <?php $totCa = count($store['products']); if($totCa > 2){ $totCa = 2; }
				 for ($i=0; $i<$totCa; $i++):
                   echo $this->Html->image(Configure::read('staticUrl') . '/files/fx/230x230/product/photo/'.$store['products'][$i]['Product']['Photo'][0]['filename'], array('alt' =>  $store['products'][$i]['Product']['title'], 'width'=>'230px', 'height'=> '230px'));

				endfor;	
			?>
		</a>
    </div>
	<div class="text">
	    <h3 class="title-font">
			<?php  
                    echo $this->Html->link($store['Store']['title'], 
                        $link,
                        array('escape' => false, 'title' => $store['Store']['title'], 'target' => ($product['Store']['is_personal'] == 1 ? '_self' : '_blank'))
					);
            ?>                
        </h3>
	
        <div class="info title-font">
	        <div class="count"><?php // echo count($store['products']).__d('view','Home.carousel.countproducts.Title'); ?></div>
			<?php  
                    echo $this->Html->link(__d('view','Home.carousel.viewstore.Title'),
                    	$link,
						array('escape' => false, 'title' => $store['Store']['title'], 'target' => ($product['Store']['is_personal'] == 1 ? '_self' : '_blank'), 
						'class' => 'btn btn3')
					);
            ?>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>        
    </div>
	<div class="clear"></div>
</div>