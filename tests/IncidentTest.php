<?php

require 'vendor/autoload.php';

use Statusio\StatusioClient;

class IncidentTest extends PHPUnit_Framework_TestCase {
    private $statusioClient;

    public function __construct()
    {
        // Setup
        $this->statusioClient = new StatusioClient(getenv('API_ID'), getenv('API_KEY'));
    }

    public function testIncidentCreate() {
        $response = $this->statusioClient->IncidentCreate(getenv('STATUSPAGE_ID'), 'Incident', 'Details',
            [getenv('COMPONENT')], [getenv('CONTAINER')], StatusioClient::STATUS_OPERATIONAL,
            StatusioClient::STATE_IDENTIFIED, StatusioClient::NOTIFY_SLACK + StatusioClient::NOTIFY_HIPCHAT);

        $this->assertEquals('no', $response->status->error);
        return $response->result;
    }

    /**
     * @depends testIncidentCreate
     */
    public function testIncidentList($incident_id) {
        $response = $this->statusioClient->IncidentList(getenv('STATUSPAGE_ID'));
        $this->assertEquals('no', $response->status->error);
        $this->assertEquals($incident_id, $response->result->active_incidents[0]->_id);
        return array($incident_id, $response->result->active_incidents[0]->messages[0]->_id);
    }

    /**
     * @depends testIncidentList
     */
    public function testIncidentMessage($data) {
        list($incident_id, $message_id) = $data;
        $response = $this->statusioClient->IncidentMessage(getenv('STATUSPAGE_ID'), $message_id);
        $this->assertEquals('no', $response->status->error);
        return $incident_id;
    }

    /**
     * @depends testIncidentMessage
     */
    public function testIncidentUpdate($incident_id) {
        $response = $this->statusioClient->IncidentUpdate(getenv('STATUSPAGE_ID'), $incident_id, 'Update', StatusioClient::STATUS_DEGRADED_PERFORMANCE, StatusioClient::STATE_MONITORING);
        $this->assertEquals('no', $response->status->error);
        return $incident_id;
    }

    /**
     * @depends testIncidentUpdate
     */
    public function testIncidentResolve($incident_id) {
        $response = $this->statusioClient->IncidentResolve(getenv('STATUSPAGE_ID'), $incident_id, 'Resolve', StatusioClient::STATUS_DEGRADED_PERFORMANCE, StatusioClient::STATE_MONITORING);
        $this->assertEquals('no', $response->status->error);
        return $incident_id;
    }

    /**
     * @depends testIncidentResolve
     */
    public function testIncidentDelete($incident_id) {
        $response = $this->statusioClient->IncidentDelete(getenv('STATUSPAGE_ID'), $incident_id);
        $this->assertEquals('no', $response->status->error);
        return $incident_id;
    }
}