<?php
class GoProductStock extends AppModel {
	public $useDbConfig = 'magentogo';
	public $useTable = 'product_stock';
	public $primaryKey = 'product';
	public $schema = array(
       'product' => array(
           'type' => 'integer',
           'null' => false,
           'key' => 'primary',
           'length' => 11,
       ),
       'qty' => array(
           'type' => 'string',
           'null' => true,
       ),
   );
   
}