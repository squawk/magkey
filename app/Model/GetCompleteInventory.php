<?php
class GetCompleteInventory extends AppModel {
	public $useDbConfig = 'portkey';
	
	public $cached;

	public function getInventory()
	{
		$this->cached = true;
		$result = Cache::read('inventory', 'short');
		if (!$result) 
		{
			$this->cached = false;
			$result = $this->find('all', array('order' => 'Post.updated DESC', 'limit' => 10));
			Cache::write('inventory', $result, 'short');
		}
		return $result;
	}
}