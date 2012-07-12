<?php

class MagentoGoSource extends DataSource {

/**
 * An optional description of your datasource
 */
    public $description = 'MagentoGo datasource';

/**
 * Our default config options. These options will be customized in our
 * ``app/Config/database.php`` and will be merged in the ``__construct()``.
 */
    public $config = array(
		'url' => 'http://simplegarden.gostorego.com/api/soap/?wsdl',
		'apiuser' => 'bvanskyh',
		'apikey' => '7a93d55b84a176935237e087e30bf94ff730a8e2792c926f9c4b53ea4f9c8f14',
    );

/**
 * If we want to create() or update() we need to specify the fields
 * available. We use the same array keys as we do with CakeSchema, eg.
 * fixtures and schema migrations.
 */
    protected $_schema = array(
        'id' => array(
            'type' => 'integer',
            'null' => false,
            'key' => 'primary',
            'length' => 11,
        ),
    );

	protected $_client = null;
	protected $_session = null;
	
/**
 * Create our  and handle any config tweaks.
 */
	public function __construct($config) 
	{
		parent::__construct($config);
		$this->_startSession();
	}

/**
 * Since datasources normally connect to a database there are a few things
 * we must change to get them to work without a database.
 */

/**
 * listSources() is for caching. You'll likely want to implement caching in
 * your own way with a custom datasource. So just ``return null``.
 */
    public function listSources() {
        return null;
    }

/**
 * describe() tells the model your schema for ``Model::save()``.
 *
 * You may want a different schema for each model but still use a single
 * datasource. If this is your case then set a ``schema`` property on your
 * models and simply return ``$Model->schema`` here instead.
 */
    public function describe(Model $Model) {
        return $Model->schema;
    }

/**
 * calculate() is for determining how we will count the records and is
 * required to get ``update()`` and ``delete()`` to work.
 *
 * We don't count the records here but return a string to be passed to
 * ``read()`` which will do the actual counting. The easiest way is to just
 * return the string 'COUNT' and check for it in ``read()`` where
 * ``$data['fields'] == 'COUNT'``.
 */
    public function calculate(Model $Model, $func, $params = array()) {
        return 'COUNT';
    }

/**
 * Implement the R in CRUD. Calls to ``Model::find()`` arrive here.
 */
	public function read(Model $Model, $data = array()) {
	
		$this->_startSession();
		
		/**
		 * Here we do the actual count as instructed by our calculate()
		 * method above. We could either check the remote source or some
		 * other way to get the record count. Here we'll simply return 1 so
		 * ``update()`` and ``delete()`` will assume the record exists.
		 */ 
		if ($data['fields'] == 'COUNT') {
			return array(array(array('count' => 1)));
		}
		
		/**
		 * Now we get, decode and return the remote data.
		 */
		if (empty($data['action'])) $data['action'] = 'list';
		$action = $Model->table . '.' . $data['action'];
		$res = $this->_client->call($this->_session, $action, $data['conditions']);
		return array($Model->alias => $res);
	}

/**
 * Implement the C in CRUD. Calls to ``Model::save()`` without $Model->id
 * set arrive here.
 */
	public function create(Model $Model, $fields = array(), $values = array()) {
		$data = array_combine($fields, $values);
		if (empty($data['action']))
		{
			$data['action'] = 'update';	
		}
		$action = $Model->table . '.' . $data['action'];
		$primary = $Model->primaryKey;
		$id = $Model->id;
		$res = $this->_client->call($this->_session, $action, array($primary => $id, $data));
		if ($res != 1) 
		{
			throw new CakeException($res);
		}
		return true;
    }

/**
 * Implement the U in CRUD. Calls to ``Model::save()`` with $Model->id
 * set arrive here. Depending on the remote source you can just call
 * ``$this->create()``.
 */
    public function update(Model $Model, $fields = array(), $values = array()) {
        return $this->create($Model, $fields, $values);
    }

/**
 * Implement the D in CRUD. Calls to ``Model::delete()`` arrive here.
 */
    public function delete(Model $Model, $conditions = null) {
		throw new CakeException('Not Implemented');
		$id = $conditions[$Model->alias . '.id'];
		return true;
    }

	/**
	 * Create a soap session
	 */
	protected function _startSession()
	{
		if (is_null($this->_client))
		{
			$this->_client = new SoapClient($this->config['url']);			
		}
		if (is_null($this->_session))
		{
			$this->_session = $this->_client->login($this->config['apiuser'], $this->config['apikey']);			
		}		
		pr($this->_client->__getFunctions());
	}

}