<div class="qualifier" style="width:650px; height:320px;">
	<?php if (empty($transaction['SaleQualification']['id']) && !$success) : ?>
		<div class="l">
			<div id="answered"><span>0</span>/2</div>
		</div>
		
		<div class="r">
			<?php echo $this->Form->create('SaleQualification', array('class' => 'form')); ?>
			
			<div class="question" id="q1">
				<span>Você recebeu o pagamento de <?php echo $transaction['Buyer']['name']; ?>?</span>
                <div class="clear"></div>                
				<?php
					$attributes=array('legend'=>false);			
					$options=array('yes'=>'Sim', 'no'=>'Não');
					echo $this->Form->radio('SaleQualification.delivered',$options,$attributes);
				?>
                <div class="clear"></div>                
			</div>
			
			<div class="question why" style="display:none">
				<span><?php echo __d('view', 'Qualification.form.why.title'); ?></span>
                <div class="clear"></div>                
				<?php				
					$options = array('1' => 'Desisti de vender', 
									'2' => 'O comprador não o dinheiro para pagar o produto', 
									'3' => 'O comprador não respondeu meus e-mails', 
									'4' => 'O comprador não entrou em contato', 
									'5' => 'O comprador desistiu da venda', 
									'6' => 'Eu não tinha o produto em estoque', 
								    '7' => 'Outro motivo');
					echo $this->Form->input('SaleQualification.delivered_description_id', 
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
					echo $this->Form->radio('SaleQualification.qualification',$options,$attributes);
				?>
                <div class="clear"></div>                
			</div>
			
			<div class="question" id="q3">
                <div class="clear"></div>            
				<?php echo $this->Form->input('SaleQualification.testimony', 
				array('type' => 'textarea', 'label' => __d('view', 'Qualification.form.field_description.buyer.title'))); ?>
			</div>
			
			<div class="submit">
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