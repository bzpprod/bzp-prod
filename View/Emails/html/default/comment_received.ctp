<?php echo $product['Store']['title']; ?>,<br /><br />
Um possível comprador tem uma dúvida / comentário sobre o seu produto <?php echo $this->Html->link($product['Product']['title'], Configure::read('Facebook.redirect') . Router::url(array('controller' => 'products', 'action' => 'view', 'admin' => false, 'product' => $product['Product']['slug'])), array('title' => $product['Product']['title'])); ?> e por isso fez uma pergunta.<br /><br />
Responda o mais rápido possível.<br /><br />
<b>Equipe BazzApp</b><br /><br /><br />
<p align="center"><font size="-4">Este é um email automático não devendo ser respondido.</font></p>