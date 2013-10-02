<?php
	// METAS
	$this->set('title_for_layout', sprintf(__d('view','Layouts.default.meta.store_view.title'), $store['Store']['title']));
	$this->set('meta_description', sprintf(__d('view','Layouts.default.meta.store_view.description'), $store['Store']['title'], $store['Address']['city'],
	$store['Store']['description']));
	
	$this->set('meta_keywords', sprintf(__d('view','Layouts.default.meta.store_view.keywords'), $store['Store']['title']));
	
	// ### BREADCRUMB
	$this->Html->addCrumb($store['User']['name'], array('controller' => 'stores', 'action' => 'view', 'store' => $store['Store']['hash']));

	$this->set('fbmeta_url', Router::url(array('controller' => 'stores', 'action' => 'view', 'store' => $store['Store']['slug']), true));
	$this->set('fbmeta_type', Configure::read('Facebook.Namespace').':store');	
	$this->set('fbmeta_title', addslashes(__d('view', 'Layouts.fb_default.bazzapp').' | '.$store['Store']['title']));	
	$this->set('fbmeta_image', Router::url('https://graph.facebook.com/' . $store['User']['FacebookUser']['fb_user_id'] . '/picture', true));
	$this->set('fbmeta_image_width','200');
	$this->set('fbmeta_image_height','200');
	$this->set('fbmeta_description', addslashes($store['Store']['description']));

?>
<div id="store-filters">
    <span><?php echo __d('view', 'Layouts.default.filters.Title'); ?></span>
    <?php echo $this->Html->link(__d('view', 'Layouts.default.filters.created.Title'), 
    array('controller' => 'stores', 'action' => 'view', 'store' => $store['Store']['slug']), array('class'=> (strpos($_SERVER['REQUEST_URI'], '/sort:') == false && strpos($_SERVER['REQUEST_URI'], '/direction:') == false ? 'selected' : ''))); ?>

    <?php echo $this->Html->link(__d('view', 'Layouts.default.filters.friends-like.Title'), 
    array('controller' => 'stores', 'action' => 'view', 'store' => $store['Store']['slug'], 'sort' => 'likes', 'direction' => 'desc'), array( 'class'=> ((strpos($_SERVER['REQUEST_URI'], '/sort:likes') >0 && strpos($_SERVER['REQUEST_URI'], '/direction:') > 0) ? 'selected' : ''))); ?>

    <?php echo $this->Html->link(__d('view', 'Home.filterlist.expensive'), 
    array('controller' => 'stores', 'action' => 'view', 'store' => $store['Store']['slug'], 'sort' => 'price', 'direction' => 'desc'), array( 'class'=> ((strpos($_SERVER['REQUEST_URI'], '/sort:price') > 0 && strpos($_SERVER['REQUEST_URI'], '/direction:') > 0) ? 'selected' : ''))); ?>

    <?php echo $this->Html->link(__d('view', 'Home.filterlist.cheap'), 
    array('controller' => 'stores', 'action' => 'view', 'store' => $store['Store']['slug'], 'sort' => 'price'), array( 'class'=> ((strpos($_SERVER['REQUEST_URI'], '/sort:price') > 0 && strpos($_SERVER['REQUEST_URI'], '/direction:') == false ) ? 'selected' : ''))); ?>
</div>

<div id="store-view-mode">
    <?php $this->Paginator->options(array('url' => array('store' => $store['Store']['slug']))); echo $this->element('paginator'); ?>		
</div>

<div id="product-list">
    <?php foreach ($products as $key => $value){ print $this->element('products' . DS . 'view', array('product' => $value)); } ?>  
</div>

<div class="clear"></div>

<div style="margin-left:19px;">
	<?php echo $this->element('paginator'); ?>
</div>