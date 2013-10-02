<?php 
// MENU SELECTED
if (isset($body_page)){
	if($body_page == "store_edit"){ $item = "item2"; }
	else if(($body_page == "purchases_view") || ($body_page == "sales_view")){ $item = "item3"; }		
	else { $item = "item1"; }
}else { $item = "item1"; }
echo $this->Html->scriptBlock('$(document).ready(function () { $("a#'.$item.'").addClass("selected"); }); ');		

?>

<div id="adm-menu">
	<?php echo $this->Html->link("<span>".__d('view', 'Layouts.default.menu.advertise.Title')."</span>", 
		array('controller' => 'stores', 'action' => 'view', 'admin' => true), 
		array('id' => 'item1', 'title' => __d('view', 'Layouts.default.menu.advertise.Title'), 'escape' => false)); ?>
    
    <?php echo $this->Html->link("<span>".__d('view', 'Layouts.default.menu.editstore.Title')."</span>",
		array('controller' => 'stores', 'action' => 'edit', 'store' => $store['Store']['slug'], 'admin' => true), 
        array('id' => 'item2', 'escape' => false, 'title' => __d('view','Stores.fb_admin_edit.title.edit'))); ?>
    
    <?php echo $this->Html->link("<span>".__d('view', 'Layouts.default.menu.my_transactions.Title')."</span>",
		array('controller' => 'sales', 'action' => 'index', 'store' => $store['Store']['slug'],  'admin' => true),
		array('id' => 'item3', 'escape' => false, 'title' => __d('view', 'Layouts.default.menu.my_transactions.Title'))); ?>                        
    
    <?php echo $this->Html->link("<span>".__d('view', 'Layouts.default.menu.invitefriends.Title')."</span>", '#', array('id' => 'item4', 
		'title' => __d('view','Layouts.default.menu.invitefriends.Title'), 'class' => 'invitefriends', 'escape' => false)); ?>

</div>    