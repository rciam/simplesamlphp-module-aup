# simplesamlphp-module-aup
SimpleSAMLphp module for handling AUP information

## Configuration

The following configuration options are available:
  - `aupApiEndpoint`: The API endpoint for storing aup agreements
  - `aupListEndpoint`: The endpoint for aups list page of the user
  - `apiUsername`: The username of the API user
  - `apiPassword`: The password of the API user
  - `spBlacklist`: An array of strings that contains the SPs that the module will skip to process or can be empty.

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