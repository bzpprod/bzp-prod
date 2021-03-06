<?php
$this->set('body_class', 'edit add');
$this->set('body_page', 'product_add');
// METAS
$this->set('title_for_layout', __d('view', 'Layouts.default.meta.sell.title'));
$this->set('meta_description', __d('view', 'Layouts.default.meta.sell.description'));
$this->set('meta_keywords', __d('view', 'Layouts.default.meta.sell.keywords'));

// ### BREADCRUMB
$this->Html->addCrumb(__d('view', 'Layouts.default.menu.advertise.Title'), array('controller' => 'products', 'action' => 'add', 'admin' => true));

if (!($form_categories = apc_fetch('__categories')) || !($subCategories = apc_fetch('__subcategories')))
{

	$categories = $this->requestAction(array('controller' => 'categories', 'action' => 'index', 'api' => true), array('named' => array('direct_children' => true)));

	$form_categories = array();
	$subCategories = array();
	

	foreach ($categories as $key => $value)
	{
		$form_categories[$value['Category']['id']] = $value['Category']['title'];
	
		foreach ($value['ChildCategory'] as $k=>$v)
		{
			$subCategories[$value['Category']['id']][$v['id']] = $v['title'];
		}
	}

	apc_add('__subcategories', $subCategories, 3600);
	apc_add('__categories', $form_categories, 3600);
}
?>
<script>
	var subCategories = <?php echo json_encode($subCategories);?>
</script>

<?php if ($hasPaypal == false):?>
	<?php echo $this->element('products' . DS . 'admin_add_paypalAccount_advertise'); ?>
<?php else: ?>

<fieldset>
	<?php echo $this->Form->create('Product', array('type' => 'file')); ?>
		<?php echo $this->Form->hidden('Product.category_id'); ?>

	<div class="l">
        <h2><?php echo __d('view', 'Products.admin_add.form.title'); ?></h2>
        
        <ul>
	        <li><?php echo $this->Form->input('Photo.0.filename', array('type' => 'file', 'class' => 'ipt_file', 'label' => "Foto Principal")); ?>
            <span><?php echo __d('view', 'Products.fb_admin_add.form.filemax', ini_get('upload_max_filesize')); ?></span></li>

            <li>
                <a class="add-more-photo" href="#" title="Adicionar mais fotos" style="color:#1296D4; text-decoration:underline;">Adicionar mais fotos</a>
                <div style="display:none; margin-top:2px;">
                    <p><?php echo $this->Form->input('Photo.1.filename', 
                        array('type' => 'file', 'label' => 'Foto 2')); ?></p>
        
                    <p><?php echo $this->Form->input('Photo.2.filename', 
                        array('type' => 'file', 'label' => 'Foto 3')); ?></p>
        
                    <p><?php echo $this->Form->input('Photo.3.filename', 
                        array('type' => 'file', 'label' => 'Foto 4')); ?></p>
                </div>            
			</li>
		</ul>            
	</div>

    
	<div class="r">
        <h3><?php echo __d('view', 'Products.admin_add.form.title2'); ?></h3>
		<ul>
		<?php if (!$fb_manage_pages) : ?>

			<li class="banner-fanpage-add">
				<?php 
					echo $this->Html->link(
							$this->Html->image('fanpage/btn_add.jpg', array(
							'alt' => 'Clique aqui e cadastre agora mesmo sua Fanpage no Bazzapp')),
							'#', array('escape' => false, 'title' =>  'Clique aqui e cadastre agora mesmo sua Fanpage no Bazzapp')
					); 
				?>	
			</li>

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
						
						<div class="btner buttonWrapper">
			        		<a class="spriteButton type1 accepted" href="#" rel="nofollow" style="width:97%"><span>Cadastrar as marcadas</span></a>
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
		
			<li><?php echo $this->Form->input('Product.title', array('type' => 'text', 'label' => __d('view', 'Products.admin_add.form.field_title.title'))); ?></li>
    		<li><?php echo $this->Form->input('Product.price', array('type' => 'text', 'label' => __d('view', 'Products.admin_add.form.field_price.title'), 'class' => 'currency')); ?></li>        
			<li class="l"><?php echo $this->Form->input('Product.quantity', array('type' => 'number','step'=> 'any', 'min'=>0, 'label' => __d('view', 'Products.admin_add.form.field_quantity.title'), 'class' => 'masknumber', 'style' => 'width:107px;')); ?></li>
			<li class="l"><div style="margin-top:-1px;"><?php echo $this->Form->input('Product.condition', array('type' => 'select', 'class' => 'required', 'label' => __d('view', 'Products.admin_add.form.field_condition.title'), 'options' => array('new' => __d('view', 'Products.admin_add.form.field_condition.new.title'), 'used' => __d('view', 'Products.admin_add.form.field_condition.used.title')))); ?></div>
			
            <!--
			<li class="l"><?php echo $this->Form->input('Product.exchangeable', array('type' => 'checkbox', 'label' => __d('view', 'Products.admin_add.form.field_exchangeable.title'))); ?></li>
			-->
            
			<li><?php echo $this->Form->input('Category.id', array('type' => 'select', 'label' => __d('view', 'Products.admin_add.form.field_category.title'), 'options' => $form_categories, 'empty' => ' ')); ?></li>
			<li class="l"><?php echo $this->Form->input('Product.category_id', array('type' => 'select', 'label' => __d('view', 'Products.admin_add.form.field_subcategory.title'))); ?></li>
			<li><?php echo $this->Form->input('Product.description', array('type' => 'textarea', 'label' => __d('view', 'Products.admin_add.form.field_description.title'))); ?></li>


			<?php if ($hasAddress !== true):?>
			<li><?php echo $this->Form->input('Product.zipcode', 
				array('type' => 'text', 'class' => 'cep', 'label' =>  __d('view', 'Stores.admin_add.form.field_cep.title'),'placeholder' => '_____-___'
				)); ?></li>			
			<li class="l"><?php echo $this->Form->input('Product.address', 
				array('type' => 'text', 'style' => 'width: 170px;', 'label' =>  __d('view', 'Stores.admin_add.form.field_address.title'),
				)); ?></li>
			<li class="l"><?php echo $this->Form->input('Product.addressLine2', 
				array('type' => 'text', 'style' => 'width: 80px;', 'label' =>  'Complemento',
				)); ?></li>
			<li class="clear"></li>
			<li><?php echo $this->Form->input('Product.district', 
				array('type' => 'text', 'label' =>  __d('view', 'Stores.admin_add.form.field_district.title'),
				)); ?></li>
			<li class="l"><?php echo $this->Form->input('Product.city', 
				array('type' => 'text', 'label' =>  __d('view', 'Stores.admin_add.form.field_city.title'), 'style' => 'text-transform:uppercase;'
				)); ?></li>
			<li class="l"><?php echo $this->Form->input('Product.state', 
				array('type' => 'text', 'label' =>  __d('view', 'Stores.admin_add.form.field_estate.title'),   'style' => 'text-transform:uppercase;'
				)); ?></li>			
			<?php endif; ?>
			<li><?php echo __d('view','Products.admin_add.form.disclaimerFee')?></li>
			<?php if ($stores): ?>
			<li><?php echo $this->Form->submit(__d('view', 'Products.admin_add.form.submit'), array('class'=> 'btn btn3', 'onclick'=>'BZ.showLoadingScreen("'.__d('view','Products.admin_add.form.loading').'")')); ?></li>
			<?php endif; ?>			
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
			
			
		}
	);

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
	
	var subcategory = "' . (!empty($this->request->data['Product']['category_id']) ? $this->request->data['Product']['category_id'] : '') . '";

	$(function()
	{
		$("select#CategoryId").change(
			function()
			{
				if ($(this).val())
				{
					$("select#ProductCategoryId").empty().attr("disabled", "disabled").append($("<option></option>").text("Carregando…"));
					$.uniform.update();
					
					categories = subCategories[$(this).children("option:selected").attr("value")];
							$("select#ProductCategoryId").empty().removeAttr("disabled").append($("<option></option>"));
							$.uniform.update(); 
							
							for (var i in categories)
							{
								var option = $("<option></option>").attr("value", i).text(categories[i]);
								if (subcategory && i == subcategory) option.attr("selected", "selected");
								
								$("select#ProductCategoryId").append(option);
							}
							
							subcategory = "";							
							$.uniform.update();
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

<?php endif;?>