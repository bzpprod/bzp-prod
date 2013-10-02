<?php 
	$this->set('body_class', 'home search');
	
	// ### BREADCRUMB	
	if(!empty($this->params['named']['filter'])){ $filter = $this->params['named']['filter']; }else{ $filter = ""; }
	
	if (!empty($this->request->params['named']['search'])){
		$this->Html->addCrumb('Busca ('.$this->request->params['named']['search'].')', 
		array('controller' => 'products', 'action' => 'search', 'admin' => false, 'search' => $this->request->params['named']['search']));		
		$meta_term = $this->request->params['named']['search'];
	}else{
		if(!empty($category['ParentCategory']['title'])){
			$this->Html->addCrumb($category['ParentCategory']['title'], 
			array('controller' => 'products', 'action' => 'index', 'category' => $category['ParentCategory']['slug'], 'admin' => false)
			);
			$meta_term = $category['ParentCategory']['title'];			
		}	
		
		if (!empty($category['Category']['title'])){
			$this->Html->addCrumb($category['Category']['title'], 
			array('controller' => 'products', 'action' => 'index', 'category' => $category['Category']['slug'], 'admin' => false)
			);
			$meta_term = $category['Category']['title'];		
		}

	}
	
	// METAS
	$this->set('title_for_layout', sprintf(__d('view','Layouts.default.meta.search.title'), $meta_term));
	$this->set('meta_description', sprintf(__d('view','Layouts.default.meta.search.description'), $meta_term));
	$this->set('meta_keywords', sprintf(__d('view','Layouts.default.meta.search.keywords'), $meta_term));

?>

<?php if (!$is_ajax) : ?>

    <div class="clear"></div>
	<?php print $this->element('breadcrumb'); ?>

    <div class="clear"></div>
<?php /*
    <div id="product-view-mode" style="float:right">
        <h2 style="display:none">Produtos</h2>
	    <span><?php echo __d('view', 'Layouts.default.filters.type.Title'); ?></span>		

		<?php echo $this->Html->link(__d('view', 'Layouts.default.filters.products.Title'), '#', 
        array('title' => __d('view', 'Layouts.default.filters.products.Title'), 'class' => 'btn btn1 btn1_3')); ?>    
    
		<?php echo $this->Html->link(__d('view', 'Layouts.default.filters.store.Title'), '#', 
        array('title' => __d('view', 'Layouts.default.filters.store.Title'), 'class' => 'btn btn1')); ?>    
    </div>
*/?>    
    <div class="clear"></div>
    <div id="product-list">
        <?php foreach ($products as $key => $value) print $this->element('products' . DS . 'view', array('product' => $value)); ?>  
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
		$.get("'. Router::url(array('controller' => 'products', 'action' => $this->action, 'filter' => $filter, 'search'=>(isset($this->request->data['Search']['search']) ? $this->request->data['Search']['search'] : null), 'category' => (isset($this->request->params['named']['category']) ? $this->request->params['named']['category'] : null), '?'=>array('vtype'=>(isset($this->request->query['vtype']) && $this->request->query['vtype'] == 'store' ? 'store' : 'product')))) . '", { last_row: $("#product-list > div.product").last().find("span.row_number").text() }, function(data){
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

									$.get("'. Router::url(array('controller' => 'products', 'action' => $this->action, 'filter' => $filter, 'search'=>(isset($this->request->data['Search']['search']) ? $this->request->data['Search']['search'] : null), 'category' => (isset($this->request->params['named']['category']) ? $this->request->params['named']['category'] : null))) . '", { offset: $("#product-list > div.product").length }, function(data){
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

<?php else : ?>
		<?php foreach ($products as $key => $value) print $this->element('products' . DS . 'view', array('product' => $value)); ?>
<?php endif; ?>