# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [unreleased]

### Changed

- Load loader load from global theme
- Decouple css and javascript from aup_in_form.tpl.php
- Refactor aup_in_form.tpl.php template


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
