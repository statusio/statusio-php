<?php

namespace Statusio;

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception;

class StatusioClient {
    const STATUS_OPERATIONAL = 100;
    const STATUS_DEGRADED_PERFORMANCE = 300;
    const STATUS_PARTIAL_SERVICE_DISRUPTION = 400;
    const STATUS_SERVICE_DISRUPTION = 500;
    const STATUS_SECURITY_EVENT = 600;

    const STATE_INVESTIGATING = 100;
    const STATE_IDENTIFIED = 200;
    const STATE_MONITORING = 300;

    const NOTIFY_EMAIL = 1;
    const NOTIFY_SMS = 2;
    const NOTIFY_WEBHOOK = 4;
    const NOTIFY_SOCIAL = 8;
    const NOTIFY_IRC = 16;
    const NOTIFY_HIPCHAT = 32;
    const NOTIFY_SLACK = 64;

    private $guzzleClient;
    private $apiId;
    private $apiKey;

    function __construct($api_id, $api_key)
    {
        $this->apiId = $api_id;
        $this->apiKey = $api_key;

        $this->guzzleClient = new Client([
            'base_uri' => 'https://api.status.io/v2/',
            'headers' => [
                'x-api-id' => $this->apiId,
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'verify' => false
        ]);
    }

    private function getNotify($notifications) {
        $notify = [
            'notify_email' => "0",
            'notify_sms' => "0",
            'notify_webhook' => "0",
            'social' => "0",
            'irc' => "0",
            'hipchat' => "0",
            'slack' => "0"
        ];
        if(($notifications & StatusioClient::NOTIFY_EMAIL) == StatusioClient::NOTIFY_EMAIL) $notify['notify_email'] = "1";
        if(($notifications & StatusioClient::NOTIFY_SMS) == StatusioClient::NOTIFY_SMS) $notify['notify_sms'] = "1";
        if(($notifications & StatusioClient::NOTIFY_WEBHOOK) == StatusioClient::NOTIFY_WEBHOOK) $notify['notify_webhook'] = "1";
        if(($notifications & StatusioClient::NOTIFY_SOCIAL) == StatusioClient::NOTIFY_SOCIAL) $notify['social'] = "1";
        if(($notifications & StatusioClient::NOTIFY_IRC) == StatusioClient::NOTIFY_IRC) $notify['irc'] = "1";
        if(($notifications & StatusioClient::NOTIFY_HIPCHAT) == StatusioClient::NOTIFY_HIPCHAT) $notify['hipchat'] = "1";
        if(($notifications & StatusioClient::NOTIFY_SLACK) == StatusioClient::NOTIFY_SLACK) $notify['slack'] = "1";
        return $notify;
    }

    // COMPONENT
    /**
     * List all components.
     *
     * @param string $statuspage_id Status page ID
     * @return object
     */
    public function ComponentList($statuspage_id) {
        $guzzleResponse = $this->guzzleClient->get('component/list/' . $statuspage_id);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Update the status of a component on the fly without creating an incident or maintenance.
     *
     * @param string $statuspage_id string Status page ID
     * @param array $components ID of each affected component
     * @param array $containers ID of each affected container
     * @param string $details A brief message describing this update
     * @param int $current_status Any numeric status code.
     * @return object
     */
    public function ComponentStatusUpdate($statuspage_id, $components, $containers, $details, $current_status) {
        $guzzleResponse = $this->guzzleClient->post('component/status/update', ['json' => [
            'statuspage_id' => $statuspage_id,
            'components' => $components,
            'containers' => $containers,
            'details' => $details,
            'current_status' => $current_status
        ]]);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    // INCIDENT

    /**
     * List all active and resolved incidents.
     *
     * @param string $statuspage_id Status page ID
     * @return object
     */
    public function IncidentList($statuspage_id) {
        $guzzleResponse = $this->guzzleClient->get('incident/list/' . $statuspage_id);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Display incident message.
     *
     * @param string $statuspage_id Status page ID
     * @param string $message_id Message ID
     * @return object
     */
    public function IncidentMessage($statuspage_id, $message_id) {
        $guzzleResponse = $this->guzzleClient->get('incident/message/' . $statuspage_id . '/' . $message_id);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Create a new incident.
     *
     * @param string $statuspage_id Status page ID
     * @param string $incident_name A descriptive title for the incident
     * @param string $incident_details Message describing this incident
     * @param array infrastructure_affected ID of each affected component and container combo
     * @param int $current_status The status of the components and containers affected by this incident (StatusioClient::STATUS_*).
     * @param int $current_state The state of this incident (StatusioClient::STATE_*).
     * @param int $notifications Bitmasked notifications (StatusioClient::NOTIFY_*). To use multiple just add them up (ie StatusioClient::NOTIFY_SMS + StatusioClient::NOTIFY_SLACK).
     * @param int $all_infrastructure_affected Affect all components and containers (default = 0)
     * @return object
     */
    public function IncidentCreate($statuspage_id, $incident_name, $incident_details, $infrastructure_affected,
                                   $current_status, $current_state, $notifications = 0, $all_infrastructure_affected = 0) {
        $data = $this->getNotify($notifications);
        $data['statuspage_id'] = $statuspage_id;
        $data['incident_name'] = $incident_name;
        $data['incident_details'] = $incident_details;
        $data['infrastructure_affected'] = $infrastructure_affected;
        $data['current_status'] = $current_status;
        $data['current_state'] = $current_state;
        $data['all_infrastructure_affected'] = $all_infrastructure_affected;

        $guzzleResponse = $this->guzzleClient->post('incident/create', ['json' => $data]);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Update an existing incident
     *
     * @param string $statuspage_id Status page ID
     * @param string $incident_id Incident ID
     * @param string $incident_details Message describing this incident update
     * @param int $current_status The status of the components and containers affected by this incident (StatusioClient::STATUS_*).
     * @param int $current_state The state of this incident (StatusioClient::STATE_*).
     * @param int $notifications Bitmasked notifications (StatusioClient::NOTIFY_*). To use multiple just add them up (ie StatusioClient::NOTIFY_SMS + StatusioClient::NOTIFY_SLACK).
     * @return object
     */
    public function IncidentUpdate($statuspage_id, $incident_id, $incident_details, $current_status, $current_state,
                                   $notifications = 0) {
        $data = $this->getNotify($notifications);
        $data['statuspage_id'] = $statuspage_id;
        $data['incident_id'] = $incident_id;
        $data['incident_details'] = $incident_details;
        $data['current_status'] = $current_status;
        $data['current_state'] = $current_state;

        $guzzleResponse = $this->guzzleClient->post('incident/update', ['json' => $data]);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Resolve an existing incident. The incident will be shown in the history instead of on the main page.
     *
     * @param string $statuspage_id Status page ID
     * @param string $incident_id Incident ID
     * @param string $incident_details Message describing this incident
     * @param int $current_status The status of the components and containers affected by this incident (StatusioClient::STATUS_*).
     * @param int $current_state The state of this incident (StatusioClient::STATE_*).
     * @param int $notifications Bitmasked notifications (StatusioClient::NOTIFY_*). To use multiple just add them up (ie StatusioClient::NOTIFY_SMS + StatusioClient::NOTIFY_SLACK).
     * @return object
     */
    public function IncidentResolve($statuspage_id, $incident_id, $incident_details, $current_status, $current_state,
                                   $notifications = 0) {
        $data = $this->getNotify($notifications);
        $data['statuspage_id'] = $statuspage_id;
        $data['incident_id'] = $incident_id;
        $data['incident_details'] = $incident_details;
        $data['current_status'] = $current_status;
        $data['current_state'] = $current_state;

        $guzzleResponse = $this->guzzleClient->post('incident/resolve', ['json' => $data]);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Delete an existing incident. The incident will be deleted forever and cannot be recovered.
     *
     * @param string $statuspage_id Status page ID
     * @param string $incident_id Incident ID
     * @return object
     */
    public function IncidentDelete($statuspage_id, $incident_id) {
        $data = [];
        $data['statuspage_id'] = $statuspage_id;
        $data['incident_id'] = $incident_id;

        $guzzleResponse = $this->guzzleClient->post('incident/delete', ['json' => $data]);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    // MAINTENANCE

    /**
     * List all active, resolved and upcoming maintenances
     *
     * @param string $statuspage_id Status page ID
     * @return object
     */
    public function MaintenanceList($statuspage_id) {
        $guzzleResponse = $this->guzzleClient->get('maintenance/list/' . $statuspage_id);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Display maintenance message
     *
     * @param string $statuspage_id Status page ID
     * @param string $message_id Message ID
     * @return object
     */
    public function MaintenanceMessage($statuspage_id, $message_id) {
        $guzzleResponse = $this->guzzleClient->get('maintenance/message/' . $statuspage_id . '/' . $message_id);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Schedule a new maintenance
     *
     * @param string $statuspage_id Status page ID
     * @param string $maintenance_name A descriptive title for this maintenance
     * @param string $maintenance_details Message describing this maintenance
     * @param array infrastructure_affected ID of each affected component and container combo
     * @param string $date_planned_start Date maintenance is expected to start
     * @param string $time_planned_start Time maintenance is expected to start
     * @param string $date_planned_end Date maintenance is expected to end
     * @param string $time_planned_end Time maintenance is expected to end
     * @param int $automation Automatically start and end the maintenance (default = 0)
     * @param int $all_infrastructure_affected Affect all components and containers (default = 0)
     * @param int $maintenance_notify_now Notify subscribers now (1 = Send notification)
     * @param int $maintenance_notify_1_hr Notify subscribers 1 hour before scheduled maintenance start time (1 = Send notification)
     * @param int $maintenance_notify_24_hr Notify subscribers 24 hours before scheduled maintenance start time (1 = Send notification)
     * @param int $maintenance_notify_72_hr Notify subscribers 72 hours before scheduled maintenance start time (1 = Send notification)
     * @return object
     */
    public function MaintenanceSchedule($statuspage_id, $maintenance_name, $maintenance_details, $infrastructure_affected,
                                        $date_planned_start, $time_planned_start, $date_planned_end, $time_planned_end,
                                        $automation = 0, $all_infrastructure_affected = 0,
                                        $maintenance_notify_now = 0, $maintenance_notify_1_hr = 0,
                                        $maintenance_notify_24_hr = 0, $maintenance_notify_72_hr = 0) {
        $data = [];
        $data['statuspage_id'] = $statuspage_id;
        $data['maintenance_name'] = $maintenance_name;
        $data['maintenance_details'] = $maintenance_details;
        $data['infrastructure_affected'] = $infrastructure_affected;
        $data['all_infrastructure_affected'] = $all_infrastructure_affected;
        $data['date_planned_start'] = $date_planned_start;
        $data['time_planned_start'] = $time_planned_start;
        $data['date_planned_end'] = $date_planned_end;
        $data['time_planned_end'] = $time_planned_end;
        $data['automation'] = $automation;
        $data['maintenance_notify_now'] = $maintenance_notify_now;
        $data['maintenance_notify_1_hr'] = $maintenance_notify_1_hr;
        $data['maintenance_notify_24_hr'] = $maintenance_notify_24_hr;
        $data['maintenance_notify_72_hr'] = $maintenance_notify_72_hr;

        $guzzleResponse = $this->guzzleClient->post('maintenance/schedule', ['json' => $data]);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Begin a scheduled maintenance now
     *
     * @param string $statuspage_id Status page ID
     * @param string $maintenance_id Maintenance ID
     * @param string $maintenance_details Message describing this maintenance update
     * @param int $notifications Bitmasked notifications (StatusioClient::NOTIFY_*). To use multiple just add them up (ie StatusioClient::NOTIFY_SMS + StatusioClient::NOTIFY_SLACK).
     * @return object
     */
    public function MaintenanceStart($statuspage_id, $maintenance_id, $maintenance_details, $notifications = 0) {
        $data = $this->getNotify($notifications);
        $data['statuspage_id'] = $statuspage_id;
        $data['maintenance_id'] = $maintenance_id;
        $data['maintenance_details'] = $maintenance_details;

        $guzzleResponse = $this->guzzleClient->post('maintenance/start', ['json' => $data]);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Update an active maintenance
     *
     * @param string $statuspage_id Status page ID
     * @param string $maintenance_id Maintenance ID
     * @param string $maintenance_details Message describing this maintenance
     * @param int $notifications Bitmasked notifications (StatusioClient::NOTIFY_*). To use multiple just add them up (ie StatusioClient::NOTIFY_SMS + StatusioClient::NOTIFY_SLACK).
     * @return object
     */
    public function MaintenanceUpdate($statuspage_id, $maintenance_id, $maintenance_details, $notifications = 0) {
        $data = $this->getNotify($notifications);
        $data['statuspage_id'] = $statuspage_id;
        $data['maintenance_id'] = $maintenance_id;
        $data['maintenance_details'] = $maintenance_details;

        $guzzleResponse = $this->guzzleClient->post('maintenance/update', ['json' => $data]);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Close an active maintenance. The maintenance will be moved to the history.
     *
     * @param string $statuspage_id Status page ID
     * @param string $maintenance_id Maintenance ID
     * @param string $maintenance_details Message describing this maintenance
     * @param int $notifications Bitmasked notifications (StatusioClient::NOTIFY_*). To use multiple just add them up (ie StatusioClient::NOTIFY_SMS + StatusioClient::NOTIFY_SLACK).
     * @return object
     */
    public function MaintenanceFinish($statuspage_id, $maintenance_id, $maintenance_details, $notifications = 0) {
        $data = $this->getNotify($notifications);
        $data['statuspage_id'] = $statuspage_id;
        $data['maintenance_id'] = $maintenance_id;
        $data['maintenance_details'] = $maintenance_details;

        $guzzleResponse = $this->guzzleClient->post('maintenance/finish', ['json' => $data]);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Delete an existing maintenance. The maintenance will be deleted forever and cannot be recovered.
     *
     * @param string $statuspage_id Status page ID
     * @param string $maintenance_id Maintenance ID
     * @return object
     */
    public function MaintenanceDelete($statuspage_id, $maintenance_id) {
        $data = [];
        $data['statuspage_id'] = $statuspage_id;
        $data['maintenance_id'] = $maintenance_id;

        $guzzleResponse = $this->guzzleClient->post('maintenance/delete', ['json' => $data]);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    // METRIC

    /**
     * Update custom metric data
     *
     * @param string $statuspage_id Status page ID
     * @param string $metric_id Metric ID
     * @param float $day_avg Average value for past 24 hours
     * @param int $day_start UNIX timestamp for start of metric timeframe
     * @param array $day_dates An array of timestamps for the past 24 hours (2014-03-28T05:43:00+00:00)
     * @param array $day_values An array of values matching the timestamps (Must be 24 values)
     * @param float $week_avg Average value for past 7 days
     * @param int $week_start UNIX timestamp for start of metric timeframe
     * @param array $week_dates An array of timestamps for the past 7 days (2014-03-28T05:43:00+00:00)
     * @param array $week_values An array of values matching the timestamps (Must be 7 values)
     * @param float $month_avg Average value for past 30 days
     * @param int $month_start UNIX timestamp for start of metric timeframe
     * @param array $month_dates An array of timestamps for the past 30 days (2014-03-28T05:43:00+00:00)
     * @param array $month_values An array of values matching the timestamps (Must be 30 values)
     * @return object
     */
    public function MetricUpdate($statuspage_id, $metric_id, $day_avg, $day_start, $day_dates, $day_values,
                                 $week_avg, $week_start, $week_dates, $week_values,
                                 $month_avg, $month_start, $month_dates, $month_values) {
        $data = [];
        $data['statuspage_id'] = $statuspage_id;
        $data['metric_id'] = $metric_id;
        $data['day_avg'] = $day_avg;
        $data['day_start'] = $day_start;
        $data['day_dates'] = $day_dates;
        $data['day_values'] = $day_values;
        $data['week_avg'] = $week_avg;
        $data['week_start'] = $week_start;
        $data['week_dates'] = $week_dates;
        $data['week_values'] = $week_values;
        $data['month_avg'] = $month_avg;
        $data['month_start'] = $month_start;
        $data['month_dates'] = $month_dates;
        $data['month_values'] = $month_values;

        $guzzleResponse = $this->guzzleClient->post('metric/update', ['json' => $data]);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    // STATUS
    /**
     * Show the summary status for all components and containers
     *
     * @param string $statuspage_id Status page ID
     * @return object
     */
    public function StatusSummary($statuspage_id) {
        $guzzleResponse = $this->guzzleClient->request('GET', 'status/summary/' . $statuspage_id, []);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    // SUBSCRIBER

    /**
     * List all subscribers
     *
     * @param string $statuspage_id Status page ID
     * @return object
     */
    public function SubscriberList($statuspage_id) {
        $guzzleResponse = $this->guzzleClient->get('subscriber/list/' . $statuspage_id);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Add a new subscriber
     *
     * @param string $statuspage_id Status page ID
     * @param string $method Communication method of subscriber. Valid methods are `email`, `sms` or `webhook`
     * @param string $address Subscriber address (SMS number must include country code ie. +1)
     * @param int $silent Supress the welcome message (1 = Do not send notification)
     * @param string $granular List of component_container combos
     * @return object
     */
    public function SubscriberAdd($statuspage_id, $method, $address, $silent = 1, $granular = '') {
        $data = [];
        $data['statuspage_id'] = $statuspage_id;
        $data['method'] = $method;
        $data['address'] = $address;
        $data['silent'] = $silent;
        $data['granular'] = $granular;

        $guzzleResponse = $this->guzzleClient->post('subscriber/add', ['json' => $data]);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Update existing subscriber
     *
     * @param string $statuspage_id Status page ID
     * @param string $subscriber_id Subscriber ID
     * @param string $address Subscriber address (SMS number must include country code ie. +1)
     * @param string $granular List of component_container combos
     * @return object
     */
    public function SubscriberUpdate($statuspage_id, $subscriber_id, $address, $granular = '') {
        $data = [];
        $data['statuspage_id'] = $statuspage_id;
        $data['subscriber_id'] = $subscriber_id;
        $data['address'] = $address;
        $data['granular'] = $granular;

        $guzzleResponse = $this->guzzleClient->patch('subscriber/update', ['json' => $data]);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }

    /**
     * Delete subscriber
     *
     * @param string $statuspage_id Status page ID
     * @param string $subscriber_id Subscriber ID
     * @return object
     */
    public function SubscriberRemove($statuspage_id, $subscriber_id) {
        $guzzleResponse = $this->guzzleClient->delete('subscriber/remove/' . $statuspage_id . '/' . $subscriber_id);
        if($guzzleResponse->getStatusCode() == 200) return json_decode($guzzleResponse->getBody());
    }
}

