<?php echo $friend['User']['name']; ?>,

Seu amigo(a) <?php echo $user['User']['name']; ?> começou a participar do BazzApp também!

Acesse o perfil de "<?php echo $store['Store']['title']; ?>" e veja já o que ele(a) está vendendo!

<?php echo Configure::read('Facebook.redirect') . Router::url(array('controller' => 'stores', 'action' => 'view', 'admin' => false, 'store' => $store['Store']['slug'])); ?> 

Equipe BazzApp