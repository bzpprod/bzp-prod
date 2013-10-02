<?php
App::uses('AppController', 'Controller');
Configure::load('postoffice');


class DeliveryController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'Delivery';
	
/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('StoreProduct');
	
	
	
/**	
 * Lists the available shipping methods for specified product and zipcode.
 *
 * @param string $product
 */
	public function index($product)
	{
		// Gets the shipping methods.
	
		$delivery = $this->api_index($product);
		
		
		$this->set(compact('delivery'));
	}
	
	
/**	
 * List available shipping methods for specified product and recipient zipcode.
 *
 * @param string $product
 * @return array
 */
	public function api_index($product)
	{
		// Available delivery methods.
		
		$delivery = array();
		
	
		// Checks if the recipient zipcode was specified.
		
		if (!empty($this->request->params['named']['zipcode']))
		{
			$zipcode = $this->request->params['named']['zipcode'];
		}
		else if (!empty($this->request->query['zipcode']))
		{
			$zipcode = $this->request->query['zipcode'];
		}
		
		
		// Checks if the product quantity was specified.
		
		if (!empty($this->request->params['named']['quantity']))
		{
			$quantity = intval($this->request->params['named']['quantity']);
		}
		else if (!empty($this->request->query['quantity']))
		{
			$quantity = intval($this->request->query['quantity']);
		}
		else
		{
			$quantity = 1;
		}
		
		if (!is_numeric($quantity) || (is_numeric($quantity) && $quantity < 1))
		{
			$quantity = 1;
		}
		
		
		// Checks if the delivery company was specified.
		
		if (!empty($this->request->params['named']['company']))
		{
			$company = $this->request->params['named']['company'];
		}
		else if (!empty($this->request->query['company']))
		{
			$company = $this->request->query['company'];
		}
		
		
		// Checks if the delivery company service was specified.
		
		if (!empty($this->request->params['named']['service']))
		{
			$service = $this->request->params['named']['service'];
		}
		else if (!empty($this->request->query['service']))
		{
			$service = $this->request->query['service'];
		}
		
		
		// Checks if the specified product exists.
		
		$product = $this->__product($product);
		
		if (!empty($product))
		{
			$product = $this->StoreProduct->find('first',
				array(
					'contain' => array(
						'Store' => array(
							'Address'
						),
						'Product' => array(
							'Category' => array(
								'Delivery'
							)
						),
					),
					'conditions' => array(
						'StoreProduct.id' => $product['StoreProduct']['id']
					)
				)
			);
		}
				
		
		// Checks if the specified product exists and its category accepts convencional shipping methods.
		
		if (!empty($product['Store']['Address']['zipcode']) && !empty($product['Product']['Category']['Delivery']['id']) && !empty($zipcode))
		{
			// Gets the delivery information.
		
			$delivery_weight = $product['Product']['Category']['Delivery']['weight'];
			
			
			$delivery_width = $product['Product']['Category']['Delivery']['width'];
			
			if ($delivery_width < 11)
			{
				$delivery_width = 11;
			}
			
			
			$delivery_height = $product['Product']['Category']['Delivery']['height'];
			
			if ($delivery_height < 2)
			{
				$delivery_height = 2;
			}
			
			
			$delivery_length = $product['Product']['Category']['Delivery']['length'];
			
			if ($delivery_length < 16)
			{
				$delivery_length = 16;
			}
			
			
			// Communicates with the external server responsible for calculating delivery times and prices.	
			
			$HttpSocket = new HttpSocket();
			
			$results = $HttpSocket->get('http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx',
				array(
					'nCdEmpresa' => Configure::read('Correios.login.company'),
					'sDsSenha' => Configure::read('Correios.login.password'),
					'sCepOrigem' => $product['Store']['Address']['zipcode'],
					'sCepDestino' => $zipcode,
					'nVlPeso' => number_format($delivery_weight * $quantity, 2, ',', ''),
					'nVlLargura' => number_format($delivery_width, 2, ',', ''),
					'nVlAltura' => number_format($delivery_height * $quantity, 2, ',', ''),
					'nVlComprimento' => number_format($delivery_length, 2, ',', ''),
					'nCdFormato' => $product['Product']['Category']['Delivery']['format'],
					'sCdMaoPropria' => 'n',
					'nVlValorDeclarado' => 0,
					'sCdAvisoRecebimento' => 'n',
					'nCdServico' => implode(',', array_keys(Configure::read('Correios.service'))),
					'nVlDiametro' => 0,
					'StrRetorno' => 'xml'
				)
			);
				
				
			try
			{
				$xmlArray = Xml::toArray(Xml::build($results->body));
				
				if (!empty($xmlArray['Servicos']['cServico']))
				{
					foreach ($xmlArray['Servicos']['cServico'] as $key => $value)
					{
						if ($value['Erro'] == 0)
						{
							// Converts the returned delivery price to a more suitable format.
							
							$price = floatval(str_replace(',', '.', $value['Valor']));
							
							
							// Calculates the fees charged by paypal.
							
							$tax =  $price*((Configure::read('Paypal.payment.tax') / 100)/(1-(Configure::read('Paypal.payment.tax') / 100)));
							
							// Checks if a specific service was requested and if so, returns this one.
							
							if (!empty($service) && strcasecmp($service, $value['Codigo']) == 0)
							{
								$delivery = array(
									'id' => $value['Codigo'],
									'price' => number_format($price+$tax, 2, ',', ''),
									'days' => $value['PrazoEntrega'],
									'service' => Configure::read('Correios.service.' . $value['Codigo'])
								);
							}
							else if (empty($service))
							{
								$delivery[] = array(
									'id' => $value['Codigo'],
									'price' => number_format($price+$tax, 2, ',', ''),
									'days' => $value['PrazoEntrega'],
									'service' => Configure::read('Correios.service.' . $value['Codigo'])
								);
							}
						}
					}	
				}
			}
			catch (XmlException $e)
			{
			}
		}
		
		
		$this->set(compact('delivery'));
		$this->set('_serialize', array('delivery'));
		
		return $delivery;
	}	
}