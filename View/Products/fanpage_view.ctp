<?php
	$this->set('body_class', 'product');
	$this->set('body_page', 'view');
		
	// ### BREADCRUMB
	if(!empty($product['Product']['Category']['ParentCategory']['title'])){
		$this->Html->addCrumb($product['Product']['Category']['ParentCategory']['title'],
		array('controller' => 'products', 'action' => 'index', 'category' => $product['Product']['Category']['ParentCategory']['slug'], 'admin' => false)
		);
	}
	$this->Html->addCrumb($product['Product']['Category']['title'], 
	array('controller' => 'products', 'action' => 'index', 'category' => $product['Product']['Category']['slug'], 'admin' => false));
	$this->Html->addCrumb($product['Product']['title'], 
	$product['Store']['FacebookPage']['link'].'&app_data=/fp/'.$product['Store']['slug'].'/'.$product['StoreProduct']['slug']);	

	if(!empty($product['Product']['Photo'][0]['is_external'])){ $url_photo = Configure::read('Amazon.S3.public_path')."/"; }else{ $url_photo = "/"; }
	//$url_photo = "/";
	
	// ### FB PERSONAL LIKE
	$link_Like = Router::url(array('controller' => 'products', 'action' => 'view', 'fanpage' => true, 'store' => $product['Store']['slug'], 
				'product' => $product['StoreProduct']['slug']), true);
	$this->set('fbmeta_url', $link_Like); 
	$this->set('fbmeta_type', 'website');
	$this->set('fbmeta_title', __d('view', 'Layouts.fb_default.bazzapp').' | '.$product['Product']['title'].' 
	por '.$this->Number->currency($product['Product']['price'], 'BRR'));	
	$this->set('fbmeta_image', Router::url($url_photo.$product['Product']['Photo'][0]['dir'].'/thumb/medium/'.$product['Product']['Photo'][0]['filename'], true));
	$this->set('fbmeta_image_width','175');
	$this->set('fbmeta_image_height','175');
	$this->set('fbmeta_description', $product['Product']['description']);	
	
?>

<div class="clear"></div>

<div class="r">
	<?php echo $this->Form->create('Product', array('id' => 'productbuy' , 'url' => array('controller' => 'products', 'action' => 'buy', 'fanpage' => true, 
	'store' => $product['Store']['slug'], 'product' => $product['StoreProduct']['slug']))); ?>
    
	<?php echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id'])); ?>
    
	<h2><?php echo $product['Product']['title']; ?></h2>
    <h3 class="price"><?php echo $this->Number->currency($product['Product']['price'], 'BRR'); ?></h3>
    <div class="type new"><?php echo __d('view', 'Products.admin_view.form.field_condition.' . $product['Product']['condition']); ?></div>

    <?php if($product['Product']['quantity_available'] > 1){ ?><div class="hline"></div><?php } ?>    
    
	<?php if ($product['Product']['quantity_available'] && !$product['Product']['is_deleted']) { ?>
    <div class="infop">
    
	    <?php if($product['Product']['quantity_available'] > 1){ ?>
            <span>Quantidade</span> 
            <div class="qtd">
                <a href="#" title="-" class="menos">-</a>
                <a href="#" title="+" class="mais">+</a>        
                <?php echo $this->Form->input('Product.quantity', 
				array('type' => 'text', 'class' => 'qtd_aval masknumber', 'label' => false, 'div' => false, 'value' => 1)); ?>           
            </div>
            <span><?php echo __d('view', 'Products.admin_view.form.field_with.title').$product['Product']['quantity_available']; ?></span>        
       
	   <?php } else{
				$this->Form->input('Product.quantity', 
				array('type' => 'text', 'label' => false, 'div' => false, 'value' => 1, 'disabled' => ($product['Product']['quantity_available'] == 1)));
			 } ?>           
        
    </div>
	    <div class="hline"></div>
        
		<?php echo $this->Form->submit("Comprar agora",array('div' => false,  'class' => 'btn btn3 btnc',  'name' => 'submit',  'value' => 'Comprar agora')); ?>
        
    <?php } else {?>
    
	    <div class="hline"></div>
		<div class="soldOut">Esgotado</div>    
    <?php } ?>
    
	<div class="share">
		<?php // FACEBOOK
			echo $this->Facebook->like(array('href' => $link_Like, 'send' => false, 'show_faces' => false, 'layout' => 'button_count', 'width' => 110)); ?>
            		
		<?php // INVITE
		/* echo $this->Html->link(__d('view', 'Layouts.default.controls.invite'),'#',
		array('title' => __d('view', 'Layouts.default.controls.invite'), 'div' => false, 'class' => 'btnShare')); */ ?>
		
		<?php // PINTEREST
			$link_Pinit = 'http://pinterest.com/pin/create/button/?url='.
					$link_Like.
					'&media='.Router::url(	'/'.$product['Product']['Photo'][0]['dir'].'/thumb/large/'.$product['Product']['Photo'][0]['filename'],true).
					'&description='.$product['Product']['title'];
						
			echo $this->Html->link(
					$this->Html->image('//assets.pinterest.com/images/PinExt.png', array('alt' => 'Pin It', 'border' => '0')),
					$link_Pinit, array('escape' => false, 'class'=> 'pin-it-button', 'target' => '_blank', 'title' => 'Pin It!', 'count-layout' => 'horizontal')
			); ?>	
	</div>
    <div class="hline dotted"></div>
    
    <div class="descp">
	    <p><?php echo nl2br($product['Product']['description']); ?></p>
    </div>
	<div class="clear"></div>
	
<?php echo $this->Form->end(); ?>
 
 <div class="clear"></div>
 <?php if (is_array($related_products) && count($related_products) > 0 ):?>
 <div id="product-list">
	<h2><?php echo __d('view', 'Products.admin_view.othersproducts.title'); ?></h2>
	<?php foreach ($related_products as $key => $value){
        	if(!empty($value['Product']['Photo'][0]['is_external'])){ $url_photo = Configure::read('Amazon.S3.public_path')."/"; }else{ $url_photo = "/"; }
			print $this->element('products' . DS . 'view', array('product' => $value));
		  }
    ?>
 </div>
 <?php endif; ?>
</div>

<div class="l">
	<div class="images">
		<?php $countPhotos = count($product['Product']['Photo']); if($countPhotos>4){ $countPhotos = 4; } ?>
        <div class="targetarea diffheight">
            <img id="multizoom2" title="<?php echo $product['Product']['title']; ?>" 
            src="<?php echo $url_photo.$product['Product']['Photo'][0]['dir'].'/thumb/large/'.$product['Product']['Photo'][0]['filename']; ?>" />

        </div>
        <div id="description2"><?php echo $product['Product']['title']; ?></div>
        <div class="multizoom2 thumbs gal_thumb">
			<?php for($i=0;$countPhotos>$i;$i++){ 
            	if(!empty($product['Product']['Photo'][$i]['is_external'])){ $url_photo = Configure::read('Amazon.S3.public_path')."/"; }else{ $url_photo = "/"; }?>
                <a href="<?php echo $url_photo.$product['Product']['Photo'][$i]['dir'].'/thumb/extralarge/'.$product['Product']['Photo'][$i]['filename']; ?>" 
                    data-large="<?php echo $url_photo.$product['Product']['Photo'][$i]['dir'].'/thumb/extralarge/'.$product['Product']['Photo'][$i]['filename']; ?>" 
                    title="<?php echo $product['Product']['title']; ?>">
                        <img src="<?php echo $url_photo.$product['Product']['Photo'][$i]['dir'].'/thumb/medium/'.$product['Product']['Photo'][$i]['filename']; ?>" 
                        alt="<?php echo $product['Product']['title']; ?>">
                </a>                
            <?php } ?>
        </div>
	</div>
    	
    <div class="clear"></div>     
 	<div id="product-comments">
	   <?php echo $this->Facebook->comments(array('width' => '490', 'href' => Router::url($this->params->here, true))); ?>    
 	</div>    
</div>

<?php
if(!empty($product['Product']['Photo'][0]['is_external'])){ $url_photo = Configure::read('Amazon.S3.public_path')."/"; }else{ $url_photo = "/"; }

echo $this->Html->scriptBlock('
	product_quantity_available = '.$product['Product']['quantity_available'].';

	$(".share > a.btnShare").click(function(e){
		e.preventDefault();
		FB.ui({
          method: "send",
          link: "'.$product['Store']['FacebookPage']['link'].'&app_data=/fp/'.$product['Store']['slug'].'/'.$product['StoreProduct']['slug'].'",
		  picture: "'.Router::url('/'.$product['Product']['Photo'][0]['dir'].'/thumb/medium/'.$product['Product']['Photo'][0]['filename'], true).'",		  
          name: "'.addslashes($product['Product']['title'].' por '.$this->Number->currency($product['Product']['price'], 'BRR')).'"
        });
	});
	
	
	function facebook_comment_create(response)
	{
		$.getJSON("' . Router::url(array('controller' => 'system', 'action' => 'comment', 'admin' => false, 'ext' => 'json')) . '", { model: "Product", hash: "' . $product['Product']['hash'] . '", comment_id: response.commentID, parent_comment_id: response.parentCommentID });
	}
');
?>