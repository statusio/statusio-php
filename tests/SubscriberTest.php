<?php

require 'vendor/autoload.php';

use Statusio\StatusioClient;

class SubscriberTest extends PHPUnit_Framework_TestCase {
    private $statusioClient;

    public function __construct()
    {
        // Setup
        $this->statusioClient = new StatusioClient('', '');
    }

    public function testSubscriberAdd() {
        $response = $this->statusioClient->SubscriberAdd('568d8a3e3cada8c2490000dd', 'email', 'apitest@example.com');
        $this->assertEquals('no', $response->status->error);
        return $response->subscriber_id;
    }

    /**
     * @depends testSubscriberAdd
     */
    public function testSubscriberList($subscriber_id) {
        $response = $this->statusioClient->SubscriberList('568d8a3e3cada8c2490000dd');
        $this->assertEquals('no', $response->status->error);
        $this->assertEquals($subscriber_id, $response->result->email[0]->_id);
        return $subscriber_id;
    }

    /**
     * @depends testSubscriberList
     */
    public function testSubscriberUpdate($subscriber_id) {
        $response = $this->statusioClient->SubscriberUpdate('568d8a3e3cada8c2490000dd', $subscriber_id, 'apitest@example.com');
        $this->assertEquals('no', $response->status->error);
        return $subscriber_id;
    }

    /**
     * @depends testSubscriberUpdate
     */
    public function testSubscriberRemove($subscriber_id) {
        $response = $this->statusioClient->SubscriberRemove('568d8a3e3cada8c2490000dd', $subscriber_id);
        $this->assertEquals('no', $response->status->error);
        return $subscriber_id;
    }
}