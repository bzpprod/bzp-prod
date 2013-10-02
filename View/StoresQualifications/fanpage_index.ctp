<?php 
	$this->set('body_class', 'qualification');
	$this->Html->addCrumb('Loja', '#');
	$this->Html->addCrumb('Depoimentos', '#');
	if(!empty($this->params['named']['sort'])){ $sort = $this->params['named']['sort']; }else{ $sort = ""; }
?>
<div id="qualifications-filter">
	<span><?php echo __d('view', 'Layouts.qualification.listby.Title'); ?></span>
	<?php echo $this->Html->link(__d('view', 'Layouts.qualification.listby.positive.Title'), Router::url(array('controller' => 'stores_qualifications', 'action' => 'index', 'admin' => false, 'store' => $store['Store']['slug'], 'sort' => 'positive')), array('class'=> 'positive', 'title' => __d('view', 'Layouts.qualification.listby.positive.Title'))); ?>
    
    <?php echo $this->Html->link(__d('view', 'Layouts.qualification.listby.negative.Title'), Router::url(array('controller' => 'stores_qualifications', 'action' => 'index', 'admin' => false, 'store' => $store['Store']['slug'], 'sort' => 'negative')), array('class'=> 'negative', 'title' => __d('view', 'Layouts.qualification.listby.negative.Title'))); ?>

</div>
<div class="clear"></div>

<div id="qualifications-items">

	<?php $totqualify = count($qualifications);
	for($i=0; $totqualify>$i; $i++){ ?>

    <!-- ITEM -->
	<div class="qualification-item">
       	<div class="image">
			<?php
            echo $this->Html->link(
                    $this->Html->image(
                        'https://graph.facebook.com/'.$qualifications[$i]['User']['FacebookUser']['fb_user_id'].'/picture?access_token='.$fb_access_token
            			,array('alt' => $qualifications[$i]['User']['name'])), 'http://facebook.com/'.$qualifications[$i]['User']['FacebookUser']['fb_user_id']
                 , array('target' => 'blank', 'escape' => false, 'title' => $qualifications[$i]['User']['name'])
            ); 
            ?>				
        </div>     
        <div class="qualification-description">
            <h3><?php echo $qualifications[$i]['User']['name']; ?></h3>
            <div class="type <?php echo $qualifications[$i]['Qualification']['qualification'];?>">
            	<?php echo __d('view', 'Layouts.qualification.qualifierwith.Title');?> 
            	<h4><?php echo __d('view', 'Layouts.qualification.listby.'.$qualifications[$i]['Qualification']['qualification'].'.Title');?></h4>
			</div>
			<p><?php echo $qualifications[$i]['Qualification']['testimony']; ?></p>
            <div class="hline"></div>
            <div class="date"><?php echo  date('d/m/Y H:i', strtotime($qualifications[$i]['Qualification']['created'])); ?></div>            
        </div>      
		<div class="clear"></div>
	</div>
    <!-- FIM#ITEM -->    
	
	<?php } ?>
    
</div>

<?php echo $this->element('paginator'); ?>