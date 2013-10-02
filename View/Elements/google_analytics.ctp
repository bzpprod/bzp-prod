	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-33170031-1']);
		_gaq.push(['_setDomainName','none']);
  		_gaq.push(['_setAllowLinker',true]);
		_gaq.push(['_setAllowAnchor',true]);
		_gaq.push(['_setAllowHash',true]);
		<?php if (isset($logged_user) && isset($logged_user['User']['email'])): ?>
		_gaq.push(['_setCustomVar',0,'logged-in','<?php echo $logged_user['User']['email']?>',1]);
		<?php endif ?>
		<?php if (Configure::read('debug') == 1) : ?>
		_gaq.push(['_setCustomVar',1,'debug','1',1]);
		<?php endif; ?>
		_gaq.push(['_trackPageview', location.pathname + location.search + location.hash]);

		<?php if (Configure::read('debug') == 0):?>
		(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
		<?php endif;?>
	</script>
