<?php

// Controller/Component/OAuthConsumers/TwitterConsumer.php
class TwitterConsumer extends AbstractConsumer {
    public function __construct() {
        parent::__construct('YOUR_CONSUMER_KEY', 'YOUR_CONSUMER_SECRET');
    }
}
