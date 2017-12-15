<?php

require 'vendor/autoload.php';

use Statusio\StatusioClient;

class StatusTest extends PHPUnit_Framework_TestCase {
    private $statusioClient;

    public function __construct()
    {
        // Setup
        $this->statusioClient = new StatusioClient('', '');
    }

    public function testStatusSummary() {
        $response = $this->statusioClient->StatusSummary('568d8a3e3cada8c2490000dd');
        $this->assertEquals('no', $response->status->error);
    }
}