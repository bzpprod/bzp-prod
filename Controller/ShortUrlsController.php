<?php
App::uses('AppController', 'Controller');


class ShortUrlsController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'ShortUrls';
	
/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('ShortUrl');
	
/**	
 * Controller actions for which authorization is not required.
 *
 * @var array
 */
	public $allowedActions = array('index', 'generate');
	
	
	
/**
 * Called before the controller action.
 *
 */
	public function beforeFilter()
	{
		// If the requested action has been 'index', disable the login request.
		
		if (in_array(strtolower($this->request->params['action']), array('index')))
		{
			$this->loginDisabled = true;
		}
		
		parent::beforeFilter();
	}
	
	
/**
 * Redirect to original URL.
 * 
 * @param string $shortUrl
 */
	public function index($shortUrl)
	{
		// Gets the short url and redirects if valid.
		
		$shortUrl = $this->api_view($shortUrl);
		
		if (!empty($shortUrl))
		{		
			$this->redirect($shortUrl['ShortUrl']['original_url'], null, true);
		}
		
		
		$this->redirect('/', 301, true);
	}
	
	
/**
 * Generates a new short url.
 *
 * @param string $originalUrl
 * return string
 */
	public function generate($originalUrl)
	{
		$shortUrl = $this->api_add($originalUrl);
		
		if (!empty($shortUrl))
		{
			return Router::url(array('controller' => 'short_urls', 'action' => 'index', 'short_url' => $shortUrl['ShortUrl']['short_url_id'], 'admin' => false), true);
		}
		
		return '';
	}
	
	
/**
 * Displays the specified short url.
 *
 * @param string $shortUrl
 * @return array
 */
	public function api_view($shortUrl)
	{
		$shortUrl = $this->ShortUrl->find('first',
			array(
				'conditions' => array(
					'ShortUrl.short_url_id' => $shortUrl
				)
			)
		);
		
			
		$this->set(compact('shortUrl'));
		$this->set('_serialize', array('shortUrl'));
		
		return $shortUrl;
	}
	
	
/**
 * Generates a new short url.
 *
 * @param string $originalUrl
 * @return array
 */
	public function api_add($originalUrl)
	{
		// Checks if the original url was already stored.
		
		$shortUrl = $this->ShortUrl->find('first',
			array(
				'conditions' => array(
					'ShortUrl.original_url' => $originalUrl
				)
			)
		);
		
		if (empty($shortUrl))
		{
			$data = array(
				'ShortUrl' => array(
					'original_url' => $originalUrl
				)
			);
			
			
			$this->ShortUrl->create();
			
			if ($this->ShortUrl->save($data))
			{
				$shortUrl = $this->ShortUrl->findById($this->ShortUrl->getLastInsertId());
			}
		}
		
		
		$this->set(compact('shortUrl'));
		$this->set('_serialize', array('shortUrl'));
		
		return $shortUrl;
	}
}
