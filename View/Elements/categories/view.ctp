<?php
$named_search	= (!empty($this->request->params['named']['search']) ? $this->request->params['named']['search'] : '');
$named_category	= (!empty($this->request->params['named']['category']) ? $this->request->params['named']['category'] : '');

$categories = $this->requestAction(
	array('controller' => 'categories', 'action' => 'index', 'api' => true),
	array('named' => array('has_products'=>true, 'category' => $named_category, 'search' => $named_search, 'direct_children' => true))	
);
?>
<div class="l">
	<div id="menulat">
		<h2><?php echo __d('view', 'Layouts.fb_default.categories'); ?></h2>
		<ul>
			<?php foreach ($categories as $key=>$value): ?>
				<?php
				$parameters = array('controller' => 'products', 'action' => 'index', 'category' => $value['Category']['slug'], 'admin' => false);
				if (!empty($named_search)) $parameters['search'] = $named_search;
				?>
				<li><?php echo $this->Html->link($value['Category']['title'], $parameters); ?> </li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>