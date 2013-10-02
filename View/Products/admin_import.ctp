<?php 
$this->set('body_class', 'edit import');
$this->Html->addCrumb(__d('view', 'Stores.import_products.Title'), array('controller' => 'products', 'action' => 'import', 'admin' => true));
	
$categories = $this->requestAction(array('controller' => 'categories', 'action' => 'index', 'api' => true), array('named' => array('direct_children' => true)));
$form_categories = array();

foreach ($categories as $key => $value) $form_categories[$value['Category']['id']] = $value['Category']['title'];	
?>
<fieldset>
	<h2><?php echo __d('view', 'Products.fb_admin_import.form.title'); ?></h2>
	<div class="divisor"></div>
	
	<!-- TEMPLATE -->
	<div id="product-template" class="template" style="display: none;">
		<a class="close">&nbsp;</a>
		<div class="l"><div class="image"><img src="" /></div></div>
		<div class="l">
			<div class="fb_photo_id"><?php echo $this->Form->input('Product.N.fb_photo_id', array('type' => 'hidden')); ?></div>
			<ul>
				<li class="l"><?php echo $this->Form->input('Category.N.category_id', array('type' => 'select', 'class' => 'nostyle catcombo', 'label' => __d('view', 'Products.admin_add.form.field_category.title'), 'options' => $form_categories, 'empty' => ' ')); ?></li>
				<li class="l"><?php echo $this->Form->input('Product.N.category_id', array('type' => 'select', 'class' => 'nostyle subcatcombo', 'label' => __d('view', 'Products.admin_add.form.field_subcategory.title'))); ?></li>
				<li class="l"><?php echo $this->Form->input('Product.N.title', array('type' => 'text', 'label' => __d('view', 'Products.admin_add.form.field_title.title'))); ?></li>
				<li class="l"><?php echo $this->Form->input('Product.N.price', array('type' => 'text', 'class' => 'maskmoney', 'label' => __d('view', 'Products.admin_add.form.field_price.title'))); ?></li>
				<li class="l"><?php echo $this->Form->input('Product.N.quantity', array('type' => 'text', 'class' => 'masknumber', 'label' => __d('view', 'Products.admin_add.form.field_quantity.title'))); ?></li>                
				<li class="l"><?php echo $this->Form->input('Product.N.condition', array('type' => 'select', 'class'=>'nostyle', 'label' => __d('view', 'Products.admin_add.form.field_condition.title'), 'options' => array('new' => __d('view', 'Products.admin_add.form.field_condition.new.title'), 'used' => __d('view', 'Products.admin_add.form.field_condition.used.title')))); ?></li>
				<li class="l checkz"><?php echo $this->Form->input('Product.N.exchangeable', array('type' => 'checkbox', 'class' => 'nostyle', 'label' => __d('view', 'Products.admin_add.form.field_exchangeable.title'))); ?></li>
				<li class="l"><?php echo $this->Form->input('Product.N.description', array('type' => 'textarea', 'label' => __d('view', 'Products.admin_add.form.field_description.title'))); ?></li>
			</ul>    
		</div>
		<br class="clear" />
	</div>

	<!-- FORM -->	
	<?php echo $this->Form->create('Product', array('type' => 'file')); ?>	
		<ul>
			<?php if (!$fb_manage_pages) : ?>
	
				<!-- <li class="banner-fanpage-add">
					<?php 
						echo $this->Html->link(
								$this->Html->image('fanpage/btn_add.jpg', array(
								'alt' => 'Clique aqui e cadastre agora mesmo sua Fanpage no Bazzapp')),
								'#', array('escape' => false, 'title' =>  'Clique aqui e cadastre agora mesmo sua Fanpage no Bazzapp')
						); 
					?>	
				</li> -->
				
			<?php else : ?>
				
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
							
							<div class="btn">
								<!--<a class="cancel" href="#" title="Cancelar">Cancelar</a>-->
								<a class="accepted" href="#" title="Confirmar">Confirmar</a>
								<br class="clear" />
							</div>
						</div>
						<div class="label2" style="display:none">Escolha abaixo a página que gostaria de anunciar:</div>
					</li>
				<?php endif; ?>
				
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
		</ul>		
		
		<div id="albums-admin">	
			<ul>
				<li><?php echo $this->Form->input('Facebook.album_id', array('type' => 'select', 'label' => __d('view', 'Products.fb_admin_import.form.selectalbum'), 'options' => $albums, 'empty' => ' ')); ?></li>
			</ul>	
			<br class="clear" />
		</div>		
		
		<div id="products-form" class="form" style="display: none;">
			<div id="products-form-list">
				<?php if (!empty($this->request->data['Product'])) : ?>
					<?php foreach ($this->request->data['Product'] as $key_2 => $value_2) : ?>
						<?php if (is_numeric($key_2)) : ?>
							<div class="template">
								<?php echo $this->Form->hidden('Subcategory.id', array('value' => (!empty($value_2['category_id']) ? $value_2['category_id'] : ''), 'class' => 'selected-subcategory')); ?>
								<div class="l"><div class="image"><img src="https://graph.facebook.com/<?php echo $value_2['fb_photo_id']; ?>/picture?access_token=<?php echo $fb_access_token; ?>" /></div></div>
								<div class="l">
									<div class="fb_photo_id"><?php echo $this->Form->input('Product.' . $key_2 . '.fb_photo_id', array('type' => 'hidden')); ?></div>
									<ul>
										<li class="l"><?php echo $this->Form->input('Category.' . $key_2 . '.category_id', array('type' => 'select', 'class' => 'catcombo', 'label' => __d('view', 'Products.admin_add.form.field_category.title'), 'options' => $form_categories, 'empty' => ' ')); ?></li>
										<li class="l"><?php echo $this->Form->input('Product.' . $key_2 . '.category_id', array('type' => 'select', 'class' => 'subcatcombo', 'label' => __d('view', 'Products.admin_add.form.field_subcategory.title'))); ?></li>
										<li class="l"><?php echo $this->Form->input('Product.' . $key_2 . '.title', array('type' => 'text', 'label' => __d('view', 'Products.admin_add.form.field_title.title'))); ?></li>
										<li class="l"><?php echo $this->Form->input('Product.' . $key_2 . '.price', array('type' => 'text', 'class' => 'maskmoney', 'label' => __d('view', 'Products.admin_add.form.field_price.title'))); ?></li>
										<li class="l"><?php echo $this->Form->input('Product.' . $key_2 . '.quantity', array('type' => 'text', 'class' => 'masknumber', 'label' => __d('view', 'Products.admin_add.form.field_quantity.title'))); ?></li>                
										<li class="l"><?php echo $this->Form->input('Product.' . $key_2 . '.condition', array('type' => 'select', 'label' => __d('view', 'Products.admin_add.form.field_condition.title'), 'options' => array('new' => __d('view', 'Products.admin_add.form.field_condition.new.title'), 'used' => __d('view', 'Products.admin_add.form.field_condition.used.title'), 'refurbished' => __d('view', 'Products.admin_add.form.field_condition.refurbished.title')))); ?></li>
										<li class="l checkz"><?php echo $this->Form->input('Product.' . $key_2 . '.exchangeable', array('type' => 'checkbox', 'class' => 'nostyle', 'label' => __d('view', 'Products.admin_add.form.field_exchangeable.title'))); ?></li>
										<li class="l"><?php echo $this->Form->input('Product.' . $key_2 . '.description', array('type' => 'textarea', 'label' => __d('view', 'Products.admin_add.form.field_description.title'))); ?></li>
									</ul>    
								</div>
								<br class="clear" />
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<?php echo $this->Form->submit(__d('view', 'Products.admin_import.form.submit'), array('class'=> 'btn btn3')); ?>

            <ul>
                <li class="l"><?php echo $this->Form->input('Product.publish', 
                    array('type' => 'checkbox', 'label' => 'Publicar o anúncio do produto em meu perfil do facebook.', 'checked' => true)); ?></li>
            </ul>

		</div>
		
	<?php echo $this->Form->end(); ?>
</fieldset>
<?php echo $this->Html->scriptBlock('
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
	
	
	
$("select").live("change",
	function()
	{
		if ($(this).attr("id") == "FacebookAlbumId")
		{
			$("div#products-form").hide();
			$("div#products-form-list").empty();
			$("#products-form .submit").show();
			
			if ($(this).val())
			{
				$.getJSON("https://graph.facebook.com/" + $("select#FacebookAlbumId").val() + "/photos", { access_token: "' . $fb_access_token . '" },
					function(response)
					{
						if (response.data)
						{
							$("div#products-form").show();
							
							$.each(response.data,
								function(index, data)
								{
									var product = $("div#product-template").clone().removeAttr("id").show();
									
									product.html(product.html().replace(/\[N\]/g, "[" + index + "]").replace(/ProductN/g, "Product" + index).replace(/CategoryN/g, "Category" + index));
									product.find("div.image > img").attr("src", "https://graph.facebook.com/" + data.id + "/picture?access_token=' . $fb_access_token . '");
									product.find("div.fb_photo_id input").val(data.id);	
									product.find("select,input:checkbox").uniform();
									product.find("input.maskmoney").maskMoney({ thousands: "" });
									product.find("input.masknumber").numeric(false);
									product.show();
									
									$("div#products-form-list").append(product);
								 	FB.Canvas.setSize();
								}
							);
						}	
					}
				);
			}
		}
		else if ($(this).hasClass("catcombo"))
		{
			var that = $(this);
			
			if (that.val())
			{
				that.parents("div.template").find("select.subcatcombo").empty().attr("disabled", "disabled").append($("<option></option>").text("Carregando…"));
				$.uniform.update();
				
				$.getJSON("' . Router::url(array('controller' => 'categories', 'action' => 'index', 'api' => true)) . '", { category: that.children("option:selected").attr("value"), direct_children: true },
					function(data)
					{
						that.parents("div.template").find("select.subcatcombo").empty().removeAttr("disabled").append($("<option></option>"));
						$.uniform.update(); 
						
						for (var i = 0; i < data.categories.length; i++)
						{
							var option = $("<option></option>").attr("value", data.categories[i].Category.id).text(data.categories[i].Category.title);
							if (that.parents("div.template").find("input.selected-subcategory").val() && data.categories[i].Category.id == that.parents("div.template").find("input.selected-subcategory").val()) option.attr("selected", "selected");
								
							that.parents("div.template").find("select.subcatcombo").append(option);
						}
						
						that.parents("div.template").find("input.selected-subcategory").val("")
						$.uniform.update();
					}
				);
			}	
		}
	}
);

$(function(){
	$("div#products-form-list select.catcombo").change();
	if ($("select#FacebookAlbumId").val()) $("div#products-form").show();
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
