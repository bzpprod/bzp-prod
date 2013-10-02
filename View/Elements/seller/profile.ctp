<?php 
		$store_follow = $this->requestAction(array('controller' => 'stores', 'action' => 'following', 'store' => $store['Store']['slug'], 'admin'=> false));
		if($store_follow == true){ $start_follow = "unfollow"; }else{ $start_follow = "follow"; }
		$follow['unfollow']['action'] = "unfollow";
		$follow['unfollow']['text'] = __d('view','Layouts.default.controls.unfollow');
		$follow['unfollow']['link'] = Router::url(array('controller' => 'stores', 'action' => 'unfollow', 'store' => $store['Store']['slug']));
		$follow['unfollow']['value'] = __d('view','Layouts.default.controls.unfollow');
				
		$follow['follow']['action'] = "follow";
		$follow['follow']['text'] = __d('view','Layouts.default.controls.follow');
		$follow['follow']['link'] = Router::url(array('controller' => 'stores', 'action' => 'follow', 'store' => $store['Store']['slug']));				
		$follow['follow']['value'] = __d('view','Layouts.default.controls.follow');
		$store_c = "1"; 

		$follow['unfollow']['link'] = str_replace('/fp','',$follow['unfollow']['link']);
		 $follow['follow']['link']  = str_replace('/fp','',$follow['follow']['link']);

	// #### COMBO STORES
	$stores = $this->requestAction(array('controller' => 'stores', 'action' => 'index', 'api' => true));
	
?>

<?php if ($this->params->prefix == "admin" && $this->params->controller != "purchases"){ ?>
<div id="stores-select" class="title-font">
    <div class="title"><?php echo __d('view', 'Stores.fb_admin_view.selectyourstore.Title'); ?></div>

	<?php foreach ($stores as $key => $value){ 
			$thumb_store = $value['Store']['pictureUrl'];	?>
	    <a <?php if (isset($this->params->store) && $this->params->store == $value['Store']['slug']) { echo 'class="selected"'; } ?>" href="/my-store/<?php echo $value['Store']['slug']; ?>" 
        title="<?php echo $value['Store']['title']; ?>">
		<?php echo $this->Html->image($thumb_store, array('alt' => $value['Store']['title'], 'width' => 20, 'height' => 20)); ?>&nbsp;<?php echo $value['Store']['title']; ?></a>
   <?php } ?>

   <div class="clear"></div>
</div>
<?php } ?>

<div id="store-header">
	<?php if (!$store['Store']['is_personal']):?>
	<div id="store-premium">
	    <div class="opacity"></div>
	    <div class="store-img"><img id="coverphoto" src="#" alt="<?php echo $store['Store']['title']; ?>"/></div>
	    <script type="text/javascript">
			<?php if ($store['Banner']['id'] !== null): ?>
		    	$(".store-img IMG").attr("src","<?php echo ($store['Banner']['is_external'] == 1 ? Configure::read('Amazon.S3.public_path') . DS : DS) . $store['Banner']['dir'] . DS . $store['Banner']['filename']; ?>");
			<?php elseif (isset($store['FacebookPage'])):?>
			    $.getJSON("https://graph.facebook.com/<?php echo $store['FacebookPage']['fb_page_id']?>/","fields=cover&access_token=<?php echo $fb_access_token?>", 
				function(a,b,c){
	    			$(".store-img IMG").attr("src",a.cover.source);
			    	onpageload=function(){FB.Canvas.setSize()};
		    	}, function(a,b,c){ $('#store-premium').remove(); $('#store-header .spacer').remove(); })
			<?php endif;?>
		</script>    
    </div>

	<div class="spacer"></div>
	<?php endif;?>
        <div id="store-header-links">
	<?php if ($store['Store']['is_personal'] == 1 && $store['FacebookPage']['link'] !== null) {?>
		    <a href="<?php echo $store['FacebookPage']['link']?>" target="_blank" rel="nofollow"  class="facebook ico">Facebook profile</a>
    <?php } ?>        
        </div>
    <div id="store-header-img">
		<?php
            if(!empty($store['User']['FacebookUser']['fb_user_id'])){	
                echo $this->Html->link(
                    	$this->Html->image('https://graph.facebook.com/'.$store['User']['FacebookUser']['fb_user_id'].'/picture?type=large&access_token='.$fb_access_token, 
					array('alt' =>  $store['User']['name'], 'style' => 'float:left;')),
                    array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $store['Store']['slug']) , 
                    array('escape' => false, 'title' => $store['User']['name'])
                );
                    
            }else{	
                echo $this->Html->link(				
                        $this->Html->image('https://graph.facebook.com/'.$store['FacebookPage']['fb_page_id'].'/picture?type=large&access_token='.$fb_access_token, 
					array('alt' =>  $store['User']['name'], 'style' => 'float:left;')),
                    '/fp/'.$store['Store']['slug'],
                    array('escape' => false, 'title' => $store['Store']['title'])
                ); 		
            }
        ?>
    </div>
    
    <h2>
		<?php 
            if(!empty($store['User']['FacebookUser']['fb_user_id'])){	
                echo $this->Html->link( $store['Store']['title'],
                    array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $store['Store']['slug']) , 
                    array('escape' => false, 'title' => $store['Store']['title'])
                );
    
            }else{
                echo $this->Html->link( $store['Store']['title'],
                    '/fp/'.$store['Store']['slug'],
                    array('escape' => false, 'title' => $store['Store']['title'])
                );
            }
        ?>
    </h2>
    
        <div id="store-header-follow">
			<?php /*echo $this->Html->link($follow[$start_follow]['text'], $follow[$start_follow]['link'], 
				array('title' => $follow[$start_follow]['text'], 'id' => $follow[$start_follow]['action'], 
				'class' => 'lkn btn btn2 '.$follow[$start_follow]['action'], 'escape' => false)); */?>
		<!-- 
		<input type='button' class='nicebutton button2 <?php echo $follow[$start_follow]['action']?>' onclick="<?php echo $follow[$start_follow]['link']?>" value="<?php echo $follow[$start_follow]['text']?>">  
		 -->
        <div class='buttonWrapper follow'>
        		<a class="btn btn2 btnFollow <?php echo $follow[$start_follow]['action']?>  <?php if ($store_follow == true):?>selected<?php endif;?>" href="<?php echo $follow[$start_follow]['link']?>" rel="nofollow"><span><?php echo $follow[$start_follow]['text']?></span></a>
        </div>



		&nbsp;
        </div>

    <div id="store-header-info">
        <ul>
            <li class="follow tooltip" title="<?php echo __d('view','Stores.profile.followers.title'); ?>"><?php echo $store['Store']['totalFollowers']; ?></li>
            <li class="positive tooltip" title="<?php echo __d('view','Stores.profile.positive_qualified.title'); ?>">
				<?php echo $this->Html->link($store['Store']['qualification_positive'],
                                array('controller' => 'stores_qualifications', 'action' => 'index', 'admin' => false, 'store' => $store['Store']['slug']),
                                array('escape' => false, 'class' => 'tooltip', 'title' => __d('view','Stores.profile.positive_qualified.title'), 'rel' => 'nofollow')
                    ); ?></li>
            <li class="negative tooltip" title="<?php echo __d('view','Stores.profile.negative_qualified.title'); ?>">
				<?php echo $this->Html->link($store['Store']['qualification_negative'],
                                array('controller' => 'stores_qualifications', 'action' => 'index', 'admin' => false, 'store' => $store['Store']['slug']),					
                                array('escape' => false, 'class' => 'tooltip', 'title' => __d('view','Stores.profile.negative_qualified.title'), 'rel' => 'nofollow')
                     ); ?></li>
            <li class="comments tooltip" title="<?php echo __d('view','Stores.profile.comments.title'); ?>">
				<?php echo $this->Html->link($store['Store']['recommendations'],
                                array('controller' => 'stores_qualifications', 'action' => 'index', 'admin' => false, 'store' => $store['Store']['slug']),					
                                array('escape' => false, 'class' => 'tooltip', 'title' => __d('view','Stores.profile.comments.title'))
                     ); ?></li>

        </ul>

    </div>
    <div class="vline"></div>    
    <div class="share">
		 <?php /* echo $this->Facebook->like(array('href' => Router::url(
            array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $store['Store']['slug'])			
            , true), 'send' => false, 'show_faces' => false, 'layout' => 'button_count', 'width' => 75)); */ ?>

		<?php if(!empty($store['PaypalAccount']['email'])){ ?>
            <div class="paypal"></div>
        <?php } ?>
        
    </div>
    <div class="clear"></div>
</div>

<?php

$mystoreLink = Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $store['Store']['slug']), true);
echo $this->Html->scriptBlock('
	var store_hash = "'.$mystoreLink.'";
	var store_slug = "'.Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $store['Store']['slug']), true).'"; 
	$("a.sharefriends").click(function(e){	
		e.preventDefault();	
		FB.ui({
			 method: "feed",
			 link: "'.Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $store['Store']['slug']), true).'",
			 picture: "'.Router::url('/img/logo.jpg',true).'",
			 name: "'.addslashes($store['Store']['title'].__d('view','Sharefriends.header.name')).'",
			 description: "'.addslashes(__d('view','Invite.header.description')).'"
		});			
	});
	
	$("a.invitefriends").live("click",function(e){	
		e.preventDefault();	
		FB.ui({
		  	method: "apprequests",
          	message: "'.addslashes(sprintf(__d('view','Invite.apprequests.name'), $logged_user['User']['name'])).'",
			link: "'.Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $store['Store']['slug']), true).'",
			picture: "'.Router::url('/img/logo.jpg',true).'",
		});			
	});		
');	
