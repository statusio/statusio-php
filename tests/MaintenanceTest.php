<?php

require 'vendor/autoload.php';

use Statusio\StatusioClient;

class MaintenanceTest extends PHPUnit_Framework_TestCase {
    private $statusioClient;

    public function __construct()
    {
        // Setup
        $this->statusioClient = new StatusioClient('', '');
    }

    public function testMaintenanceSchedule() {
        $response = $this->statusioClient->MaintenanceSchedule('568d8a3e3cada8c2490000dd', 'Maintenance', 'Details',
            ['568d8a3e3cada8c2490000ed-568d8a3e3cada8c2490000ec'], '2018-12-31', '23:59', '2019-01-01', '23:59');
        $this->assertEquals('no', $response->status->error);
        return $response->result;
    }

    /**
     * @depends testMaintenanceSchedule
     */
    public function testMaintenanceList($maintenance_id) {
        $response = $this->statusioClient->MaintenanceList('568d8a3e3cada8c2490000dd');
        $this->assertEquals('no', $response->status->error);
        $this->assertEquals($maintenance_id, $response->result->upcoming_maintenances[0]->_id);
        return array($maintenance_id, $response->result->upcoming_maintenances[0]->messages[0]->_id);
    }

    /**
     * @depends testMaintenanceList
     */
    public function testMaintenanceMessage($data) {
        list($maintenance_id, $message_id) = $data;
        $response = $this->statusioClient->MaintenanceMessage('568d8a3e3cada8c2490000dd', $message_id);
        $this->assertEquals('no', $response->status->error);
        return $maintenance_id;
    }

    /**
     * @depends testMaintenanceMessage
     */
    public function testMaintenanceStart($maintenance_id) {
        $response = $this->statusioClient->MaintenanceStart('568d8a3e3cada8c2490000dd', $maintenance_id, 'Start');
        $this->assertEquals('no', $response->status->error);
        return $maintenance_id;
    }

    /**
     * @depends testMaintenanceStart
     */
    public function testMaintenanceUpdate($maintenance_id) {
        $response = $this->statusioClient->MaintenanceUpdate('568d8a3e3cada8c2490000dd', $maintenance_id, 'UPDATE');
        $this->assertEquals('no', $response->status->error);
        return $maintenance_id;
    }

    /**
     * @depends testMaintenanceUpdate
     */
    public function testMaintenanceFinish($maintenance_id) {
        $response = $this->statusioClient->MaintenanceFinish('568d8a3e3cada8c2490000dd', $maintenance_id, 'UPDATE');
        $this->assertEquals('no', $response->status->error);
        return $maintenance_id;
    }

    /**
     * @depends testMaintenanceFinish
     */
    public function testIncidentDelete($maintenance_id) {
        $response = $this->statusioClient->MaintenanceDelete('568d8a3e3cada8c2490000dd', $maintenance_id);
        $this->assertEquals('no', $response->status->error);
        return $maintenance_id;
    }
}