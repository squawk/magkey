<?php
/**
 * SOAP Datasource
 *
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * MagentoSource
 *
 */
class MagentoSource extends DataSource {
    
/**
 * Description
 *
 * @var string
 */
	public $description = 'Magento DataSource';

/**
 * SoapClient instance
 *
 * @var SoapClient
 */
	public $client = null;

/**
 * session status
 *
 * @var mixed
 */
	public $session = null;

/**
 * Connection status
 *
 * @var boolean
 */
	public $connected = false;

/**
 * Default configuration
 *
 * @var array
 */
	public $_baseConfig = array(
		'url' => '',
		'apiuser' => '',
		'apikey' => '',
	);
/**
 * Constructor
 *
 * @param array $config An array defining the configuration settings
 */
	public function __construct($config) {
		parent::__construct($config);
		$this->connect();
	}

/**
 * Connects to the SOAP server using the WSDL in the configuration
 *
 * @param array $config An array defining the new configuration settings
 * @return boolean True on success, false on failure
 */ 
	public function connect() {
		try {
			$this->client = new SoapClient($this->config['url']);
		} catch(SoapFault $fault) {
			$this->error = $fault->faultstring;
			$this->showError();
		}		
		$this->session = $this->client->login($this->config['apiuser'], $this->config['apikey']);

		if ($this->client and $this->session) {
			$this->connected = true;
		}
		return $this->connected;
	}

/**
 * Sets the SoapClient instance to null
 *
 * @return boolean True
 */
	public function close() {
		$this->client->endSession($this->session);
		$this->client = null;
		$this->session = null;
		$this->connected = false;
		return true;
	}

/**
 * Returns the available SOAP methods
 *
 * @return array List of SOAP methods
 */
	public function listSources() {
		return $this->client->__getFunctions();
	}
	
/**
 * Query the SOAP server with the given method and parameters
 *
 * @return mixed Returns the result on success, false on failure
 */
	public function query() {
		$this->error = false;
		if (!$this->connected) {
			return false;
		}

		$args = func_get_args();
		$method = null;
		$queryData = null;

		if (count($args) == 1) {
			$method = $args[0];
			$queryData = null;
		} elseif (count($args) == 2) {
			$method = $args[0];
			$queryData = $args[1];
		} elseif (count($args) > 2 && !empty($args[1])) {
			$method = $args[0];
			$queryData = $args[1][0];
		} else {
			return false;
		}
		
		try {
			$result = $this->client->call($this->session, $method, $queryData);
		} catch (SoapFault $fault) {
			$this->error = $fault->faultstring;
			$this->showError();
			return false;
		}
		return $result;
	}

/**
 * Returns the last SOAP response
 *
 * @return string The last SOAP response
 */
	public function getResponse() {
		return $this->client->__getLastResponse();
	}

/**
 * Returns the last SOAP request
 *
 * @return string The last SOAP request
 */
	public function getRequest() {
		return $this->client->__getLastRequest();
	}

/**
 * Shows an error message and outputs the SOAP result if passed
 *
 * @param string $result A SOAP result
 * @return string The last SOAP response
 */
	public function showError($result = null) {
		if (Configure::read() > 0) {
			if ($this->error) {
				trigger_error('<span style = "color:Red;text-align:left"><b>SOAP Error:</b> ' . $this->error . '</span>', E_USER_WARNING);
			}
			if (!empty($result)) {
				echo sprintf("<p><b>Result:</b> %s </p>", $result);
			}
		}
	}
}
