<?php 
	if($store['Store']['is_personal'] == 1): 
		$link = $store['Store']['url']; 
	else :
		$link = $store['Store']['appUrl']; 
	endif;
	
?>
<div class="product bxStore" id="<?php echo $store['Store']['hash']; ?>">
    
    <div class="product-image">
	        <div class="info_seller">
		<?php 
            echo $this->Html->link(
                    $this->Html->image(Configure::read('staticUrl') . '/files/fx/230x230/product/photo/'.$store['products'][0]['Product']['Photo'][0]['filename'], array(
                    'alt' =>  $store['products'][0]['Product']['title'], 'width'=>'230px', 'height'=> '230px')),
                    $link,							
                    array('escape' => false, 'title' => $store['products'][0]['Product']['title'], 'target' => ($product['Store']['is_personal'] == 1 ? '_self' : '_blank') ,'rel' => 'nofollow')
            );

        ?>
	    </div>
	    
    </div>
	    <ul class='moreProducts clear'>
	    	<?php for ($i=1, $t=(count($store['products'])>5 ? 5 : count($store['products'])); $i<$t; $i++):?>
	    		<li <?php if ($i == $t-1):?>class='last'<?php endif;?>>
		<?php 
        if(!empty($store['products'][$i]['Product']['Photo'][0]['is_external'])){ $url_photo = Configure::read('Amazon.S3.public_path')."/"; }else{ $url_photo = "/"; }
        //$url_photo = "/";
            echo $this->Html->link(
                    $this->Html->image(Configure::read('staticUrl') . '/files/fx/56x56/product/photo/'.$store['products'][$i]['Product']['Photo'][0]['filename'], array(
                    'alt' =>  $store['products'][$i]['Product']['title'], 'width'=>'56px', 'height'=> '56px')),
                    $link,							
                    array('escape' => false, 'title' => $store['products'][$i]['Product']['title'], 'target' => ($product['Store']['is_personal'] == 1 ? '_self' : '_blank'),'rel' => 'nofollow')
            );

        ?>	    		
	    		</li>
	    	
	    	<?php endfor;?>
	    
	    </ul>
    

	<ul class='storeInfo act<?php echo $store['Store']['follow']['action']?>'>
		<li class="follow">
        	<div class='buttonWrapper follow'>
        		<a class="btn btn2 btnFollow <?php echo $store['Store']['follow']['action']?> 
					<?php if ($store['Store']['isFollowing'] == true):?>selected<?php endif;?>" 
                href="<?php echo $store['Store']['follow']['link']?>" rel="nofollow"><span><?php echo $store['Store']['follow']['text']?></span></a>
        	</div>
        </li>
            	<li class="thumb"><?php 
            	echo $this->Html->image($store['Store']['pictureUrl']
                        , array('alt' => $store['Store']['title']));
                ?></li>
				<li class="toolbar">
					<a href="<?php echo $store['FacebookPage']['link']?>" target="_top" rel="nofollow" class="facebook btn">Facebook Profile</a>
				</li>
            	<li class="name"><?php 
                echo $this->Html->link($store['Store']['title'], 
                    $link,
                    array('escape' => false, 'title' => $store['Store']['title'], 'target' => ($product['Store']['is_personal'] == 1 ? '_self' : '_blank'))
                ); 
                ?></li>  
            <li class="followers vtop tooltip" title="<?php echo __d('view','Stores.profile.followers.title'); ?>"><?php echo $store['Store']['totalFollowers']; ?></li>
            <li class="positive vtop tooltip" title="<?php echo __d('view','Stores.profile.positive_qualified.title'); ?>">
				<?php echo $this->Html->link($store['Store']['qualification_positive'],
                                array('controller' => 'stores_qualifications', 'action' => 'index', 'admin' => false, 'store' => $store['Store']['slug']),
                                array('escape' => false, 'class' => 'tooltip', 'title' => __d('view','Stores.profile.positive_qualified.title'), 'rel' => 'nofollow')
                    ); ?></li>
            <li class="negative vtop tooltip" title="<?php echo __d('view','Stores.profile.negative_qualified.title'); ?>">
				<?php echo $this->Html->link($store['Store']['qualification_negative'],
                                array('controller' => 'stores_qualifications', 'action' => 'index', 'admin' => false, 'store' => $store['Store']['slug']),					
                                array('escape' => false, 'class' => 'tooltip', 'title' => __d('view','Stores.profile.negative_qualified.title'), 'rel' => 'nofollow')
                     ); ?></li>
            <li class="comments vtop tooltip" title="<?php echo __d('view','Stores.profile.comments.title'); ?>">
				<?php echo $this->Html->link($store['Store']['recommendations'],
                                array('controller' => 'stores_qualifications', 'action' => 'index', 'admin' => false, 'store' => $store['Store']['slug']),					
                                array('escape' => false, 'class' => 'tooltip', 'title' => __d('view','Stores.profile.comments.title'))
                     ); ?></li>
        
    </ul>    
</div>
