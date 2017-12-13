<?php

require 'vendor/autoload.php';

use Statusio\StatusioClient;

class SubscriberTest extends PHPUnit_Framework_TestCase {
    private $statusioClient;

    public function __construct()
    {
        // Setup
        $this->statusioClient = new StatusioClient(getenv('API_ID'), getenv('API_KEY'));
    }

    public function testSubscriberAdd() {
        $response = $this->statusioClient->SubscriberAdd(getenv('STATUSPAGE_ID'), 'email', 'apitest@example.com');
        $this->assertEquals('no', $response->status->error);
        return $response->subscriber_id;
    }

    /**
     * @depends testSubscriberAdd
     */
    public function testSubscriberList($subscriber_id) {
        $response = $this->statusioClient->SubscriberList(getenv('STATUSPAGE_ID'));
        $this->assertEquals('no', $response->status->error);
        $this->assertEquals($subscriber_id, $response->result->email[0]->_id);
        return $subscriber_id;
    }

    /**
     * @depends testSubscriberList
     */
    public function testSubscriberUpdate($subscriber_id) {
        $response = $this->statusioClient->SubscriberUpdate(getenv('STATUSPAGE_ID'), $subscriber_id, 'apitest@example.com');
        $this->assertEquals('no', $response->status->error);
        return $subscriber_id;
    }

    /**
     * @depends testSubscriberUpdate
     */
    public function testSubscriberRemove($subscriber_id) {
        $response = $this->statusioClient->SubscriberRemove(getenv('STATUSPAGE_ID'), $subscriber_id);
        $this->assertEquals('no', $response->status->error);
        return $subscriber_id;
    }
}