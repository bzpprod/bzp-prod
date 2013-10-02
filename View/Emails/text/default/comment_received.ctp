<?php echo $product['Store']['title']; ?>,

Um possível comprador tem uma dúvida / comentário sobre o seu produto "<?php echo $product['Product']['title']; ?>" e por isso fez uma pergunta.

<?php echo Configure::read('Facebook.redirect') . Router::url(array('controller' => 'products', 'action' => 'view', 'admin' => false, 'product' => $product['Product']['slug'])); ?> 

Responda o mais rápido possível.

Equipe BazzApp