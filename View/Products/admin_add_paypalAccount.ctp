<?php
$this->set('body_class', 'edit add');
$this->set('body_page', 'product_add');
// METAS
$this->set('title_for_layout', __d('view', 'Layouts.default.meta.sell.title'));
$this->set('meta_description', __d('view', 'Layouts.default.meta.sell.description'));
$this->set('meta_keywords', __d('view', 'Layouts.default.meta.sell.keywords'));

if (isset($paypalRedirectUrl)):
?>
<script>
setTimeout(function(){window.open("<?php echo $paypalRedirectUrl?>","_blank"); $('#paypalWaitingDisplay').removeClass('hide')},3000);
</script>
<h2>Muito obrigado! :)</h2><br>
Agora você será redirecionado para o site do <img src="<?php echo Configure::read('staticUrl')?>/img/icon-paypal.jpg" alt="PayPal"> para terminar o processo (são só mais 2 passos, prometemos). Depois disto clique no email de confirmação que eles mandarem e pronto :) Você conseguirá receber pagamentos via PayPal no BazzApp :)
<br><br>
Caso a janela não abra veja se você não está bloqueando popups em seu navegador ou <a href="<?php echo $paypalRedirectUrl?>" onClick="$('#paypalWaitingDisplay').removeClass('hide')" target="_blank">clique aqui</a>.

<br><br><br>
<div class="btn hide" id="paypalWaitingDisplay">
	Verificando se seu email foi confirmando no PayPal. Por favor, aguarde.<br>
	<img src='/img/loading.gif' alt="Carregando..." style="margin-top:15px;">
</div>
<div class="btn hide" id="paypalOkDisplay">
	Tudo certo!<br>Seu email já foi validado. Estamos te direcionando para a página de publicação de produto.
</div>
<script>$(document).ready(function(){BZ.checkPaypalEmailAddress(BZ.baseUrl + '<?php echo $this->here?>?action=checkVerified');});</script>

<?php else:?>
<fieldset>
	<?php echo $this->Form->create('AA', array('type' => 'file')); ?>
		<?php echo $this->Form->hidden('paypalAdaptiveAccount.hasAddress', array('value'=>$paypalAdaptiveAccount['hasAddress'])); ?>
		<?php echo $this->Form->hidden('paypalAdaptiveAccount.hasPhone',  array('value'=>$paypalAdaptiveAccount['hasPhone']));   ?>

	<div class="r">
				<h2>Preencha os campos abaixo para criar sua conta no PayPal.</h2>
				<?php if (isset($errorMsg)):?>
					<label class='error'><?php echo $errorMsg;?></label>
				<?php endif;?>
			<ul>
			<li>
				<?php echo $this->Form->input('paypalAdaptiveAccount.firstName', array('type' => 'text', 'label' => 'Nome:', 'value' => $paypalAdaptiveAccount['firstName'])); ?>
			</li>
			<li class="l">
				<?php echo $this->Form->input('paypalAdaptiveAccount.lastName', array('type' => 'text', 'label' => 'Sobrenome:', 'value' => $paypalAdaptiveAccount['lastName'])); ?>
			</li>
			<li class="l">
				<?php echo $this->Form->input('paypalAdaptiveAccount.uniqueIdentifierNumber', array('type' => 'text', 'label' => 'CPF:', 'class'=>'cpf', 'value' => $paypalAdaptiveAccount['uniqueIdentifierNumber'], 'placeholder' => '___.___.___-__')); ?>
			</li>
			<li>
				<?php echo $this->Form->input('paypalAdaptiveAccount.birthday', array('type' => 'text', 'label' => 'Data de nascimento:', 'class'=>'date', 'value' => $paypalAdaptiveAccount['birthday'], 'placeholder'=>'__/__/____')); ?>
			</li>
			<li class="l">
				<?php echo $this->Form->input('paypalAdaptiveAccount.paypalEmail', array('type' => 'email', 'label' => 'E-mail da conta PayPal:', 'class'=>'email', 'value' => $paypalAdaptiveAccount['email'])); ?>
			</li>

			<li class="l">
				<?php echo $this->Form->input('paypalAdaptiveAccount.phone', array('type' => 'tel', 'label' => 'Telefone:', 'class'=>'phone',  'value' => $paypalAdaptiveAccount['phone'], 'placeholder' => '(__) ____-_____')); ?>
			</li>

			<li><?php echo $this->Form->input('paypalAdaptiveAccount.zipcode', 
				array('type' => 'text', 'class' => 'cep', 'label' =>  __d('view', 'Stores.admin_add.form.field_cep.title'), 'value' => $paypalAdaptiveAccount['zipcode'], 'placeholder' => '_____-___'
				)); ?></li>			
			<li class="l"><?php echo $this->Form->input('paypalAdaptiveAccount.address', 
				array('type' => 'text', 'style' => 'width: 170px;', 'label' =>  __d('view', 'Stores.admin_add.form.field_address.title'), 'value' => $paypalAdaptiveAccount['address']
				)); ?></li>
			<li class="l"><?php echo $this->Form->input('paypalAdaptiveAccount.addressLine2', 
				array('type' => 'text', 'style' => 'width: 80px;', 'label' =>  'Complemento', 'value' => $paypalAdaptiveAccount['addressLine2']
				)); ?></li>
			<li class="clear"></li>
			<li><?php echo $this->Form->input('paypalAdaptiveAccount.district', 
				array('type' => 'text', 'label' =>  __d('view', 'Stores.admin_add.form.field_district.title'), 'value' => $paypalAdaptiveAccount['district']
				)); ?></li>
			<li class="l"><?php echo $this->Form->input('paypalAdaptiveAccount.city', 
				array('type' => 'text', 'label' =>  __d('view', 'Stores.admin_add.form.field_city.title'), 'value' => $paypalAdaptiveAccount['city'], 'style' => 'text-transform:uppercase;'
				)); ?></li>
			<li class="l"><?php echo $this->Form->input('paypalAdaptiveAccount.state', 
				array('type' => 'text', 'label' =>  __d('view', 'Stores.admin_add.form.field_estate.title'), 'value' => $paypalAdaptiveAccount['state'], 'style' => 'text-transform:uppercase;'
				)); ?></li>			
			<li class="clear"></li>			

			<li style='width:420px;margin-bottom:5px;'>		
        		<div class='buttonWrapper follow'>
        			<a class="btn btn3 btnPaypalSubmit" href="javascript:$('FORM').submit()" rel="nofollow"><span>Criar conta no PayPal</span></a>
        		</div>
        	</li>
			<li style='width:420px'>		
        		<div class='buttonWrapper follow'>
        			<a class="btn btn2 btnPaypalSubmit" href="/" rel="nofollow" target="_parent"><span>Voltar para o BazzApp</span></a>
        		</div>
        	</li>
		</ul>
	<?php echo $this->Form->end(); ?>
    
	</div>    
    
</fieldset>
<?php endif; ?>
<style>
#global.edit.add div.r {
	float: left;
}
#content {
	height:500px;
	width:400px;
	<?php if(isset($paypalRedirectUrl)):?>
		padding:10px 20px;
	<?php endif;?>
	text-align:justify;
}
</style>
