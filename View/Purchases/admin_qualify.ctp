<div class="qualifier" style="width:650px; height:320px;">
	<?php if (empty($transaction['PurchaseQualification']['id']) && !isset($success)) : ?>
		<div class="l">
			<div id="answered"><span>0</span>/2</div>
		</div>
		
		<div class="r">
			<?php echo $this->Form->create('PurchaseQualification', array('class' => 'form')); ?>
			
			<div class="question" id="q1">
				<span>Você recebeu o produto de <?php echo $transaction['Store']['title']; ?>?</span>
                <div class="clear"></div>                
				<?php
					$attributes=array('legend'=>false);			
					$options=array('yes'=>'Sim', 'no'=>'Não');
					echo $this->Form->radio('PurchaseQualification.delivered',$options,$attributes);
				?>	
                <div class="clear"></div>                		
			</div>

			<div class="question why" style="display:none">
				<span><?php echo __d('view', 'Qualification.form.why.title'); ?></span>
                <div class="clear"></div>                
				<?php				
					$options = array('8' => 'Desisti de comprar', 
									'9' => 'Não tinha o dinheiro para pagar o produto', 
									'10' => 'O vendedor me contatou, porém não pude respondê-lo', 
									'11' => 'O vendedor não respondeu meus e-mails', 
									'12' => 'O vendedor não entrou em contato', 
									'13' => 'O vendedor desistiu da venda', 
									'14' => 'O vendedor não tinha o produto em estoque', 
									'15' => 'A descrição do produto não correspondia com o mesmo', 
									'16' => 'O vendedor não respeitou as políticas de envio e pagamento do produto', 
								   '17' => 'Outro motivo');
					echo $this->Form->input('PurchaseQualification.delivered_description_id', 
						array('type' => 'select', 'label' => false, 'options' => $options, 'empty' => 'Selecione', 'disabled' => 'true'));									
				?>					
                <div class="clear"></div>                
			</div>				
			
			<div class="question" id="q2">
				<span><?php echo __d('view', 'Qualification.form.field_qualification.title'); ?></span>
                <div class="clear"></div>
				<?php
					$attributes=array('legend'=>false, 'class'=> 'qualification-sended-type');				
					$options=array('positive'=>'Positiva', 'neutral'=>'Neutra', 'negative'=>'Negativa');
					echo $this->Form->radio('PurchaseQualification.qualification',$options,$attributes);
				?>
                <div class="clear"></div>                
			</div>
		
			<div class="question" id="q3">
				<?php echo $this->Form->input('PurchaseQualification.testimony', 
				array('type' => 'textarea', 'label' => __d('view', 'Qualification.form.field_description.store.title'))); ?>
                <div class="clear"></div>                
			</div>
			
			<div class="submit">
                <div class="clear"></div>            
				<div style="display:none">
					<?php echo $this->Form->submit(__d('view', 'Qualification.form.submit')); ?>
				</div>				
			</div>
		
		<?php echo $this->Form->end(); ?>		
		</div>
	<?php elseif ($success) : ?>
		<p>Qualificação enviada com sucesso.</p>
	<?php endif; ?>
</div>


<?php
echo $this->Html->scriptBlock('
	function qRefresh(){ 
		// if($("#q3.question textarea").val().length > 4) { qText = 1; }else{ qText = 0;} 
		qText = 0;
		qfieldsCheck = qText + ($(".qualifier input:checked").length); 
		$(".qualifier > .l > #answered > span").html(qfieldsCheck);			
		if(qfieldsCheck>1){ $(".qualifier .submit > div").show(); }else{ $(".qualifier .submit > div").hide(); }
	}	

	$("#q3.question textarea").live("keyup", function() { qRefresh(); });
	$("#q3.question textarea").blur(function(){ qRefresh(); });

	$(".question input").click(function(){
		qValue = $(this).val();
		qReference = $(this).parent().attr("id");
		if((qReference =="q1") || (qReference =="q2")){
			if((qValue=="no") || (qValue=="negative")|| (qValue=="neutral")){
				$("#"+qReference).next(".why").find("select").removeAttr("disabled");
				$("#"+qReference).next(".why").show();
			}else{
				$("#"+qReference).next(".why").find("select").attr("disabled", "disabled");
				$("#"+qReference).next(".why").hide();
			}
		}
		qRefresh();
	});

	$(".form").submit(function(){
		FB.api("/me/'. Configure::read('Facebook.Namespace').':qualify", "post", { store: "'.Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $transaction['Store']['slug']), true).'", rate: $("DIV .checked INPUT").val() }, function(e){if(e.error){console.log(e.error.message)}})
		qualify_send = $(".qualification-sended-type:checked").val();
		$.ajax({
		   type: "POST",
		   url: qualify_link,
		   data: $("form").serialize(),
		   success: function(data){ 
				 $("#"+qualify_id).remove();
				 $("#icon-qualifications-sender").addClass(qualify_send).show();
		   		 $.colorbox.close();
			}
		 });	
	 	return false;
	});		
	
	
'); ?>