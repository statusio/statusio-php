<?php

require 'vendor/autoload.php';

use Statusio\StatusioClient;



class ComponentTest extends PHPUnit_Framework_TestCase {
    private $statusioClient;

    public function __construct()
    {
        // Setup
        $this->statusioClient = new StatusioClient('', '');
    }

    public function testComponentList() {
        $response = $this->statusioClient->ComponentList('568d8a3e3cada8c2490000dd');
        $this->assertEquals('no', $response->status->error);
    }

    public function testComponentStatusUpdate() {
        $response = $this->statusioClient->ComponentStatusUpdate('568d8a3e3cada8c2490000dd', '568d8a3e3cada8c2490000ed', '568d8a3e3cada8c2490000ec', 'Autotest', 300);
        $this->assertEquals('no', $response->status->error);
    }
}