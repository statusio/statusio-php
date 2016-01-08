<?php

require 'vendor/autoload.php';

use Statusio\StatusioClient;

class StatusTest extends PHPUnit_Framework_TestCase {
    private $statusioClient;

    public function __construct()
    {
        // Setup
        $this->statusioClient = new StatusioClient(getenv('API_ID'), getenv('API_KEY'));
    }

    public function testStatusSummary() {
        $response = $this->statusioClient->StatusSummary(getenv('STATUSPAGE_ID'));
        $this->assertEquals('no', $response->status->error);
    }
}