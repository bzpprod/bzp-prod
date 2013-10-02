<!-- #TOP -->
<div id="top"><a name="i"></a>

        <div id="menu">

            <div class="mn menu-account">
                <span class="total counter"></span>
                <?php echo $this->Html->link(__d('view', 'Layouts.default.menu.my_account.Title'), '#', 
                array('escape' => false, 'title' => __d('view', 'Layouts.default.menu.my_account.Title'))); ?>
                
                <div style="display:none;" class="dropdown">
                    <ul>
                        <li class="i5"><span style="display:none" class="counter"></span><?php echo $this->Html->link(__d('view', 
						'Layouts.default.menu.my_ads.Title'), Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => true)),
                            array('escape' => false, 'target' => '_parent', 'title' => __d('view', 'Layouts.default.menu.my_ads.Title'))); ?><br class="clear" /></li>
        
                        <li class="i1"><span style="display:none" class="counter"></span><?php echo $this->Html->link(__d('view', 
						'Layouts.default.menu.my_store.Title'), Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => true)),
                            array('escape' => false, 'target' => '_parent', 'title' => __d('view', 'Layouts.default.menu.my_store.Title'), 
                            'onclick'=>'BZ.notificationCounter(\'rm\',1,\'#menu .menu-account\')')); ?><br class="clear" /></li>
        
                        <li class="i2"><span style="display:none" class="counter"></span><?php echo $this->Html->link(__d('view', 
						'Layouts.default.menu.my_transactions.Title'), Router::url(array('controller' => 'sales', 'action' => 'index', 'admin' => true)),
                            array('escape' => false, 'target' => '_parent', 'title' => __d('view', 'Layouts.default.menu.my_transactions.Title'), 
                            'onclick'=>'BZ.notificationCounter(\'rm\',2,\'#menu .menu-account\')')); ?><br class="clear" /></li>
        
                        <li class="i3"><span style="display:none" class="counter"></span><?php echo $this->Html->link(__d('view', 
						'Layouts.default.menu.my_invites.Title'), Router::url(array('controller' => 'referrals', 'action' => 'index', 'admin' => true)),
                            array('escape' => false, 'target' => '_parent', 'title' => __d('view', 'Layouts.default.menu.my_invites.Title'), 
                            'onclick'=>'BZ.notificationCounter(\'rm\',3,\'#menu .menu-account\')')); ?><br class="clear" /></li>
                        <?php /*
                        <li class="i4" style="display:none">
                            <span style="display:none" class="counter"></span><?php echo $this->Html->link(__d('view', 'Layouts.default.menu.my_feeds.Title'), '#', 
                            array('escape' => false, 'title' => __d('view', 'Layouts.default.menu.my_feeds.Title'), 'onclick'=>'BZ.notificationCounter(\'rm\',4,\'#menu .menu-account\');function(e){e.preventDefault();document.location=this.href};')); ?><br class="clear" /></li>
                        */ ?>
                    </ul>                                                            
                </div>            
            </div>

            <br class="clear" />
        </div>


	<div class="center">

		<div class="l">
	        <div class="logo">
				<h1><?php echo $this->Html->link('Bazzapp', Configure::read('Facebook.redirect'), 
				array('target' => '_parent', 'title' => __d('view', 'Layouts.fb_default.bazzapp'))).__d('view', 'Layouts.fb_default.bazzapp'); ?></h1>
			</div>

		</div>

		<div class="r">
			<div id="top-search-bar">
				<!-- 
                <div class="top-search">
						<?php echo $this->Form->input('Search.search', array('type' => 'text', 'label' => false, 'div'=> false, 
						'class' => 'search searchSiteValue search-field', 'title' => 'Buscar no Bazzapp', 'value' => 'Buscar no Bazzapp' )); ?>
						<?php echo $this->Form->submit('',array('div'=> false, 'class' => 'btnsearch btnsearchSite')); ?>	
				</div>
				<br class="clear" />
                -->
			</div>
			<br class="clear" />        
		</div>
		
    </div>
</div>
<!-- END#TOP -->

<?php 
// print_r($store);
