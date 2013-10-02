Olá <?php echo $transaction['Store']['title']; ?>, você tem uma nova venda do item "<?php echo $transaction['StoreProduct']['Product']['title']; ?>" por <?php echo $transaction['Buyer']['name']; ?>.

Você pode acompanhar suas compras e vendas por <?php echo Configure::read('Facebook.redirect') . Router::url(array('controller' => 'sales', 'action' => 'index', 'admin' => true)); ?>.

<?php if (!empty($transaction['Delivery']['Address'])) : ?>
O comprador solicitou que o produto seja entregue no seguinte endereço:
CEP: <?php echo $transaction['Delivery']['Address']['zipcode']; ?> 
Endereço: <?php echo $transaction['Delivery']['Address']['address']; ?> 
Bairro: <?php echo $transaction['Delivery']['Address']['district']; ?> 
Cidade: <?php echo $transaction['Delivery']['Address']['city']; ?> 
Estado: <?php echo $transaction['Delivery']['Address']['state']; ?> 
<?php endif; ?>

Entre em contato com o comprador pelo email: <?php echo $transaction['Buyer']['email']; ?>