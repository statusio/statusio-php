<?php

require 'vendor/autoload.php';

use Statusio\StatusioClient;

class ComponentTest extends PHPUnit_Framework_TestCase {
    private $statusioClient;

    public function __construct()
    {
        // Setup
        $this->statusioClient = new StatusioClient(getenv('API_ID'), getenv('API_KEY'));
    }

    public function testComponentList() {
        $response = $this->statusioClient->ComponentList(getenv('STATUSPAGE_ID'));
        $this->assertEquals('no', $response->status->error);
    }

    public function testComponentStatusUpdate() {
        $response = $this->statusioClient->ComponentStatusUpdate(getenv('STATUSPAGE_ID'), [getenv('COMPONENT')], [getenv('CONTAINER')], 'Autotest', 300);
        $this->assertEquals('no', $response->status->error);
    }
}