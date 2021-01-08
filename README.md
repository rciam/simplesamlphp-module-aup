# simplesamlphp-module-aup
SimpleSAMLphp module for handling AUP information

## Configuration

The following configuration options are available:
  - `aupApiEndpoint`: Required, the API endpoint for storing aup agreements
  - `aupListEndpoint`: Required, the endpoint for aups list page of the user
  - `apiUsername`: Required, the username of the API user
  - `apiPassword`: Required, the password of the API user
  - `userIdAttribute` : Optional, a string containing the name of the attribute whose value will be used to check if it is in userIdBlacklist.
  - `spBlacklist`: Optional, an array of strings that contains the SPs that the module will skip the process.
  - `userIdBlacklist`: Optional, an array of strings that contains the userIds for which the module will skip the aup process. In order to be activated a value is required to userIdAttribute option.

### Example configuration

```
     'authproc' => array(
        ...
        '82' => array(
             'class' => 'aup:Client',
             'aupApiEndpoint' => '',
             'aupListEndpoint' => '',
             'apiUsername' => '',
             'apiPassword' => '',
             'userIdAttribute' => '',
             'spBlacklist' => array(),
             'userIdBlacklist' => array()
        ),
```

## Compatibility matrix

This table matches the module version with the supported SimpleSAMLphp version.

| Module |  SimpleSAMLphp |
|:------:|:--------------:|
| v1.0   | v1.14          |

# License

Licensed under the Apache 2.0 license, for details see `LICENSE`.