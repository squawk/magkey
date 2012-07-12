<?php

// Controller/ExampleController.php
class ExampleController extends AppController {
	public $components = array('OAuthConsumer');

	public function index() {
		$requestToken = $this->OAuthConsumer->getRequestToken('Magento', 'http://simplegarden.gostorego.com/oauth/initiate', 'http://' . $_SERVER['HTTP_HOST'] . '/example/callback');

		if ($requestToken) {
			$this->Session->write('magento_request_token', $requestToken);
			$this->redirect('http://simplegarden.gostorego.com/oauth/authorize?oauth_token=' . $requestToken->key);
		} else {
			// an error occured when obtaining a request token
			echo 'Error';
		}
	}

	public function callback() {
		$requestToken = $this->Session->read('magento_request_token');
		$accessToken = $this->OAuthConsumer->getAccessToken('Magento', 'https://simplegarden.gostorego.com/oauth/token', $requestToken);

		pr($accessToken);
		if ($accessToken) {
//            $this->OAuthConsumer->post('Magento', $accessToken->key, $accessToken->secret, 'https://simplegarden.gostorego.com/1/statuses/update.json', array('status' => 'hello world!'));
		}
	}
}
