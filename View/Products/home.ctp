<?php
$this->set('body_class', 'home'); 

// METAS
$this->set('meta_description', __d('view', 'Layouts.default.meta.home.description'));
$this->set('meta_keywords', __d('view', 'Layouts.default.meta.home.keywords'));

if(!empty($this->params['named']['filter'])){ $filter = $this->params['named']['filter']; }else{ $filter = ""; }

?>

<?php if (!$is_ajax) : ?>

    <div id="product-view-mode">
	    <span><?php echo __d('view', 'Layouts.default.filters.type.Title'); ?></span>		

		<?php echo $this->Html->link('<span>'.__d('view', 'Layouts.default.filters.products.Title').'</span>', '/?vtype=product', 
        array('title' => __d('view', 'Layouts.default.filters.products.Title'), 'escape' => false, 'class' => 'spriteButton type3 button '.(!isset($this->request->query['vtype']) || $this->request->query['vtype'] != 'store' ? 'selected' : '') )); ?>    
    
		<?php echo $this->Html->link('<span>'.__d('view', 'Layouts.default.filters.store.Title').'</span>', '/?vtype=store', 
        array('title' => __d('view', 'Layouts.default.filters.store.Title'), 'escape' => false, 'class' => 'spriteButton type3 button '.(isset($this->request->query['vtype']) && $this->request->query['vtype'] == 'store' ? 'selected' : ''))); ?>    
        
    </div>
    <div class="clear"></div>
    
				<?php 
				if(isset($this->request->query['vtype']) && $this->request->query['vtype'] == "store"){ 
				 	$title_carousel = __d('view', 'Home.storesdestak.Title');
				 	$title_others   = __d('view', 'Home.storesothers.Title');					
					$carousel_item  =  $premiumStore;
					

				}else{ 
				 	$title_carousel = __d('view', 'Home.productsdestak.Title');
				 	$title_others   = __d('view', 'Home.productsothers.Title');					
					$carousel_item  =  $premiumProduct;
				} ?>
	<?php if(!empty($carousel_item)){ ?>
        <div id="home-carousel" <?php if (isset($this->request->query['vtype'])) : echo 'class="'.$this->request->query['vtype'].'"'; endif; ?>>
            <div class="title"><div>&nbsp;</div><h2 class="title-font"><?php echo $title_carousel; ?></h2></div>
            <div id="carousel">
            <?php
                foreach ($carousel_item as $key => $value)
                {
                    // Listing products
					if(!isset($value['products']))
					{
						print $this->element('products' . DS . 'view', array('product' => $value));
					}
					// Listing stores
					else
					{
						print $this->element('carouselStore', array('store' => $value));
					}       
				}
            ?>
            </div>
            <div class="clear"></div>
        </div>

        <div class="clear"></div>
    	<h2 class="home-tit-others title-font"><span><?php echo $title_others; ?></span></h2>
	<?php } ?>
    
    <div id="product-list">
        <?php
        foreach ($products as $key => $value)
        {
        	// Listing products
        	if(!isset($value['products']))
        	{
        		print $this->element('products' . DS . 'view', array('product' => $value));
        	}
        	// Listing stores
        	else
        	{
        		print $this->element('boxStore', array('store' => $value));
        	} 
        }
        ?>
	    <div class="clear"></div>          
    </div>
    <div class="clear"></div>
    <div id="loading-content">&nbsp;</div>
    <div id="viewmore" style="display:none;">(+) <a href="#" title="Ver mais Produtos">Ver mais Produtos</a></div>
	
<?php

echo $this->Html->scriptBlock('

	$("#viewmore > a").click(function(e){
		e.preventDefault();
		scrollQtd = 0;
		$("#loading-content").show();
		$("#viewmore").hide();
		$.get("'. Router::url(array('controller' => 'products', 'action' => $this->action, 'filter' => $filter, 'search'=>(isset($this->request->data['Search']['search']) ? $this->request->data['Search']['search'] : null), 'category' => (isset($this->request->params['named']['category']) ? $this->request->params['named']['category'] : null), 'page'=> (isset($this->request->params['named']['page']) ? $this->request->params['named']['page'] : 0), '?'=>array('vtype'=>(isset($this->request->query['vtype']) && $this->request->query['vtype'] == 'store' ? 'store' : 'product')))) . '", { last_row: $("#product-list > div.product").last().find("span.row_number").text() }, function(data){
			$("#loading-content").hide();
			scrollCheckProcessing = false;
			if ($.trim(data))
			{
				$(data).filter(".product").each(function(){
						if (!$("#product-list > div.product").filter("#" + $(this).attr("id")).length){
							$(this).appendTo("#product-list");
						}
				});
				
				FB.Canvas.setSize();
			}else{
				window.clearInterval(scrollCheckInterval);
			}
		});		
	});


	var scrollCheckProcessing = false;
	var scrollCheckInterval;
	
	function facebook_started()
	{
 		scrollQtd = 0;			 
		scrollCheckInterval = window.setInterval(
			function()
			{
				if (!scrollCheckProcessing)
				{
					FB.Canvas.getPageInfo(
						function(fbCanvasInfoObject)
						{
							var userView = fbCanvasInfoObject.scrollTop + fbCanvasInfoObject.clientHeight;
							var productsListTop = $("#product-list").position().top + fbCanvasInfoObject.offsetTop;
						
							if (userView >= (productsListTop + $("#product-list").height() - $("#product-list > div.product").last().height() * 2))
							{
							
								scrollCheckProcessing = true;
								
								if(scrollQtd>3){
									$("#loading-content").hide();
									$("#viewmore").show();
									FB.Canvas.setSize();									
								}else{
									$("#loading-content").show();
									$("#viewmore").hide();										
									scrollQtd++;										
									$.get("'. Router::url(array('controller' => 'products', 'action' => $this->action, 'filter' => $filter, 'search'=>(isset($this->request->data['Search']['search']) ? $this->request->data['Search']['search'] : null), 'category' => (isset($this->request->params['named']['category']) ? $this->request->params['named']['category'] : null), 'page'=> (isset($this->request->params['named']['page']) ? $this->request->params['named']['page'] : 0), '?'=>array('vtype'=>(isset($this->request->query['vtype']) && $this->request->query['vtype'] == 'store' ? 'store' : 'product')))) . '", { offset: $("#product-list > div.product").length }, function(data){
										$("#loading-content").hide();
										scrollCheckProcessing = false;
										if ($.trim(data))
										{
											$(data).filter(".product").each(function(){
													if (!$("#product-list > div.product").filter("#" + $(this).attr("id")).length){
														$(this).appendTo("#product-list");
														FB.XFBML.parse(document.getElementById($(this).attr("id")));
													}

											});
											
											FB.Canvas.setSize();
										}else{
											window.clearInterval(scrollCheckInterval);
										}
									});
								}	
							}
						}
					);
				}
			}
		, 2000);
	}
'); ?>
	
<?php  else: ?> 
	<?php 
		if (isset($this->request->query['vtype']) && $this->request->query['vtype'] == 'store')
		{
			foreach ($products as $key => $value) print $this->element('boxStore', array('store' => $value));
		}
		else
		{
			foreach ($products as $key => $value) print $this->element('products' . DS . 'view', array('product' => $value));
		}
	?>
<?php endif; ?>