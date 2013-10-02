<?php
$this->set('body_class', 'home'); 
if(!empty($this->params['named']['sort'])){ $sort = $this->params['named']['sort']; }else{ $sort = ""; }
if(!empty($this->params['named']['dir'])){ $dir = $this->params['named']['dir']; }else{ $dir = ""; }
// $named_search = (!empty($this->request->params['named']['search']) ? $this->request->params['named']['search'] : '');
if(!empty($this->request->params['named']['search'])){ $named_search = $this->request->params['named']['search']; }else{ $named_search = "Buscar na loja"; }

// CATEGORIES
$categories = $this->requestAction(array('controller' => 'categories', 'action' => 'index', 'api' => true), array('named' => array('direct_children' => true, 'has_products' => true), 'pass' => array($store['Store']['slug'])));
$form_categories = array();
foreach ($categories as $key => $value) $form_categories[$value['Category']['id']] = $value['Category']['title'];

?>

<div class="products-view">
	<div class="products-view-controls">
		<div class="l">

			<div id="store-search-bar" class="l">
				<div class="store-search">
						<?php echo $this->Form->create(null,  array('url' => array('controller'=>'products', 'action'=>'index', 
						'store' => $store['Store']['slug'], 'fanpage' => true))); ?>
						<?php echo $this->Form->input('Search.search', array('type' => 'text', 'label' => false, 'div'=> false, 'class' => 'search search-field', 'value'=>
						$named_search, 'title' => 'Buscar na loja')); ?>
						<?php echo $this->Form->submit('',array('div'=> false, 'class' => 'btnsearch')); ?>	
					<?php echo $this->Form->end();?>			
				</div>	    			
				<br class="clear" />            
			</div>			

			<div class="l store-search-filter">
				<span>Ver</span>
				<?php echo $this->Form->input('Category.id', array('type' => 'select', 'label' => false, 'div' => false, 
				'options' => $form_categories, 'empty' => 'Selecione')); ?>
			</div>
			
		</div>
		
		<div class="r">
			<ul class="filter">
				<li class="filter2<?php if(($sort == "created") || ($sort == "")){ echo " selected"; } ?>"><?php echo $this->Html->link(__d('view', 'Home.filterlist.created'), Router::url(array('controller' => 'products', 'action' => 'index', 'fanpage' => true, 'store' => $store['Store']['slug']))); ?></li>

				<li class="filter1<?php if($sort == "likes"){ echo " selected"; } ?>"><?php echo $this->Html->link(__d('view', 'Home.filterlist.friends-like'), 
					Router::url(array('controller' => 'products', 'action' => 'index', 'sort' => 'likes', 'fanpage' => true, 'store' => $store['Store']['slug']))); ?></li>

				<li class="filter4<?php if(($sort == "price") && ($dir == "desc")){ echo " selected"; } ?>">
					<?php echo $this->Html->link(__d('view', 'Home.filterlist.expensive'),
					Router::url(array('controller' => 'products', 'action' => 'index', 'sort' => 'price', 'dir' => 'desc',
					'fanpage' => true, 'store' => $store['Store']['slug']))); ?></li>

				<li class="filter5<?php if(($sort == "price") && ($dir == "asc")){ echo " selected"; } ?>">
					<?php echo $this->Html->link(__d('view', 'Home.filterlist.cheap'), 
					Router::url(array('controller' => 'products', 'action' => 'index', 'sort' => 'price', 'dir' => 'asc',
					'fanpage' => true, 'store' => $store['Store']['slug']))); ?></li>
			</ul>
		</div>

	<br class="clear" />    		
	</div>	

	<div id="product-list">
			<?php foreach ($products as $key => $value) print $this->element('fanpage' . DS . 'products' . DS . 'view', array('product' => $value)); ?>  
	</div>
	
    <div class="clear"></div>
	<?php
	$this->Paginator->options(array('url' => array('store' => $store['Store']['slug'])));		
	echo $this->element('paginator');
	?>
	<div class="clear"></div>
</div>