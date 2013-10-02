<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
 	
 	Router::parseExtensions('json');
 	
 	
 	Router::connect('/js/appVar.js',
 		array('controller'	=> 'static',
 			  'action'		=> 'js'
 		)
 	);
 	/** API **/
	
	Router::connect('/api/categories/*',
		array('controller' => 'categories', 'action' => 'index', 'api' => true)
	);
	
	Router::connect('/api/users/addresses/*',
		array('controller' => 'users_addresses', 'action' => 'index', 'api' => true)
	);
	
	Router::connect('/api/stores/install',
		array('controller' => 'stores', 'action' => 'install', 'api' => true)
	);
	
	Router::connect('/api/delivery/*',
		array('controller' => 'delivery', 'action' => 'index', 'api' => true)
	);
	
	Router::connect('/payments/paypal/paykey',
		array('controller' => 'payments_paypal', 'action' => 'add', 'admin' => true)
	);
	
	
	
	/* Routes for fanpage {{{ */
	if (strcasecmp(Configure::read('Config.subdomain'), 'fanpage') == 0)
 	{
 		
 		/* legacy routes (for access from domain fanpage.bazzapp.com)  */
 		Router::connect('/',
 			array('controller' => 'products', 'action' => 'home', 'fanpage' => true)
 		);
 		
 		
 		Router::connect('/login',
			array('controller' => 'users', 'action' => 'login')
		);
		
		Router::connect('/logout',
			array('controller' => 'users', 'action' => 'logout')
		);
		
		
		Router::connect('/system/like',
			array('controller' => 'system', 'action' => 'like')
		);
		
		Router::connect('/system/unlike',
			array('controller' => 'system', 'action' => 'unlike')
		);
		
		Router::connect('/system/comment',
			array('controller' => 'system', 'action' => 'comment')
		);
		
		Router::connect('/system/facebook-friends',
			array('controller' => 'system', 'action' => 'facebook_friends')
		);
		
				
		Router::connect('/stores/:store/follow',
			array('controller' => 'stores', 'action' => 'follow'),
			array(
				'store' => '[a-zA-Z0-9_-]*',
				'pass' => array('store')
			)
		);
		
		Router::connect('/stores/:store/unfollow',
			array('controller' => 'stores', 'action' => 'unfollow'),
			array(
				'store' => '[a-zA-Z0-9_-]*',
				'pass' => array('store')
			)
		);
		
		Router::connect('/stores/:store/following',
			array('controller' => 'stores', 'action' => 'following'),
			array(
				'store' => '[a-zA-Z0-9_-]*',
				'pass' => array('store')
			)
		);
 		
 		
 		Router::connect('/:store/purchase/:transaction',
 			array('controller' => 'purchases', 'action' => 'view', 'fanpage' => true),
 			array(
 				'store' => '[a-zA-Z0-9_-]*',
 				'transaction' => '[a-zA-Z0-9_-]*',
 				'pass' => array('transaction', 'store')
 			)
 		);
 		
 		Router::connect('/:store/qualifications/*',
 			array('controller' => 'stores_qualifications', 'action' => 'index', 'fanpage' => true),
 			array(
 				'store' => '[a-zA-Z0-9_-]*',
 				'pass' => array('store'),
 				'named' => array('filter')
 			)
 		);
 		
 		Router::connect('/:store/:product',
 			array('controller' => 'products', 'action' => 'view', 'fanpage' => true),
 			array(
 				'store' => '[a-zA-Z0-9_-]*',
 				'product' => '[a-zA-Z0-9_-]*',
 				'pass' => array('product', 'store')
 			)
 		);
 		
 		Router::connect('/:store/:product/buy',
 			array('controller' => 'products', 'action' => 'buy', 'fanpage' => true),
 			array(
 				'store' => '[a-zA-Z0-9_-]*',
 				'product' => '[a-zA-Z0-9_-]*',
 				'pass' => array('product', 'store')
 			)
 		);
 		
 		Router::connect('/:store/:product/delivery',
 			array('controller' => 'delivery', 'action' => 'index'),
 			array(
 				'store' => '[a-zA-Z0-9_-]*',
 				'product' => '[a-zA-Z0-9_-]*',
 				'pass' => array('product')
 			)
 		);
 		
 		Router::connect('/:store/*',
			array('controller' => 'products', 'action' => 'index', 'fanpage' => true),
			array(
				'store' => '[a-zA-Z0-9_-]*',
				'pass' => array('store'),
				'named' => array('search', 'page', 'sort', 'dir')
			)
		);
		
		Router::connect('/:store',
			array('controller' => 'products', 'action' => 'index', 'fanpage' => true),
			array(
				'store' => '[a-zA-Z0-9_-]*',
				'pass' => array('store')
			)
		);
 	}
 	/* Entering fanpage by /fp/ url */
 	elseif (strpos($_SERVER['REQUEST_URI'],'/fp') == 0)
 	{
 		Router::connect('/fp',
 			array('controller' => 'products', 'action' => 'home', 'fanpage' => true)
 		);
 		
 		
 		Router::connect('/fp/login',
			array('controller' => 'users', 'action' => 'login', 'fanpage' => true)
		);
		
		Router::connect('/fp/logout',
			array('controller' => 'users', 'action' => 'logout', 'fanpage' => true)
		);
		
		
		Router::connect('/fp/system/like',
			array('controller' => 'system', 'action' => 'like', 'fanpage' => true)
		);
		
		Router::connect('/fp/system/unlike',
			array('controller' => 'system', 'action' => 'unlike', 'fanpage' => true)
		);
		
		Router::connect('/fp/system/comment',
			array('controller' => 'system', 'action' => 'comment', 'fanpage' => true)
		);
		
		Router::connect('/fp/system/facebook-friends',
			array('controller' => 'system', 'action' => 'facebook_friends', 'fanpage' => true)
		);
		
		
		Router::connect('/fp/stores/:store/follow',
			array('controller' => 'stores', 'action' => 'follow', 'fanpage' => true),
			array(
				'store' => '[a-zA-Z0-9_-]*',
				'pass' => array('store')
			)
		);
		
		Router::connect('/fp/stores/:store/unfollow',
			array('controller' => 'stores', 'action' => 'unfollow', 'fanpage' => true),
			array(
				'store' => '[a-zA-Z0-9_-]*',
				'pass' => array('store')
			)
		);
		
		Router::connect('/fp/stores/:store/following',
			array('controller' => 'stores', 'action' => 'following', 'fanpage' => true),
			array(
				'store' => '[a-zA-Z0-9_-]*',
				'pass' => array('store')
			)
		);
 		
 		
 		Router::connect('/fp/:store/purchase/:transaction',
 			array('controller' => 'purchases', 'action' => 'view', 'fanpage' => true),
 			array(
 				'store' => '[a-zA-Z0-9_-]*',
 				'transaction' => '[a-zA-Z0-9_-]*',
 				'pass' => array('transaction', 'store')
 			)
 		);
 		
 		Router::connect('/fp/:store/qualifications/*',
 			array('controller' => 'stores_qualifications', 'action' => 'index', 'fanpage' => true),
 			array(
 				'store' => '[a-zA-Z0-9_-]*',
 				'pass' => array('store'),
 				'named' => array('filter')
 			)
 		);
 		
 		Router::connect('/fp/:store/:product',
 			array('controller' => 'products', 'action' => 'view', 'fanpage' => true),
 			array(
 				'store' => '[a-zA-Z0-9_-]*',
 				'product' => '[a-zA-Z0-9_-]*',
 				'pass' => array('product', 'store')
 			)
 		);
 		
 		Router::connect('/fp/:store/:product/buy',
 			array('controller' => 'products', 'action' => 'buy', 'fanpage' => true),
 			array(
 				'store' => '[a-zA-Z0-9_-]*',
 				'product' => '[a-zA-Z0-9_-]*',
 				'pass' => array('product', 'store')
 			)
 		);
 		
 		Router::connect('/fp/:store/:product/delivery',
 			array('controller' => 'delivery', 'action' => 'index'),
 			array(
 				'store' => '[a-zA-Z0-9_-]*',
 				'product' => '[a-zA-Z0-9_-]*',
 				'pass' => array('product')
 			)
 		);
 		
 		Router::connect('/fp/:store/*',
			array('controller' => 'products', 'action' => 'index', 'fanpage' => true),
			array(
				'store' => '[a-zA-Z0-9_-]*',
				'pass' => array('store'),
				'named' => array('search', 'page', 'sort', 'dir')
			)
		);
		
		Router::connect('/fp/:store',
			array('controller' => 'products', 'action' => 'index', 'fanpage' => true),
			array(
				'store' => '[a-zA-Z0-9_-]*',
				'pass' => array('store')
			)
		);
 	}
 	/*}}} end if fanpage */
		
 	
 	Router::connect('/',
		array('controller' => 'products', 'action' => 'home')
	);
	
	Router::connect('/home/*',
		array('controller' => 'products', 'action' => 'home'),
		array(
			'named' => array('filter')
		)
	);
	
	
	Router::connect('/terms-of-use',
		array('controller' => 'pages', 'action' => 'display', 'terms_of_use')
	);
	
	Router::connect('/privacy-policy',
		array('controller' => 'pages', 'action' => 'display', 'privacy_policy')
	);
	
	Router::connect('/login',
		array('controller' => 'users', 'action' => 'login')
	);
	
	Router::connect('/logout',
		array('controller' => 'users', 'action' => 'logout')
	);
		
	Router::connect('/system/like',
		array('controller' => 'system', 'action' => 'like')
	);
	
	Router::connect('/system/unlike',
		array('controller' => 'system', 'action' => 'unlike')
	);
	
	Router::connect('/system/comment',
		array('controller' => 'system', 'action' => 'comment')
	);
	
	Router::connect('/system/facebook-friends',
		array('controller' => 'system', 'action' => 'facebook_friends')
	);
	
	Router::connect('/system/notificationAlert',
		array('controller' => 'system', 'action' => 'notificationAlert'),
		array('pass' => array('command','type'))
	);
		
	
	Router::connect('/referral',
		array('controller' => 'referrals', 'action' => 'index', 'admin' => true)
	);
	
	Router::connect('/licenses',
		array('controller' => 'pages', 'action' => 'display', 'licenses')
	);
	
	Router::connect('/stores/:store/follow',
		array('controller' => 'stores', 'action' => 'follow'),
		array(
			'store' => '[a-zA-Z0-9_-]*',
			'pass' => array('store')
		)
	);
	
	Router::connect('/stores/:store/unfollow',
		array('controller' => 'stores', 'action' => 'unfollow'),
		array(
			'store' => '[a-zA-Z0-9_-]*',
			'pass' => array('store')
		)
	);
	
	Router::connect('/stores/:store/following',
		array('controller' => 'stores', 'action' => 'following'),
		array(
			'store' => '[a-zA-Z0-9_-]*',
			'pass' => array('store')
		)
	);
	
	Router::connect('/stores/:store/qualifications/*',
		array('controller' => 'stores_qualifications', 'action' => 'index'),
		array(
			'store' => '[a-zA-Z0-9_-]*',
			'pass' => array('store'),
			'named' => array('page', 'filter')
		)
	);
	
	Router::connect('/stores/:store/*',
		array('controller' => 'stores', 'action' => 'view'),
		array(
			'store' => '[a-zA-Z0-9_-]*',
			'pass' => array('store'),
			'named' => array('page', 'sort', 'dir', 'category','filter')
		)
	);
	
	Router::connect('/stores',
		array('controller' => 'stores', 'action' => 'view')
	);
	
	
	Router::connect('/products/:product',
		array('controller' => 'products', 'action' => 'view'),
		array(
			'product' => '[a-zA-Z0-9_-]*',
			'pass' => array('product')
		)
	);
	
	Router::connect('/products/:product/delivery',
 		array('controller' => 'delivery', 'action' => 'index'),
 		array(
 			'product' => '[a-zA-Z0-9_-]*',
 			'pass' => array('product')
 		)
 	);
	
	Router::connect('/products/:product/buy',
		array('controller' => 'products', 'action' => 'buy'),
		array(
			'product' => '[a-zA-Z0-9_-]*',
			'pass' => array('product')
		)
	);
	
	Router::connect('/products/*',
		array('controller' => 'products', 'action' => 'index'),
		array(
			'named' => array('search', 'category', 'page', 'sort', 'dir','filter')
		)
	);
	
	
	Router::connect('/sell/paypalAccount',
		array('controller' => 'products', 'action' => 'paypalAdaptiveAccountAction')
	);
	
	Router::connect('/sell',
		array('controller' => 'products', 'action' => 'add', 'admin' => true)
	);
	
	Router::connect('/sell/import',
		array('controller' => 'products', 'action' => 'import', 'admin' => true)
	);
	
	
	Router::connect('/my-store/administered',
 		array('controller' => 'stores', 'action' => 'index', 'admin' => true)
 	);
 	
 	Router::connect('/my-store/:store/edit',
 		array('controller' => 'stores', 'action' => 'edit', 'admin' => true),
 		array(
			'store' => '[a-zA-Z0-9_-]*',
			'pass' => array('store')
		)
 	);
	
	Router::connect('/my-store/:store/:product/edit',
		array('controller' => 'products', 'action' => 'edit', 'admin' => true),
		array(
			'store' => '[a-zA-Z0-9_-]*',
			'product' => '[a-zA-Z0-9_-]*',
			'pass' => array('product', 'store')
		)
	);
	
	Router::connect('/my-store/:store/:product/delete',
		array('controller' => 'products', 'action' => 'delete', 'admin' => true),
		array(
			'store' => '[a-zA-Z0-9_-]*',
			'product' => '[a-zA-Z0-9_-]*',
			'pass' => array('product', 'store')
		)
	);
	
	Router::connect('/my-store/:store/:product/:photo/delete',
		array('controller' => 'products_photos', 'action' => 'delete', 'admin' => true),
		array(
			'store' => '[a-zA-Z0-9_-]*',
			'product' => '[a-zA-Z0-9_-]*',
			'photo' => '[a-zA-Z0-9_-]*',
			'pass' => array('photo', 'product', 'store')
		)
	);
	
	Router::connect('/my-store/:store/sold-out/*',
		array('controller' => 'stores', 'action' => 'view_archive', 'admin' => true),
		array(
			'store' => '[a-zA-Z0-9_-]*',
			'pass' => array('store'),
			'named' => array('page', 'sort', 'dir')
		)
	);
	
	Router::connect('/my-store/:store/*',
		array('controller' => 'stores', 'action' => 'view', 'admin' => true),
		array(
			'store' => '[a-zA-Z0-9_-]*',
			'pass' => array('store'),
			'named' => array('page', 'sort', 'dir')
		)
	);
	
	Router::connect('/my-store',
 		array('controller' => 'stores', 'action' => 'view', 'admin' => true)
 	);
	
	
	Router::connect('/sales/:store/:transaction',
		array('controller' => 'sales', 'action' => 'view', 'admin' => true),
		array(
			'store' => '[a-zA-Z0-9_-]*',
			'transaction' => '[a-zA-Z0-9_-]*',
			'pass' => array('transaction', 'store')
		)
	);
	
	Router::connect('/sales/:store/:transaction/buyer',
		array('controller' => 'buyers', 'action' => 'view', 'admin' => true),
		array(
			'store' => '[a-zA-Z0-9_-]*',
			'transaction' => '[a-zA-Z0-9_-]*',
			'pass' => array('transaction', 'store')
		)
	);
	
	Router::connect('/sales/:store/:transaction/qualify',
		array('controller' => 'sales', 'action' => 'qualify', 'admin' => true),
		array(
			'store' => '[a-zA-Z0-9_-]*',
			'transaction' => '[a-zA-Z0-9_-]*',
			'pass' => array('transaction', 'store')
		)
	);
	
	Router::connect('/sales/:store/*',
		array('controller' => 'sales', 'action' => 'index', 'admin' => true),
		array(
			'store' => '[a-zA-Z0-9_-]*',
			'pass' => array('store'),
			'named' => array('page')
		)
	);
	
	Router::connect('/sales',
		array('controller' => 'sales', 'action' => 'index', 'admin' => true)
	);
	
	
	/** BACKUP **/
	
	Router::connect('/purchases/paypal/payment-key', array('controller' => 'payments_paypal', 'action' => 'add', 'admin' => true));
	Router::connect('/purchases/paypal/:payment/cancel', array('controller' => 'payments_paypal', 'action' => 'cancel'), array('payment' => '[a-zA-Z0-9_-]*', 'pass' => array('payment')));
	Router::connect('/purchases/paypal/:payment/finish', array('controller' => 'payments_paypal', 'action' => 'finish'), array('payment' => '[a-zA-Z0-9_-]*', 'pass' => array('payment')));
	Router::connect('/purchases/paypal/:payment/notification', array('controller' => 'payments_paypal', 'action' => 'notification'), array('payment' => '[a-zA-Z0-9_-]*', 'pass' => array('payment')));
	
	/************/	
	
	Router::connect('/purchases/:transaction',
		array('controller' => 'purchases', 'action' => 'view', 'admin' => true),
		array(
			'transaction' => '[a-zA-Z0-9_-]*',
			'pass' => array('transaction')
		)
	);
	
	Router::connect('/purchases/:transaction/seller',
		array('controller' => 'sellers', 'action' => 'view', 'admin' => true),
		array(
			'transaction' => '[a-zA-Z0-9_-]*',
			'pass' => array('transaction')
		)
	);
	
	Router::connect('/purchases/:transaction/qualify',
		array('controller' => 'purchases', 'action' => 'qualify', 'admin' => true),
		array(
			'transaction' => '[a-zA-Z0-9_-]*',
			'pass' => array('transaction')
		)
	);
	
	Router::connect('/purchases/:transaction/finished',
		array('controller' => 'purchases', 'action' => 'view'),
		array(
			'transaction' => '[a-zA-Z0-9_-]*',
			'pass' => array('transaction')
		)
	);
	
	Router::connect('/purchases/*',
		array('controller' => 'purchases', 'action' => 'index', 'admin' => true),
		array(
			'named' => array('page')
		)
	);
	
	
	Router::connect('/payments/paypal/:payment/cancel',
		array('controller' => 'payments_paypal', 'action' => 'cancel'),
		array(
			'payment' => '[a-zA-Z0-9_-]*',
			'pass' => array('payment')
		)
	);
	
	Router::connect('/payments/paypal/:payment/finish',
		array('controller' => 'payments_paypal', 'action' => 'finish'),
		array(
			'payment' => '[a-zA-Z0-9_-]*',
			'pass' => array('payment')
		)
	);
	
	Router::connect('/payments/paypal/:payment/notification',
		array('controller' => 'payments_paypal', 'action' => 'notification'),
		array(
			'payment' => '[a-zA-Z0-9_-]*',
			'pass' => array('payment')
		)
	);
	
	
	Router::connect('/short-url/*',
		array('controller' => 'short_urls', 'action' => 'generate')
	);
	
	Router::connect('/:short_url',
		array('controller' => 'short_urls', 'action' => 'index'),
		array(
			'short_url' => '[a-zA-Z0-9]{5,11}',
			'pass' => array('short_url')
		)
	);
	
	
	/** BACKUP **/
	
	Router::connect('/store', array('controller' => 'stores', 'action' => 'index'));
	Router::connect('/store/:store/qualifications/*', array('controller' => 'stores_qualifications', 'action' => 'index'), array('store' => '[a-zA-Z0-9_-]*', 'pass' => array('store'), 'named' => array('page', 'sort', 'dir')));
	Router::connect('/store/:store/*', array('controller' => 'stores', 'action' => 'view'), array('store' => '[a-zA-Z0-9_-]*', 'pass' => array('store'), 'named' => array('page', 'sort', 'dir')));
	
	/************/
	
	Router::connect('/__cron__forgottenProduct',
			array('controller' => 'system', 'action' => 'forgottenProduct')
	);
	
	Router::connect('/__cron__qualifyReminder',
			array('controller' => 'system', 'action' => 'qualifyReminder')
	);
	
	
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes.  See the CakePlugin documentation on 
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
