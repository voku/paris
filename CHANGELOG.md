Changelog
---------

#### 1.6.1

* Added new composer-fork [[voku](https://github.com/voku/paris/commit/1a7f4889f54b0f3dc7ffefdac8df2454db685ab1)]

#### 1.6.0 - released 2015-08-03

* Added namespace "paris\orm"
* Added missing __unset syntax [[qyanu](https://github.com/qyanu/parisorm/commit/7e234cc817b42ee10fd8d2419345ba4ea40b6768)]
* Added @method tags for magic methods [[stellis](https://github.com/stellis)]
* Refactoring "_get_static_property()" -> so we can use dynamic variables via Models (__constructor) [[voku](https://github.com/voku/paris/commit/37ed7dd6003ba2ad5644607c934b8bc8a0311c86)]
* Added missing '@' for a @return tag for create() method [[stellis](https://github.com/stellis)]
* Added global configuration option Model::$short_table_names [[michaelward82](https://github.com/michaelward82/paris/commit/e505f269f281fce3cd8a345812725a0af599bb65)]
* Fixed issue with the has_many_through method [[Ralphunter](https://github.com/Ralphunter/paris/commit/205dd62f8e5b20d6d4c867514285c90f026f9c6b)]

#### 1.5.4 - released 2014-09-23

* Corrects return value in docblock for 2 Model functions [[michaelward82](https://github.com/michaelward82)] - [issue #99](https://github.com/j4mie/paris/pull/99)

#### 1.5.3 - released 2014-06-25

* Remove erroneously committed git merge backup file

#### 1.5.2 - released 2014-06-23

* Paris incorrectly relying on old Idiorm version in the composer.json [[ilsenem](https://github.com/ilsenem)] - [issue #96](https://github.com/j4mie/paris/pull/96)

#### 1.5.1 - released 2014-06-22

* Remove HHVM build target from travis-ci as there is a bug in HHVM

#### 1.5.0 - released 2014-06-22

* Allows static calling of Model subclasses, ignoring namespace info during table name generation [[michaelward82](https://github.com/michaelward82)] - [issue #90](https://github.com/j4mie/paris/issues/90)
* Prevent invalid method calls from triggering infinite recursion [[michaelward82](https://github.com/michaelward82)] - [issue #75](https://github.com/j4mie/idiorm/issues/75)
* Allow chaining of the `set()` and `set_expr()` methods [[naga3](https://github.com/naga3)] - [issue #94](https://github.com/j4mie/paris/issues/94)
* Add HHVM to travis-ci build matrix [[ptarjan](https://github.com/ptarjan)] - [issue #81](https://github.com/j4mie/idiorm/issues/81)
* Improve join documentation [[rhynodesigns](https://github.com/rhynodesigns)] - [issue #92](https://github.com/j4mie/paris/issues/92)
* Improve code docblock [[michaelward82](https://github.com/michaelward82)] - [issue #91](https://github.com/j4mie/paris/issues/91)
* Improve code docblocks and whitespace [[michaelward82](https://github.com/michaelward82)] - [issue #93](https://github.com/j4mie/paris/issues/93)
* Improve connections documentation [[kkeiper1103](https://github.com/kkeiper1103)] - [issue #79](https://github.com/j4mie/paris/issues/79)

#### 1.4.2 - released 2013-12-12

**Patch update to remove a broken pull request** - may have consequences for users of 1.4.0 and 1.4.1 that exploited the "`find_many()` now returns an associative array with the databases primary ID as the array keys" change that was merged in 1.4.0.

* Back out pull request/issue [#133](https://github.com/j4mie/idiorm/pull/133) as it breaks backwards compatibility in previously unexpected ways (see Idiorm issues [#162](https://github.com/j4mie/idiorm/pull/162), [#156](https://github.com/j4mie/idiorm/issues/156) and [#133](https://github.com/j4mie/idiorm/pull/133#issuecomment-29063108)) - sorry for merging this change into Paris - closes Idiorm [issue 156](https://github.com/j4mie/idiorm/issues/156)

#### 1.4.1 - released 2013-09-05

* Increment composer.json requirement for Idiorm to 1.4.0 [[michaelward82](https://github.com/michaelward82)] - [Issue #72](https://github.com/j4mie/paris/pull/72)

#### 1.4.0 - released 2013-09-05

* Call methods against model class directly eg. `User::find_many()` - PHP 5.3 only [[Lapayo](https://github.com/Lapayo)] - [issue #62](https://github.com/j4mie/idiorm/issues/62)
* `find_many()` now returns an associative array with the databases primary ID as the array keys [[Surt](https://github.com/Surt)] - see commit [9ac0ae7](https://github.com/j4mie/paris/commit/9ac0ae7d302f1980c95b97a98cbd6d5b2c04923f) and Idiorm [issue #133](https://github.com/j4mie/idiorm/issues/133)
* Add PSR-1 compliant camelCase method calls to Idiorm (PHP 5.3+ required) [[crhayes](https://github.com/crhayes)] - [issue #59](https://github.com/j4mie/idiorm/issues/59)
* Allow specification of connection on relation methods [[alexandrusavin](https://github.com/alexandrusavin)] - [issue #55](https://github.com/j4mie/idiorm/issues/55)
* Make tests/bootstrap.php HHVM compatible [[JoelMarcey](https://github.com/JoelMarcey)] - [issue #71](https://github.com/j4mie/idiorm/issues/71)
* belongs_to doesn't work with $auto_prefix_models ([issue #70](https://github.com/j4mie/paris/issues/70))

#### 1.3.0 - released 2013-01-31

* Documentation moved to [paris.rtfd.org](http://paris.rtfd.org) and now built using [Sphinx](http://sphinx-doc.org/)
* Add support for multiple database connections [[tag](https://github.com/tag)] - [issue #15](https://github.com/j4mie/idiorm/issues/15)
* Allow a prefix for model class names - see Configuration in the documentation - closes [issues #33](https://github.com/j4mie/paris/issues/33)
* Exclude tests and git files from git exports (used by composer)
* Implement `set_expr` - closes [issue #39](https://github.com/j4mie/paris/issues/39)
* Add `is_new` - closes [issue #40](https://github.com/j4mie/paris/issues/40)
* Add support for the new IdiormResultSet object in Idiorm - closes [issue #14](https://github.com/j4mie/paris/issues/14)
* Change Composer to use a classmap so that autoloading is better supported [[javierd](https://github.com/javiervd)] - [issue #44](https://github.com/j4mie/paris/issues/44)
* Move tests into PHPUnit to match Idiorm
* Update included Idiorm version for tests
* Move documentation to use Sphinx

#### 1.2.0 - released 2012-11-14

* Setup composer for installation via packagist (j4mie/paris)
* Add in basic namespace support, see [issue #20](https://github.com/j4mie/paris/issues/20)
* Allow properties to be set as an associative array in `set()`, see [issue #13](https://github.com/j4mie/paris/issues/13)
* Patch in idiorm now allows empty models to be saved (j4mie/idiorm see [issue #58](https://github.com/j4mie/paris/issues/58))

#### 1.1.1 - released 2011-01-30

* Fix incorrect tests, see [issue #12](https://github.com/j4mie/paris/issues/12)

#### 1.1.0 - released 2011-01-24

* Add `is_dirty` method

#### 1.0.0 - released 2010-12-01

* Initial release
