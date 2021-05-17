## Change Log

### v1.5.1 (2021/5/17)
- Added Microsoft Teams

### v1.5.0 (2020/9/8)
- Allow Guzzle to use versions above 6.0

### v1.4.0 (2020/2/17)
- Adding message_subject to all incident and maintenance methods
- Fix variable type for subscriber/add silent flag
- Fixed the ordering of the message_subject argument for the incident and maintenance methods which was breaking backwards compatibility

### v1.3.0 (2018/11/27)
- Changed variables to proper type (int->str)
- Fixed maintenance delete test

### v1.2.0 (2018/2/10)
- Change /component/status/update to use a single component
- Support retrieving single incident/maintenance events. New incident/maintenance methods to fetch list of IDs
- Remove unnecessary require vendor/autoload (@flightsupport)

### v1.1.0 (2017/12/20)
- Fixed notification variable type bug
- Updated Travis test config
- Update test subscriber config
- Updated maintenance/schedule to handle infrastructure_affected
- Updated incident/create to handle infrastructure_affected

### v1.0.0 (2016/1/8)
- Initial release