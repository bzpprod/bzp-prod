<?php 
	$this->set('body_class', 'home'); 
?>

<div class="r">
	<div class="l">CURTIR</div>
    <div class="r">
        <select>
            <option>Mais Recentes</option>
            <option>Menor Pre�o</option>                
        </select>    
    </div>
	<?php print $this->element('product/list'); ?>
</div>