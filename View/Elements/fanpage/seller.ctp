<?php
	$named_search = (!empty($this->request->params['named']['search']) ? $this->request->params['named']['search'] : '');
	
	// FOLLOW ####
	$store_follow = $this->requestAction(array('controller' => 'stores', 'action' => 'following', 'fanpage' => false, 'store' => $store['Store']['slug']));

	if($store_follow == true){ $start_follow = "unfollow"; }else{ $start_follow = "follow"; }
	$follow['unfollow']['action'] = "unfollow";
	$follow['unfollow']['text'] = __d('view','Stores.unfollow.title');
	$follow['unfollow']['link'] = Router::url(array('controller' => 'stores', 'action' => 'unfollow', 'store' => $store['Store']['slug'], 'fanpage' => false));

	$follow['follow']['action'] = "follow";
	$follow['follow']['text'] = __d('view','Stores.follow.title');
	$follow['follow']['link'] = Router::url(array('controller' => 'stores', 'action' => 'follow', 'store' => $store['Store']['slug'], 'fanpage' => false));				
	$store_c = "1"; 

?>
<h2><?php echo $this->Html->link($store['Store']['title'], $store['FacebookPage']['link'].'&app_data=/fp/'.$store['Store']['slug'], array('title' => $store['Store']['title'], 'target' => '_parent')); ?></h2>

<div id="fp-share-follow" class="share <?php echo $follow[$start_follow]['action'];?>">
		<?php 
			if ($start_follow != 'follow')
			{
				echo $this->Html->link($follow[$start_follow]['text'], 
								       $follow[$start_follow]['link'],
									   array('title' => $follow[$start_follow]['text'], 
													 	   'id' => $follow[$start_follow]['action'], 
													 	   'class' => 'lkn', 
													 	   'escape' => false 
									   )
									);
			}
			else
			{
				echo $this->Html->link($follow[$start_follow]['text'], 
								       '#',
									   array('title' => $follow[$start_follow]['text'], 
													 	   'id' => $follow[$start_follow]['action'], 
													 	   'class' => 'lkn', 
													 	   'escape' => false, 
													 	   'onclick' => 'FB.api(\'/me/'. Configure::read('Facebook.Namespace') . ':follow\', \'post\', { store: \''. Router::url( array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $store['Store']['slug']) , true) .'\'},function(e){if (e.error){console.log(e.error.message);};document.location.href=\''.$follow[$start_follow]['link'].'\';});'
									   )
									);
			}
		?>
</div>

<div class="qualifier-status">
	<div class="icon positive">&nbsp;</div>
	<div class="value"><?php echo $store['Store']['qualification_positive']; ?></div>
	<div class="icon negative">&nbsp;</div>
	<div class="value"><?php echo $store['Store']['qualification_negative']; ?></div>
</div>

<div class="qualify">
	<?php echo $this->Html->link('Qualifica&ccedil;&otilde;es',
		array('controller' => 'stores_qualifications', 'action' => 'index', 'fanpage' => true, 'store' => $store['Store']['slug']), 
		array('escape' => false, 'style' => 'text-decoration: underline', 'title' => 'Qualifica&ccedil;&otilde;es')
	); ?>(+)
</div>

<?php
	if(!empty($store_c)){ 	

		echo $this->Html->scriptBlock('	
			$("#fp-share-follow.share > a").live("click", function(e){
				e.preventDefault();
				var linkz = $(this).attr("href")+".json";
				var actionz = $(this).attr("id");
					obj = this;
					
				if(actionz=="follow"){
					flw_id = "'.$follow['unfollow']['action'].'";
				   flw_txt = "'.$follow['unfollow']['text'].'";
				  flw_link = "'.$follow['unfollow']['link'].'";
				}else{
					flw_id = "'.$follow['follow']['action'].'";
				   flw_txt = "'.$follow['follow']['text'].'";
				  flw_link = "'.$follow['follow']['link'].'";
				}
		
				$.getJSON(linkz, function(data){
					if ($.trim(data)){
						$(obj).attr("id", flw_id);
						$(obj).attr("href", flw_link);
						$(obj).attr("title", flw_txt);
						$(obj).text(flw_txt);
						$(obj).parent().removeClass(actionz);
						$(obj).parent().addClass(flw_id);
					}
				});
		
			});		
		');	
	}
?>