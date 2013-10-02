<?php
$this->set('body_class', 'edit product_edit');
$this->Html->addCrumb(__d('view', 'Layouts.fb_default.editar'), array('controller' => 'products', 'action' => 'edit', 'admin' => 'true', 'product' => $product['Product']['hash']));

print $this->element('breadcrumb');

$categories = $this->requestAction(array('controller' => 'categories', 'action' => 'index', 'api' => true), array('named' => array('direct_children' => true)));
$form_categories = array();

foreach ($categories as $key => $value) $form_categories[$value['Category']['id']] = $value['Category']['title'];
?>
<fieldset>
	<?php echo $this->Form->create('Product', array('type' => 'file')); ?>
		<?php echo $this->Form->hidden('Product.category_id'); ?>

	<div style="float:left; width:280px;">
        <h2 style="margin-left:15px; text-align:left; margin-top:5px;"><?php echo __d('view', 'Products.admin_edit.form.title'); ?></h2>
        
        <ul>
            <li style="margin-left:85px;">
				<?php 
					$countPhotos = count($product['Product']['Photo']);
						for($i=0;$countPhotos>$i;$i++){
							if(!empty($product['Product']['Photo'][$i]['is_external'])){ $url_photo = Configure::read('Amazon.S3.public_path')."/"; }else{ $url_photo = "/"; }						
						 ?>
						<div class="Photo" id="<?php echo $product['Product']['Photo'][$i]['filename']; ?>">
						<?php echo $this->Html->image($url_photo.$product['Product']['Photo'][$i]['dir'].'/thumb/small/'.$product['Product']['Photo'][$i]['filename']); ?>
							<div><a href="<?php echo Router::url(array('controller' => 'products_photos', 'action' => 'delete', 'admin' => true, 'product' => $product['StoreProduct']['slug'], 'store' => $product['Store']['slug'], 'photo' => $product['Product']['Photo'][$i]['hash'])); ?>" title="Excluir" class="delete" id="delete_<?php echo $product['Product']['Photo'][$i]['filename'];?>">Excluir</a></div>
						</div>
				<?php echo "<br/><br/>";
					}
				?>	
			</li>
            
            <?php if($countPhotos<4){ ?>
            <li style="margin-left:15px;">
                <a class="add-more-photo" href="#" title="Adicionar mais fotos" style="color:#1296D4; text-decoration:underline;">Adicionar mais fotos</a>
                <div style="display:none; margin-top:2px;">
					<?php for($i=$countPhotos;$i<4;$i++){ ?>
                        <p><?php echo $this->Form->input('Photo.'.$i.'.filename', 
                            array('type' => 'file', 'label' => 'Foto '.$i+1)); ?></p>
                            
                    <?php } ?>    
                </div>            
			</li>
			<?php } ?>            
            
		</ul>            
	</div>

    
	<div class="r" style="float:right;">
        <h3 style="margin-top:10px";><?php echo __d('view', 'Products.admin_add.form.title2'); ?></h3>
		<ul>
		<?php /* if (!$fb_manage_pages) : ?>

			<li class="banner-fanpage-add">
				<?php 
					echo $this->Html->link(
							$this->Html->image('fanpage/btn_add.jpg', array(
							'alt' => 'Clique aqui e cadastre agora mesmo sua Fanpage no Bazzapp')),
							'#', array('escape' => false, 'title' =>  'Clique aqui e cadastre agora mesmo sua Fanpage no Bazzapp')
					); 
				?>	
			</li>

		<?php else :*/ ?>
	
			<?php if (!empty($stores_waiting_install)) : ?>
				<li class="banner-fanpage-select">
					<div class="label"><?php echo $this->Html->image('fanpage/combo_select.jpg', array('alt' => 'Cadastre sua Fanpage')); ?></div>
					<div id="fanpage-combo" style="display:none;">
	
						<?php foreach ($stores_waiting_install as $key => $value) : ?>
							<p class="<?php echo $value['Store']['hash']; ?>">
								<input type="checkbox" value="<?php echo $value['Store']['hash']; ?>" /> 
							   	<?php echo $this->Html->image('https://graph.facebook.com/' . $value['FacebookPage']['fb_page_id'] . '/picture?access_toke=' . $fb_access_token, array('alt' => $value['Store']['title'], 'width' => 20, 'height' => 20)); ?>
							   	<?php echo $value['Store']['title']; ?>
							</p>
						<?php endforeach; ?>
						
						<div class="btner buttonWrapper">
			        		<a class="spriteButton type1 accepted" href="#" rel="nofollow" style="width:97%"><span>Cadastrar as marcadas</span></a>
						</div>
					</div>
					<div class="label2" style="display:none">Escolha abaixo a página que gostaria de anunciar:</div>
				</li>
			<?php /* endif; */ ?>
			
			<li id="fanpage-selected">
				<ul>
					<?php foreach ($stores as $key => $value) : ?>
						<li class="<?php echo $value['Store']['hash']; ?>">
							<input type="checkbox" name="data[Store][]" class="<?php echo $value['Store']['hash']; ?>" value="<?php echo $value['Store']['id']; ?>" />
							<?php
							if (!empty($value['FacebookPage']['id']))
							{
								echo $this->Html->image('https://graph.facebook.com/' . $value['FacebookPage']['fb_page_id'] . '/picture?access_toke=' . $fb_access_token, array('alt' => $value['Store']['title'], 'width' => 20, 'height' => 20));
							}
							else
							{
								echo $this->Html->image('fanpage/icon-fanpage.jpg', array('alt' => $value['Store']['title'], 'width' => 20, 'height' => 20));
							}
							?>
							<?php echo $value['Store']['title']; ?>
						</li>
					<?php endforeach; ?>					
				</ul>
			</li>
			
		<?php endif; ?>
		
        	<!--
			<?php // if ($countPhotos <= 6):?>
			<li class="l"><?php echo $this->Form->input('Photo..filename', array('type' => 'file', 'multiple', 
			'label' => __d('view', 'Products.admin_add.form.field_photo.title'))); ?> <span><?php echo __d('view', 
			'Products.fb_admin_add.form.filemax', ini_get('upload_max_filesize')); ?></span></li>
			<?php // endif; ?>
            -->
            
			<li><?php echo $this->Form->input('Product.title', array('type' => 'text', 'label' => __d('view', 'Products.admin_add.form.field_title.title'))); ?></li>
    		<li><?php echo $this->Form->input('Product.price', array('type' => 'text', 'label' => __d('view', 'Products.admin_add.form.field_price.title'), 'class' => 'maskmoney')); ?></li>        
			<li class="l"><?php echo $this->Form->input('Product.quantity', array('type' => 'text', 'label' => __d('view', 'Products.admin_add.form.field_quantity.title'), 'class' => 'masknumber', 'style' => 'width:107px;')); ?></li>
			<li class="l"><div style="margin-top:-1px;"><?php echo $this->Form->input('Product.condition', array('type' => 'select', 'class' => 'required', 'label' => __d('view', 'Products.admin_add.form.field_condition.title'), 'options' => array('new' => __d('view', 'Products.admin_add.form.field_condition.new.title'), 'used' => __d('view', 'Products.admin_add.form.field_condition.used.title')))); ?></div>
			
            <!--
			<li class="l"><?php echo $this->Form->input('Product.exchangeable', array('type' => 'checkbox', 'label' => __d('view', 'Products.admin_add.form.field_exchangeable.title'))); ?></li>
			-->
            
			<li><?php echo $this->Form->input('Category.id', array('type' => 'select', 'label' => __d('view', 'Products.admin_add.form.field_category.title'), 'options' => $form_categories, 'empty' => ' ')); ?></li>
			<li class="l"><?php echo $this->Form->input('Product.category_id', array('type' => 'select', 'label' => __d('view', 'Products.admin_add.form.field_subcategory.title'))); ?></li>

			<li><?php echo $this->Form->input('Product.description', array('type' => 'textarea', 'label' => __d('view', 'Products.admin_add.form.field_description.title'))); ?></li>
			<li class="l" style="margin-left:0;"><?php echo $this->Form->input('Product.publish', array('type' => 'checkbox', 'label' => 'Publicar o anúncio do produto em meu perfil do facebook.', 'checked' => true)); ?></li>
			<li><?php echo $this->Form->submit(__d('view', 'Products.admin_edit.form.submit'), array('class'=> 'btn btn3', 'onclick'=>'BZ.showLoadingScreen("'.__d('view','Products.admin_edit.form.loading').'")')); ?></li>			
		</ul>
	<?php echo $this->Form->end(); ?>
    
	</div>    
    
</fieldset>

<?php
echo $this->Html->scriptBlock('
	linkInstFanpage = "'.Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => false), true).'";
	
	$(".banner-fanpage-select #fanpage-combo a.accepted").click(
		function(e)
		{
			e.preventDefault();
			
			var that = $(this);
			var selectedStores = new Array();
			
			$(".banner-fanpage-select #fanpage-combo input[type=checkbox]:checked").each(
				function()
				{
					selectedStores.push($(this).val());
				}
			);
			
			if (selectedStores.length > 0)
			{
				that.hide();
			
				$.getJSON("' . Router::url(array('controller' => 'stores', 'action' => 'install', 'api' => true, 'ext' => 'json')) . '", { store: selectedStores.join(",") },
					function(data)
					{
						that.show();
						$(".banner-fanpage-select #fanpage-combo").hide();
						
						if (data.stores.length)
						{
							$.each(data.stores,
								function(key, value)
								{
									$("#fanpage-combo p").filter("." + value.Store.hash).remove();
									
									if (!$("#fanpage-selected > ul > li").filter("." + value.Store.hash).length)
									{
										var template;

										if (value.FacebookPage.id)
										{
											template = $("#storeFacebookPageTemplate").html();
											template = template.replace(/{fb_page_id}/g, value.FacebookPage.fb_page_id);
										}
										else
										{
											template = $("#storeTemplate").html();
										}
										
										template = template.replace(/{id}/g, value.Store.id);
										template = template.replace(/{hash}/g, value.Store.hash);
										template = template.replace(/{title}/g, value.Store.title);
										
										$("#fanpage-selected > ul").append(template);
	
									}
								}
							);
							
							$("#fanpage-selected > ul input:checkbox").uniform();
							
							if (!$("#fanpage-combo p").length)
							{
								$(".banner-fanpage-select").hide();
							}
						}
					}
				);
			}
			
			
			/*
			$(".banner-fanpage-select #fanpage-combo input[type=checkbox]:checked").each(
				function()
				{
					var id = $(this).attr("id");
					var linkz = linkInstFanpage+id;			
		
					$(".banner-fanpage-select > .label2").show();
					
					// $.get(linkz, function(data){
					// if ($.trim(data)){
					$("#fanpage-combo > p."+id).hide();
					// $("#fanpage-selected input."+id).attr("checked", true);
					// $("#fanpage-selected input."+id).removeAttr("disabled");
					$("#fanpage-selected > ul > li."+id).show();
					$.uniform.update();
	
					// $("#fanpage-selected > ul > li").append(data);					
					// }
					// });
				}
			);
			*/
			
			
		}
	);
	
	/*
	$("#fanpage-selected input").click(function(e) {
	e.preventDefault();
	id = $(this).attr("class");
	// $("#fanpage-combo > p."+id).show();
	// $(".banner-fanpage-select #fanpage-combo input."+id).removeAttr("checked");
	$("#fanpage-selected > ul > li."+id).hide();
	$("#fanpage-selected input."+id).attr("disabled", true);		
	$.uniform.update();
	});
	*/
	
	$(".banner-fanpage-select div:first").click(
		function()
		{
			$(this).next("div").toggle();
			$(this).parent().toggleClass("selected");
		}
	);
	
	$(document).bind("click",
		function(e)
		{
			var $clicked = $(e.target);
			
			if (!$clicked.parents().hasClass("banner-fanpage-select"))
			{
				$("#fanpage-combo").hide(); 
				$("#fanpage-combo").removeClass("selected");
			}
		}
	);
	
	
	
	var subcategory = "' . $this->request->data['Product']['category_id'] . '";

	$(function()
	{	
		$("select#CategoryId").change(
			function()
			{
				if ($(this).val())
				{
					$("select#ProductCategoryId").empty().attr("disabled", "disabled").append($("<option></option>").text("Carregando…"));
					$.uniform.update();
					
					$.getJSON("' . Router::url(array('controller' => 'categories', 'action' => 'index', 'api' => true)) . '", { category: $(this).children("option:selected").attr("value"), direct_children: true },
						function(data)
						{
							$("select#ProductCategoryId").empty().removeAttr("disabled").append($("<option></option>"));
							
							for (var i = 0; i < data.categories.length; i++)
							{
								var option = $("<option></option>").attr("value", data.categories[i].Category.id).text(data.categories[i].Category.title);
								if (subcategory && data.categories[i].Category.id == subcategory) option.attr("selected", "selected");
								
								$("select#ProductCategoryId").append(option);							
													
							}
							
							subcategory = "";							
							$.uniform.update();
						}
					);
				}
			}
		).change();
		
		$("li.banner-fanpage-add a").click(
			function(e)
			{
				e.preventDefault();
				
				FB.login(
					function(response)
					{
						FB.api(
						{
							method: "fql.query",
							query: "SELECT manage_pages FROM permissions WHERE uid = me()"
						},
							function(response)
							{
								if (response[0].manage_pages == 1)
								{
									location.reload(true);
								}
							}
						);
					},
					{ scope: "manage_pages" }
				);
			}
		);
	});		
');
?>
<script id="storeTemplate" type="text/html">
    <li class="{hash}">
		<input type="checkbox" name="data[Store][]" class="{hash}" value="{id}" />
		<?php echo $this->Html->image('fanpage/icon-fanpage.jpg', array('alt' => '{title}', 'width' => 20, 'height' => 20)); ?>
		{title}
	</li>
</script>

<script id="storeFacebookPageTemplate" type="text/html">
    <li class="{hash}">
		<input type="checkbox" name="data[Store][]" class="{hash}" value="{id}" />
		<?php echo $this->Html->image('https://graph.facebook.com/{fb_page_id}/picture?access_toke=' . $fb_access_token, array('alt' => '{title}', 'width' => 20, 'height' => 20)); ?>
		{title}
	</li>
</script>