<?php
App::uses('AppController', 'Controller');
//App::uses('Xml', 'Utility');
//App::uses('HttpSocket', 'Network/Http');

/**
 * Portkey Controller
 *
 * @property Portkey $Portkey
 */
class PortkeyController extends AppController {
	
	public function beforeFilter()
	{
		$this->Auth->allow('*');
	}
	
	public function magento()
	{
		$this->loadModel('GoSalesOrder');
		
		$start = microtime(true);
		$orders = $this->GoSalesOrder->find('all');
		echo 'ORDERS: ', microtime(true) - $start, '<br>';
		
		$start = microtime(true);
		$order = $this->GoSalesOrder->find('all', array('action' => 'info', 'conditions' => '100000014'));
		echo 'ORDER: ', microtime(true) - $start, '<br>';

		$start = microtime(true);
		$orders = $this->GoSalesOrder->find('all');
		echo 'ORDERS: ', microtime(true) - $start, '<br>';
		
		$this->set(compact('orders'));
	}
	
	public function go_inventory()
	{
		$this->loadModel('GoInventory');
		$items = $this->GoInventory->find('all');
		
		pr($items);
	}
	
	public function index()
	{		
		$this->loadModel('GetCompleteInventory');	
		$tmp = $this->GetCompleteInventory->getInventory();
		$items = Hash::combine($tmp['GetCompleteInventory']['InventoryList']['ItemInventory'], '{n}.PartNumber', '{n}');
		
		$this->loadModel('Magento');
		$goskus = Hash::extract($this->Magento->query('product.list'), '{n}.sku');
		$go = $this->Magento->query('product_stock.list', array($goskus));
		foreach ($go as $g)
		{
			$sku = strtoupper($g['sku']);
			$goitems[$sku] = array(
				'PartNumber' => $sku,
				'WebQuantity' => $g['qty'],
				'product_id' => $g['product_id'],
				'is_in_stock' => $g['is_in_stock'],
				'Quantity' => -1,
				'QuantityOnHold' => -1,
				'QuantityAllocated' => -1,
				'QuantityRequired' => -1,
				'QuantityScheduled' => -1,
				'QuantityTotal' => -1
			);
		}
		
		$combined = Hash::sort(Hash::merge($goitems, $items), '{s}.PartNumber', 'asc');

		$cached = $this->GetCompleteInventory->cached;
		$this->set(compact('combined', 'cached'));
	}
	
	/**
	 * Update inventory
	 */
	public function save()
	{
		/*  for testing
		$this->request->data['id'] = 224;
		$this->request->data['value'] = 200;
		*/
		
		$id = $this->request->data['id'];
		$value = $this->request->data['value'];		
		$record = array('product' => $id, array('qty' => $value));
		
		$this->loadModel('Magento');
		if ($this->Magento->query('product_stock.update', $record))
		{
			Cache::delete('inventory', 'short');
			echo $value;
		}
		$this->layout = null;
	}
	
	function reference() {
		//$this->Portkey->query('SoapMethod', array('mySoapParams'));	
$xmlArray = array(
    'project' => array(
        '@id' => 1,
        'name' => 'Name of project, as tag',
        '@' => 'Value of project'
    )
);
$xmlObject = Xml::fromArray($xmlArray);
$xmlString = $xmlObject->asXML();
//echo $xmlString;


$params = array('user' => 'fertile', 'pass' => 'burEcha3');
	$xmlpost = '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
	$xmlpost .= '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">'."\n";
	$xmlpost .= '  <soap:Header>'."\n";
	$xmlpost .= '    <AuthHeader xmlns="http://www.integracoreb2b.com/">'."\n";
	$xmlpost .= '      <Username>'.$params['user'].'</Username>'."\n";
	$xmlpost .= '      <Password>'.$params['pass'].'</Password>'."\n";
	//$xmlpost .= '      <Test>boolean</Test>'."\n";
	$xmlpost .= '    </AuthHeader>'."\n";
	$xmlpost .= '  </soap:Header>'."\n";
	$xmlpost .= '  <soap:Body>'."\n";
	$xmlpost .= '    <GetCompleteInventory xmlns="http://www.integracoreb2b.com/" />'."\n";
	$xmlpost .= '  </soap:Body>'."\n";
	$xmlpost .= '</soap:Envelope>'."\n";
	$url='https://www.integracoreb2b.com/IntCore/Inventory.asmx';
	$header['header']['Content-Type'] = 'text/xml';

$xmlArray = Xml::toArray(Xml::build($xmlpost));
//pr($xmlArray); exit;

	$test = array(
    	'soap:Envelope' => array(
    		'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
    		'xmlns:xsd' => "http://www.w3.org/2001/XMLSchema",
    		'xmlns:soap' => "http://schemas.xmlsoap.org/soap/envelope/",
            'soap:Header' => array(
                'AuthHeader' => array(
                	'@xmlns' => "http://www.integracoreb2b.com/",
                    'Username' => 'fertile',
                    'Password' => 'burEcha3',
                    'Test' => 1,
                     )
                ),
            	'soap:Body' => array(
                  'GetCompleteInventory' => array(
                  	'@xmlns' => "http://www.integracoreb2b.com/",	
	                )
            	)
        	)
		);

	$xml = Xml::build($test);
	$xmlpost = $xml->asXML();
	//pr($results); exit;

$this->layout = '';
//$results = $xmlpost;
$HttpSocket = new HttpSocket();
$results = $HttpSocket->post($url, $xmlpost, $header);

//pr($results->body); exit;
pr(Xml::toArray(Xml::build($results->body)));
exit;

$this->set(compact('results'));
	}
}
