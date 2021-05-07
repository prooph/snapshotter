# Changelog

## [v2.2.0](https://github.com/prooph/snapshotter/tree/v2.2.0)

[Full Changelog](https://github.com/prooph/snapshotter/compare/v2.1.0...v2.2.0)

**Implemented enhancements:**

- Php8 support [\#36](https://github.com/prooph/snapshotter/pull/36) ([fritz-gerneth](https://github.com/fritz-gerneth))

**Merged pull requests:**

- Update cs headers [\#35](https://github.com/prooph/snapshotter/pull/35) ([basz](https://github.com/basz))

## [v1.3.0](https://github.com/prooph/snapshotter/tree/v1.3.0) (2018-02-27)

[Full Changelog](https://github.com/prooph/snapshotter/compare/v1.2.0...v1.3.0)

**Merged pull requests:**

- Add snapshot interface [\#33](https://github.com/prooph/snapshotter/pull/33) ([sandrokeil](https://github.com/sandrokeil))

## [v2.1.0](https://github.com/prooph/snapshotter/tree/v2.1.0) (2017-12-15)

[Full Changelog](https://github.com/prooph/snapshotter/compare/v2.0.1...v2.1.0)

**Implemented enhancements:**

- test php 7.2 on travis [\#32](https://github.com/prooph/snapshotter/pull/32) ([prolic](https://github.com/prolic))

**Fixed bugs:**

- fix snapshot read model [\#31](https://github.com/prooph/snapshotter/pull/31) ([prolic](https://github.com/prolic))

**Closed issues:**

- Events are applied multiple times when fetching aggregate through snapshot store [\#30](https://github.com/prooph/snapshotter/issues/30)

## [v2.0.1](https://github.com/prooph/snapshotter/tree/v2.0.1) (2017-11-18)

[Full Changelog](https://github.com/prooph/snapshotter/compare/v2.0.0...v2.0.1)

**Closed issues:**

- making TakeSnapshot command async [\#12](https://github.com/prooph/snapshotter/issues/12)

**Merged pull requests:**

- Fixes unlimited memory consumption by clearing identityMap of Aggrega… [\#29](https://github.com/prooph/snapshotter/pull/29) ([Adapik](https://github.com/Adapik))
- Restructure docs [\#27](https://github.com/prooph/snapshotter/pull/27) ([codeliner](https://github.com/codeliner))

## [v2.0.0](https://github.com/prooph/snapshotter/tree/v2.0.0) (2017-03-30)

[Full Changelog](https://github.com/prooph/snapshotter/compare/v2.0.0-beta1...v2.0.0)

**Implemented enhancements:**

- Update snapshot read model to allow reset\(\) and delete\(\) [\#20](https://github.com/prooph/snapshotter/issues/20)
- Updates [\#22](https://github.com/prooph/snapshotter/pull/22) ([prolic](https://github.com/prolic))
- New implementation [\#16](https://github.com/prooph/snapshotter/pull/16) ([prolic](https://github.com/prolic))

**Closed issues:**

- Allow snapshotter to update its own state to idle when it is halted [\#19](https://github.com/prooph/snapshotter/issues/19)

**Merged pull requests:**

- Composer [\#25](https://github.com/prooph/snapshotter/pull/25) ([basz](https://github.com/basz))
- Projection rename [\#24](https://github.com/prooph/snapshotter/pull/24) ([basz](https://github.com/basz))
- annotation [\#23](https://github.com/prooph/snapshotter/pull/23) ([basz](https://github.com/basz))
- remove unused dependencies [\#18](https://github.com/prooph/snapshotter/pull/18) ([basz](https://github.com/basz))
- Bugfix [\#17](https://github.com/prooph/snapshotter/pull/17) ([basz](https://github.com/basz))

## [v2.0.0-beta1](https://github.com/prooph/snapshotter/tree/v2.0.0-beta1) (2016-12-13)

[Full Changelog](https://github.com/prooph/snapshotter/compare/v1.2.0...v2.0.0-beta1)

**Implemented enhancements:**

- take snapshot by event name [\#15](https://github.com/prooph/snapshotter/pull/15) ([sandrokeil](https://github.com/sandrokeil))
- Support for PHP 7.1 [\#13](https://github.com/prooph/snapshotter/pull/13) ([prolic](https://github.com/prolic))

**Closed issues:**

- Update to coveralls ^1.0 [\#8](https://github.com/prooph/snapshotter/issues/8)

## [v1.2.0](https://github.com/prooph/snapshotter/tree/v1.2.0) (2016-10-17)

[Full Changelog](https://github.com/prooph/snapshotter/compare/v1.1.1...v1.2.0)

## [v1.1.1](https://github.com/prooph/snapshotter/tree/v1.1.1) (2016-07-27)

[Full Changelog](https://github.com/prooph/snapshotter/compare/v1.1.0...v1.1.1)

**Fixed bugs:**

- fix -\>getParam\('recordedEvents', new \ArrayIterator\(\)\) [\#11](https://github.com/prooph/snapshotter/pull/11) ([prolic](https://github.com/prolic))

## [v1.1.0](https://github.com/prooph/snapshotter/tree/v1.1.0) (2016-05-08)

[Full Changelog](https://github.com/prooph/snapshotter/compare/v1.0...v1.1.0)

**Merged pull requests:**

- Prepare 1.1 Release  [\#10](https://github.com/prooph/snapshotter/pull/10) ([codeliner](https://github.com/codeliner))
- update factories to interop-config 1.0 [\#9](https://github.com/prooph/snapshotter/pull/9) ([sandrokeil](https://github.com/sandrokeil))

## [v1.0](https://github.com/prooph/snapshotter/tree/v1.0) (2015-11-22)

[Full Changelog](https://github.com/prooph/snapshotter/compare/53bf2373d8f95cca0e2cb50ce8ced6cd709043df...v1.0)

**Implemented enhancements:**

- Provide factories  [\#2](https://github.com/prooph/snapshotter/issues/2)
- Add factories [\#4](https://github.com/prooph/snapshotter/pull/4) ([prolic](https://github.com/prolic))

**Closed issues:**

- Document set up [\#3](https://github.com/prooph/snapshotter/issues/3)

**Merged pull requests:**

- updated bookdown templates to version 0.2.0 [\#7](https://github.com/prooph/snapshotter/pull/7) ([sandrokeil](https://github.com/sandrokeil))
- added bookdown.io documentation [\#6](https://github.com/prooph/snapshotter/pull/6) ([sandrokeil](https://github.com/sandrokeil))
- add docs [\#5](https://github.com/prooph/snapshotter/pull/5) ([prolic](https://github.com/prolic))
- Snapshotter implementation [\#1](https://github.com/prooph/snapshotter/pull/1) ([prolic](https://github.com/prolic))



\* *This Changelog was automatically generated by [github_changelog_generator](https://github.com/github-changelog-generator/github-changelog-generator)*
