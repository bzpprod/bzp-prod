<?php
$categories = $this->requestAction(
	array('controller' => 'categories', 'action' => 'index', 'api' => true), 
	array('named'=>array('has_products'=> true, 'direct_children' => true)
));

$named_search = (!empty($this->request->params['named']['search']) ? $this->request->params['named']['search'] : __d('view','Layouts.default.search.Value'));
$subcats = "";
?>
<?php #tag da afilio ?>
<img src="https://secure.afilio.com.br/lead.php?pid=992&order_id=#UNIQUE_ID#" border="0" width="1" height="1" />
<!-- #TOP -->
<div id="top">
	<div id="logo">
    	<h1><?php echo $this->Html->link('','/',array('title' => __d('view', 'Layouts.default.top.logo.Name'))).__d('view', 'Layouts.default.top.logo.Name'); ?></h1>
    </div>

    <div id="menu">
	    <div class="menu-add buttonWrapper">
        		<a class="spriteButton type1 <?php echo ($this->action == 'admin_add' ? 'selected' : '')?>" href="<?php echo Router::url(array('controller' => 'products', 'action' => 'add', 'admin' => true))?>" rel="nofollow"><span><?php echo __d('view', 'Layouts.default.menu.advertise.Title')?></span></a>
		</div>

	    <div class="mn menu-about">
			<?php echo $this->Html->link(__d('view', 'Layouts.default.menu.about.Title'), 
			array('controller' => 'pages', 'action' => 'display', 'how_works', 'admin' => false), array('title' => __d('view', 'Layouts.default.menu.about.Title'))); ?>            
	        <div style="display:none;">
	            <ul>
	            	<?php /*
                    <li><?php echo $this->Html->link(__d('view', 'Layouts.default.menu.press.Title'), 
					array('controller' => 'pages', 'action' => 'display', 'press', 'admin' => false), 
					array('title' => __d('view', 'Layouts.default.menu.press.Title'))); ?></li>
					*/?>
                    <li><?php echo $this->Html->link(__d('view', 'Layouts.default.menu.how_works.Title'), 
					array('controller' => 'pages', 'action' => 'display', 'how_works', 'admin' => false), 
					array('title' => __d('view', 'Layouts.default.menu.how_works.Title'))); ?></li>

					<li><?php echo $this->Html->link(__d('view', 'Layouts.default.menu.terms_use.Title'), 
					array('controller' => 'pages', 'action' => 'display', 'terms_of_use', 'admin' => false), 
					array('title' => __d('view', 'Layouts.default.menu.terms_use.Title'))); ?></li>
					
                    <li><?php echo $this->Html->link(__d('view', 'Layouts.default.menu.privacy_policy.Title'), 
					array('controller' => 'pages', 'action' => 'display', 'privacy_policy', 'admin' => false), 
					array('title' => __d('view', 'Layouts.default.menu.privacy_policy.Title'))); ?></li>

				</ul>                                                            
            </div>
            
        </div>
		<div class="vline"></div>

	    <div class="mn menu-share">
			<?php echo $this->Html->link(__d('view', 'Layouts.default.menu.share.Title'), 
			array('controller' => 'referrals', 'action' => 'index', 'admin' => true),
            array('escape' => false, 'title' => __d('view', 'Referral.shareinviteriends.Title'))); ?></div>
		<div class="vline"></div>

	    <div class="mn menu-account">
	       	<span class="total counter"></span>
			<?php echo $this->Html->link(__d('view', 'Layouts.default.menu.my_account.Title'), array('controller' => 'stores', 'action' => 'edit', 'admin' => true), 
            array('escape' => false, 'title' => __d('view', 'Layouts.default.menu.my_account.Title'))); ?>
            
	        <div style="display:none;" class="dropdown">
	            <ul>
                	<li class="i5"><span style="display:none" class="counter"></span><?php echo $this->Html->link(__d('view', 'Layouts.default.menu.my_ads.Title'),
						array('controller' => 'stores', 'action' => 'view', 'admin' => true),
            			array('escape' => false, 'title' => __d('view', 'Layouts.default.menu.my_ads.Title'))); ?><br class="clear" /></li>

                	<li class="i1"><span style="display:none" class="counter"></span><?php echo $this->Html->link(__d('view', 'Layouts.default.menu.my_store.Title'), 
						array('controller' => 'stores', 'action' => 'edit', 'admin' => true),
            			array('escape' => false, 'title' => __d('view', 'Layouts.default.menu.my_store.Title'), 'onclick'=>'BZ.notificationCounter(\'rm\',1,\'#menu .menu-account\')')); ?><br class="clear" /></li>

                	<li class="i2"><span style="display:none" class="counter"></span><?php echo $this->Html->link(__d('view', 'Layouts.default.menu.my_transactions.Title'),
						array('controller' => 'sales', 'action' => 'index', 'admin' => true),
						array('escape' => false, 'title' => __d('view', 'Layouts.default.menu.my_transactions.Title'), 'onclick'=>'BZ.notificationCounter(\'rm\',2,\'#menu .menu-account\')')); ?><br class="clear" /></li>

                	<li class="i3"><span style="display:none" class="counter"></span><?php echo $this->Html->link(__d('view', 'Layouts.default.menu.my_invites.Title'),
						array('controller' => 'referrals', 'action' => 'index', 'admin' => true),
            			array('escape' => false, 'title' => __d('view', 'Layouts.default.menu.my_invites.Title'), 'onclick'=>'BZ.notificationCounter(\'rm\',3,\'#menu .menu-account\')')); ?><br class="clear" /></li>
					<?php /*
                	<li class="i4" style="display:none">
                    	<span style="display:none" class="counter"></span><?php echo $this->Html->link(__d('view', 'Layouts.default.menu.my_feeds.Title'), '#', 
            			array('escape' => false, 'title' => __d('view', 'Layouts.default.menu.my_feeds.Title'), 'onclick'=>'BZ.notificationCounter(\'rm\',4,\'#menu .menu-account\');function(e){e.preventDefault();document.location=this.href};')); ?><br class="clear" /></li>
                    */ ?>
				</ul>                                                            
            </div>            
		</div>
        
        <!-- 
		<div class="vline"></div>        
	    <div class="mn menu-wishlist">
	       	<span class="total counter"></span>
			<?php echo $this->Html->link('&nbsp;', '#', array('escape' => false, 'title' => __d('view', 'Layouts.default.menu.wishlist.Title'))); ?>
	        <div style="display:none;" class="dropdown">
	            <div class="title"><?php echo __d('view', 'Layouts.default.menu.wishlist.Title'); ?></div>
	            <ul>
                	<li class="prod">
	                    <a href="#"><img src="https://d26lwykep91dnu.cloudfront.net/files/product/photo/thumb/small/50d3beaa-fdb4-423e-b2e2-4d5b0a1c8360.jpg" /></a>    
                        <div class="t1"><a href="" title="Lorem ipsum dollor">Lorem ipsum dollor</a></div>
                        <div class="p1"><a href="#" title="R$xx,xx">R$xx,xx</a></div>
						<?php echo $this->Html->link(__d('view', 'Layouts.default.controls.delete'), '#', 
							array('escape' => false, 'class' => 'delete',  'title' => __d('view', 'Layouts.default.controls.delete'))); ?>                        
                    </li>
                    
	            </ul>            
            </div>
		</div>
        -->

    </div>
    <div class="search box-search">
		<?php echo $this->Form->create(null,  array('url' => array('controller'=>'products', 'action'=>'index', 'admin' => false))); ?>
            <?php echo $this->Form->input('Search.search', array('type' => 'text', 'title' => __d('view','Layouts.default.search.Value'), 'label' => false, 'div'=> false, 'class' => 'query search-field', 'value'=> $named_search)); ?>
            <?php if (isset($this->request->params['named']['category'])) {
            	echo $this->Form->input('Search.category', array('type' => 'hidden',  'div'=> false, 'value'=> $this->request->params['named']['category'] ));
            	 
            }?>
            <?php echo $this->Form->submit(__d('view','Layouts.default.search.Title'), array('div' => false, 'escape' => false, 'class' => 'btn_submit')); ?>
        <?php echo $this->Form->end();?>        
    </div>
	<div class="clear"></div>    
</div>

<div id="subtop" class="navbar">
    <div class="menu-categories">
		<?php echo $this->Html->link(__d('view', 'Layouts.default.menu.categories.Title'), '#', 
        array('title' => __d('view', 'Layouts.default.menu.categories.Title'))); ?>

        <div class="categories">
            <div id="nav_cats">
                <ul>
					<?php foreach ($categories as $key=>$value): ?>
                        <li id="cat_<?php echo $value['Category']['id']; ?>"><?php echo $this->Html->link($value['Category']['title'],
                        array('controller' => 'products', 'action' => 'index', 'admin' => false, 'category' => $value['Category']['slug']),
                        array('title'=> $value['Category']['title'])); ?> </li>

                        <?php 
							$subcats .= '<div id="subcat_'.$value['Category']['id'].'" class="subcat '.(count($value['ChildCategory']) > 0 ? 'notEmpty' : 'empty').'">
										<ul>';
								foreach ($value['ChildCategory'] as $k=>$v){
									$parameters = array('controller' => 'products', 'action' => 'index', 'category' => $v['slug'], 'admin' => false);													
									$subcats .= '<li>'.$this->Html->link($v['title'], $parameters).'</li>';
								}							

							$subcats .= '</ul>
										<div style="display:none"></div>
										</div>';
						endforeach; ?>       

                </ul>
            </div>
        
            <div id="nav_subcats" style="display:none">
				<?php echo $subcats; ?>            
            </div>
        </div>
        
    </div>
    
    <?php if(isset($body_class) && (($body_class=="home") || ($body_class=="home search"))){ ?>
	<div id="filters"> 
	    <span><?php echo __d('view', 'Layouts.default.filters.Title'); ?></span>
		<?php echo $this->Html->link(__d('view', 'Layouts.default.filters.created.Title'), 
        Router::url(array('controller' => 'products', 'action' => $this->action, 'category' => (isset($this->request->params['named']['category']) ? $this->request->params['named']['category'] : null), 'search'=>(isset($this->request->params['named']['search']) ? $this->request->params['named']['search'] : null), '?'=>(array('vtype'=>(isset($this->request->query['vtype']) && $this->request->query['vtype'] == 'store' ? 'store' : 'product'))))), array('class'=> strpos($_SERVER['REQUEST_URI'], '/filter:') == false ? 'selected' : ''  )); ?>

		<?php if (!isset($this->request->query['vtype']) || $this->request->query['vtype'] != 'store'):?>
        <?php echo $this->Html->link(__d('view', 'Layouts.default.filters.friends-like.Title'), 
        Router::url(array('controller' => 'products', 'action' => $this->action, 'filter' => 'likes', 'category' => (isset($this->request->params['named']['category']) ? $this->request->params['named']['category'] : null), 'search'=>(isset($this->request->params['named']['search']) ? $this->request->params['named']['search'] : null), '?'=>(array('vtype'=>(isset($this->request->query['vtype']) && $this->request->query['vtype'] == 'store' ? 'store' : 'product'))))), array('class'=> strpos($_SERVER['REQUEST_URI'], '/filter:likes') != false ? 'selected' : ''  )); ?>
		<?php endif;?>
		
        <?php echo $this->Html->link(__d('view', 'Layouts.default.filters.friends-products.Title'), 
        Router::url(array('controller' => 'products', 'action' => $this->action, 'filter' => 'friends-products', 'category' => (isset($this->request->params['named']['category']) ? $this->request->params['named']['category'] : null), 'search'=>(isset($this->request->params['named']['search']) ? $this->request->params['named']['search'] : null), '?'=>(array('vtype'=>(isset($this->request->query['vtype']) && $this->request->query['vtype'] == 'store' ? 'store' : 'product'))))), array('class'=> strpos($_SERVER['REQUEST_URI'], '/filter:friends-products') != false ? 'selected' : ''  )); ?>

        <?php echo $this->Html->link(__d('view', 'Layouts.default.filters.follow-stores.Title'), 
        Router::url(array('controller' => 'products', 'action' => $this->action, 'filter' => 'follow-stores', 'category' => (isset($this->request->params['named']['category']) ? $this->request->params['named']['category'] : null), 'search'=>(isset($this->request->params['named']['search']) ? $this->request->params['named']['search'] : null), '?'=>(array('vtype'=>(isset($this->request->query['vtype']) && $this->request->query['vtype'] == 'store' ? 'store' : 'product'))))), array('class'=> strpos($_SERVER['REQUEST_URI'], '/filter:follow-stores') != false ? 'selected' : ''  )); ?>
            
        <?php if (!isset($this->request->query['vtype']) || $this->request->query['vtype'] != 'store'):?>
        <?php echo $this->Html->link(__d('view', 'Layouts.default.filters.expensive.Title'), 
        Router::url(array('controller' => 'products', 'action' => $this->action, 'filter' => 'expensive', 'category' => (isset($this->request->params['named']['category']) ? $this->request->params['named']['category'] : null), 'search'=>(isset($this->request->params['named']['search']) ? $this->request->params['named']['search'] : null), '?'=>(array('vtype'=>(isset($this->request->query['vtype']) && $this->request->query['vtype'] == 'store' ? 'store' : 'product'))))), array('class'=> strpos($_SERVER['REQUEST_URI'], '/filter:expensive') != false ? 'selected' : ''  )); ?>

        <?php echo $this->Html->link(__d('view', 'Layouts.default.filters.cheap.Title'), 
        Router::url(array('controller' => 'products', 'action' => $this->action, 'filter' => 'cheaper', 'category' => (isset($this->request->params['named']['category']) ? $this->request->params['named']['category'] : null), 'search'=>(isset($this->request->params['named']['search']) ? $this->request->params['named']['search'] : null), '?'=>(array('vtype'=>(isset($this->request->query['vtype']) && $this->request->query['vtype'] == 'store' ? 'store' : 'product'))))), array('class'=> strpos($_SERVER['REQUEST_URI'], '/filter:cheaper') != false ? 'selected' : ''  )); ?>
        <?php endif;?>
    </div>
    <?php } ?>     
</div>
<div id="subtop-line"></div>
<!-- END#TOP -->