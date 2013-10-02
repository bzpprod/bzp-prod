<?php
	$this->set('body_class', 'friends-accepted'); 
	
	$invite_url = $this->requestAction(array('controller' => 'short_urls', 'action' => 'generate', 'admin' => false), array('pass' => array(Configure::read('Facebook.redirect') ."/?referral=".$logged_user['User']['referral_uid'])));
	list($referrals, $referrals_count) = $this->requestAction(array('controller' => 'referrals', 'action' => 'index', 'admin' => true), array('pass' => array()));
?>

<h2><?php echo __d('view','Referral.shareinviteriends.Title'); ?></h2>

<div class="left">
    <h3><?php echo __d('view','Referral.invitefriends.Title'); ?></h3>
    <p><?php echo __d('view','Referral.copypastelink.Title'); ?></p>
    
    <div class="referral"><?php echo str_replace("www.","",$invite_url); ?></div>
   	<a href="#" title="<?php echo __d('view','Referral.selectfriends.Title'); ?>" class="invite btn-fb">&nbsp;</a>
    
    <div class="clear"></div>
</div>

<div class="right vline">
    <h3><?php echo __d('view','Referral.selectfriends.Title'); ?></h3>
    <p><?php echo __d('view','Referral.choiceoneorenvite.Title'); ?></p>

   	<a href="#" class="btn-fb-selec">&nbsp;</a>
</div>    

<div class="clear hline"></div>

<h2><?php echo __d('view','Referral.invites.Title'); ?></h2>
<h3><?php echo $referrals_count.__d('view','Referral.acceptedfriends.Title'); ?></h3>

<ul class="apted-people">
	<?php foreach ($referrals as $key => $value){ ?>
		<li> <?php echo $this->Html->link(
			  $this->Html->image('https://graph.facebook.com/'.$value['User']['FacebookUser']['fb_user_id'].'/picture?type=normal&access_token='.$fb_access_token
							,array('alt' =>  $value['User']['name'], 'class' => 'thumb-seller'))." ".$value['User']['name'],
					'https://facebook.com/'.$value['User']['FacebookUser']['fb_user_id'], 
					array('escape' => false, 'target' => '_blank', 'title' => $value['User']['name'])
			); ?>
        </li>                                
	<?php } ?>              
</ul>


<?php
echo $this->Html->scriptBlock('
	$("a.invite.btn-fb").live("click",function(e){
		e.preventDefault();
		FB.ui({
			 method: "feed",
			 link: "'.$invite_url.'",
			 picture: "'.Router::url('/img/logo.jpg',true).'",
			 name: "'.__d('view','Invite.header.name').'",
			 description: "'.__d('view','Invite.header.description').'"
		});
	});
	
	$("a.btn-fb-selec").click(function(e){
		e.preventDefault();
		  FB.ui({
		  		method: "apprequests",
          		message: "'.sprintf(__d('view','Invite.apprequests.name'), $logged_user['User']['name']).'",
			 	link: "'.$invite_url.'",
			 	picture: "'.Router::url('/img/logo.jpg',true).'",
        });
	});
			
');
?>

