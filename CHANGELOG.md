# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v2.0.3] - 2021-01-25

### Fixed

- Namespace errors

## [v2.0.2] - 2021-01-22

### Added

- Option for configuring the timeout for AUP update API calls

### Changed

- Load loader load from global SimpleSAMLphp theme
- Decouple css and javascript
- Refactor template

## [v1.0.3] - 2021-01-21

### Added

- Option for configuring the timeout for AUP update API calls

### Changed

- Load loader load from global SimpleSAMLphp theme
- Decouple css and javascript
- Refactor template

## [v2.0.1] - 2021-01-17

### Fixed

- Include AUPs never agreed before to the list of AUPs that need to be (re)accepted

## [v2.0.0] - 2021-01-14

This version is compatible with [SimpleSAMLphp v1.17](https://simplesamlphp.org/docs/1.17/simplesamlphp-changelog)

### Changed

- Compatibility with [SimpleSAMLphp v1.17](https://simplesamlphp.org/docs/1.17/simplesamlphp-changelog)

## [v1.0.2] - 2021-01-15

### Fixed

- Include AUPs never agreed before to the list of AUPs that need to be (re)accepted

## [v1.0.1] - 2021-01-14

### Changed

- Improve handling of missing AUP information
- Improve logging

## [v1.0.0] - 2021-01-12

This version is compatible with [SimpleSAMLphp v1.14](https://simplesamlphp.org/docs/1.14/simplesamlphp-changelog)

### Added

- Initial version of UpdateAUP authproc filter for allowing users to update agreement to relevant AUPs
