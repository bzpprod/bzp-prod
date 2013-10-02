<?php echo $transaction['Buyer']['name']; ?>,<br /><br />
Obrigado pela compra do item "<?php echo $this->Html->link($transaction['StoreProduct']['Product']['title'], Configure::read('Facebook.redirect') . Router::url(array('controller' => 'products', 'action' => 'view', 'admin' => false, 'product' => $transaction['StoreProduct']['slug']))); ?>", por <?php echo CakeNumber::currency($transaction['Transaction']['price'], 'BRR'); ?>.<br /><br />
<?php if (!empty($transaction['Store']['BankAccount'])) : ?>
Agora falta somente efetuar o pagamento do mesmo, você pode fazer isto com os dados bancários abaixo.<br /><br />
<b>Banco:</b> <?php echo $transaction['Store']['BankAccount']['bank']; ?><br />
<b>Agência:</b> <?php echo $transaction['Store']['BankAccount']['agency']; ?><br />
<b>Conta Bancária:</b> <?php echo $transaction['Store']['BankAccount']['account']; ?><br />
<b>Nome:</b> <?php echo $transaction['Store']['BankAccount']['name']; ?><br />
<b>CPF/CNPJ:</b> <?php echo $transaction['Store']['BankAccount']['document']; ?><br /><br />
Quaisquer dúvidas entre em contato com o vendedor através do email <?php echo $this->Html->link($transaction['Store']['User']['email'], 'mailto:' . $transaction['Store']['User']['email']); ?>.<br /><br /><br />
<?php else : ?>
Agora falta somente efetuar o pagamento do mesmo. Entre em contato com o vendedor através do e-mail <?php echo $this->Html->link($transaction['Store']['User']['email'], 'mailto:' . $transaction['Store']['User']['email']); ?> solicitando os dados bancários.<br /><br /><br />
<?php endif; ?>
<b>Equipe BazzApp</b><br /><br /><br />
<font size="-4">Este é um email automático não devendo ser respondido.</font>