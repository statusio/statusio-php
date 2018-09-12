# PHP Status.io

PHP Package for Status.io

## Installation

```
composer require statusio/statusio-php
```

## Usage

```php
use Statusio\StatusioClient;

$api = new StatusioClient('<api_id>', '<api_key>');
$result = $api->StatusSummary('<statuspage_id>');

var_dump($result);
```

View the full API documentation at: http://developers.status.io/

## Methods

```php
    $api->ComponentList($statuspage_id)
    $api->ComponentStatusUpdate($statuspage_id, $component, $container, $details, $current_status)
    $api->IncidentList($statuspage_id)
    $api->IncidentListByID($statuspage_id)
    $api->IncidentMessage($statuspage_id, $message_id)
    $api->IncidentSingle($statuspage_id, $incident_id)
    $api->IncidentCreate($statuspage_id, $incident_name, $incident_details, $infrastructure_affected, 
                         $current_status, $current_state, $notifications = 0, $all_infrastructure_affected = 0)
    $api->IncidentUpdate($statuspage_id, $incident_id, $incident_details, $current_status, $current_state, 
                         $notifications = 0)
    $api->IncidentResolve($statuspage_id, $incident_id, $incident_details, $current_status, $current_state, 
                          $notifications = 0)
    $api->IncidentDelete($statuspage_id, $incident_id)
    $api->MaintenanceList($statuspage_id)
    $api->MaintenanceListByID($statuspage_id)
    $api->MaintenanceMessage($statuspage_id, $message_id)
    $api->MaintenanceSingle($statuspage_id, $maintenance_id)
    $api->MaintenanceSchedule($statuspage_id, $maintenance_name, $maintenance_details, $infrastructure_affected, 
                              $date_planned_start, $time_planned_start, $date_planned_end, 
                              $time_planned_end, $automation = 0, $all_infrastructure_affected = 0, 
                              $maintenance_notify_now = 0, $maintenance_notify_1_hr = 0, 
                              $maintenance_notify_24_hr = 0, $maintenance_notify_72_hr = 0)
    $api->MaintenanceStart($statuspage_id, $maintenance_id, $maintenance_details, $notifications = 0)
    $api->MaintenanceUpdate($statuspage_id, $maintenance_id, $maintenance_details, $notifications = 0) 
    $api->MaintenanceFinish($statuspage_id, $maintenance_id, $maintenance_details, $notifications = 0)
    $api->MaintenanceDelete($statuspage_id, $maintenance_id)
    $api->MetricUpdate($statuspage_id, $metric_id, $day_avg, $day_start, $day_dates, $day_values,
                       $week_avg, $week_start, $week_dates, $week_values,
                       $month_avg, $month_start, $month_dates, $month_values)
    $api->StatusSummary($statuspage_id)
    $api->SubscriberList($statuspage_id)
    $api->SubscriberAdd($statuspage_id, $method, $address, $silent = 1, $granular = '')
    $api->SubscriberUpdate($statuspage_id, $subscriber_id, $address, $granular = '') 
    $api->SubscriberRemove($statuspage_id, $subscriber_id)
```

## Constants

Predefined constants and notification setup based on bitmask

```php
    $result = $api->IncidentCreate(
            '<statuspage_id>', 
            'Incident name', 
            'Incident details',
            ['<component_id>-<container_id>'], 
            StatusioClient::STATUS_OPERATIONAL,
            StatusioClient::STATE_IDENTIFIED, 
            StatusioClient::NOTIFY_EMAIL + StatusioClient::NOTIFY_SMS // equal to 'notify_email' = 1, 'notify_sms' = 1 and all other notifications = 0 
);
```

So if you want to notify users by Slack and Twitter should set `$notifications` parameter equal to `StatusioClient::NOTIFY_SLACK + StatusioClient::NOTIFY_SOCIAL`. 
To disable all notifications just set parameter equal to `0`.

Full list of constants

```php
StatusioClient::STATUS_OPERATIONAL;
StatusioClient::STATUS_DEGRADED_PERFORMANCE;
StatusioClient::STATUS_PARTIAL_SERVICE_DISRUPTION;
StatusioClient::STATUS_SERVICE_DISRUPTION;
StatusioClient::STATUS_SECURITY_EVENT;

StatusioClient::STATE_INVESTIGATING;
StatusioClient::STATE_IDENTIFIED;
StatusioClient::STATE_MONITORING;

StatusioClient::NOTIFY_EMAIL;
StatusioClient::NOTIFY_SMS;
StatusioClient::NOTIFY_WEBHOOK;
StatusioClient::NOTIFY_SOCIAL;
StatusioClient::NOTIFY_IRC;
StatusioClient::NOTIFY_HIPCHAT;
StatusioClient::NOTIFY_SLACK;
```

