Olá <?php echo $transaction['Buyer']['name']; ?>, você comprou o item "<?php echo $transaction['StoreProduct']['Product']['title']; ?>" do vendedor <?php echo $transaction['Store']['User']['name']; ?>.

Você pode acompanhar suas compras e vendas por <?php echo Configure::read('Facebook.redirect') . Router::url(array('controller' => 'purchases', 'action' => 'index', 'admin' => true)); ?>.

Entre em contato com o vendedor pelo email: <?php echo $transaction['Store']['User']['email']; ?>