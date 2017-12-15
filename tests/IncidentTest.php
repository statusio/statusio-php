<?php

require 'vendor/autoload.php';

use Statusio\StatusioClient;

class IncidentTest extends PHPUnit_Framework_TestCase {
    private $statusioClient;

    public function __construct()
    {
        // Setup
        $this->statusioClient = new StatusioClient('', '');
    }

    public function testIncidentCreate() {
        $response = $this->statusioClient->IncidentCreate('568d8a3e3cada8c2490000dd', 'Incident', 'Details',
            ['568d8a3e3cada8c2490000ed'], ['568d8a3e3cada8c2490000ec'], StatusioClient::STATUS_OPERATIONAL,
            StatusioClient::STATE_IDENTIFIED, StatusioClient::NOTIFY_SLACK + StatusioClient::NOTIFY_HIPCHAT);

        $this->assertEquals('no', $response->status->error);
        return $response->result;
    }

    /**
     * @depends testIncidentCreate
     */
    public function testIncidentList($incident_id) {
        $response = $this->statusioClient->IncidentList('568d8a3e3cada8c2490000dd');
        $this->assertEquals('no', $response->status->error);
        $this->assertEquals($incident_id, $response->result->active_incidents[0]->_id);
        return array($incident_id, $response->result->active_incidents[0]->messages[0]->_id);
    }

    /**
     * @depends testIncidentList
     */
    public function testIncidentMessage($data) {
        list($incident_id, $message_id) = $data;
        $response = $this->statusioClient->IncidentMessage('568d8a3e3cada8c2490000dd', $message_id);
        $this->assertEquals('no', $response->status->error);
        return $incident_id;
    }

    /**
     * @depends testIncidentMessage
     */
    public function testIncidentUpdate($incident_id) {
        $response = $this->statusioClient->IncidentUpdate('568d8a3e3cada8c2490000dd', $incident_id, 'Update', StatusioClient::STATUS_DEGRADED_PERFORMANCE, StatusioClient::STATE_MONITORING);
        $this->assertEquals('no', $response->status->error);
        return $incident_id;
    }

    /**
     * @depends testIncidentUpdate
     */
    public function testIncidentResolve($incident_id) {
        $response = $this->statusioClient->IncidentResolve('568d8a3e3cada8c2490000dd', $incident_id, 'Resolve', StatusioClient::STATUS_DEGRADED_PERFORMANCE, StatusioClient::STATE_MONITORING);
        $this->assertEquals('no', $response->status->error);
        return $incident_id;
    }

    /**
     * @depends testIncidentResolve
     */
    public function testIncidentDelete($incident_id) {
        $response = $this->statusioClient->IncidentDelete('568d8a3e3cada8c2490000dd', $incident_id);
        $this->assertEquals('no', $response->status->error);
        return $incident_id;
    }
}