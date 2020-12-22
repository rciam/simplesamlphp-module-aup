# simplesamlphp-module-aup
SimpleSAMLphp module for handling AUP information

## Configuration

The following configuration options are available:
  - `aupApiEndpoint`: Required, the API endpoint for storing aup agreements
  - `aupListEndpoint`: Required, the endpoint for aups list page of the user
  - `apiUsername`: Required, the username of the API user
  - `apiPassword`: Required, the password of the API user
  - `spBlacklist`: Optional, an array of strings that contains the SPs that the module will skip to process or can be empty.

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
             'spBlacklist' => array(),
        ),
```

## Compatibility matrix

This table matches the module version with the supported SimpleSAMLphp version.

| Module |  SimpleSAMLphp |
|:------:|:--------------:|
| v1.0   | v1.14          |

# License

Licensed under the Apache 2.0 license, for details see `LICENSE`.