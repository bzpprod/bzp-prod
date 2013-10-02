<?php echo $transaction['Store']['title']; ?>,<br /><br />
Você tem uma venda do item "<?php echo $this->Html->link($transaction['StoreProduct']['Product']['title'], Configure::read('Facebook.redirect') . Router::url(array('controller' => 'products', 'action' => 'view', 'admin' => false, 'product' => $transaction['StoreProduct']['slug']))); ?>", por <?php echo CakeNumber::currency($transaction['Transaction']['price'], 'BRR'); ?>, para o usuário <?php echo $transaction['Buyer']['name']; ?>.<br /><br />
<?php if (!empty($transaction['Store']['BankAccount'])) : ?>
Seus dados bancários foram enviados para ele, caso queira falar com ele entre em contato pelo email <?php echo $this->Html->link($transaction['Buyer']['email'], 'mailto:' . $transaction['Buyer']['email']); ?>.<br /><br />
<?php else : ?>
Entre em contato com o usuário o mais breve possível através do e-mail <?php echo $this->Html->link($transaction['Buyer']['email'], 'mailto:' . $transaction['Buyer']['email']); ?> informando os dados bancários para pagamento.<br /><br />
<?php endif; ?>
<?php if (!empty($transaction['Delivery']['Address'])) : ?>
O comprador solicitou que o produto seja entregue no seguinte endereço:<br /><br />
<b>CEP:</b> <?php echo $transaction['Delivery']['Address']['zipcode']; ?><br />
<b>Endereço:</b> <?php echo $transaction['Delivery']['Address']['address'] . ' - ' . $transaction['Delivery']['Address']['address_line2']; ?><br />
<b>Bairro:</b> <?php echo $transaction['Delivery']['Address']['district']; ?><br />
<b>Cidade:</b> <?php echo $transaction['Delivery']['Address']['city']; ?><br />
<b>Estado:</b> <?php echo $transaction['Delivery']['Address']['state']; ?><br /><br /><br />
<?php endif; ?>
<b>Equipe BazzApp</b><br /><br /><br />
<font size="-4">Este é um email automático não devendo ser respondido.</font>