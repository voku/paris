<?php

namespace Paris\Tests;

use MockPDO;
use ORM;
use paris\orm\Model;
use PHPUnit_Framework_TestCase;

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class ParisTest53 extends PHPUnit_Framework_TestCase
{

  public function setUp()
  {
    // Enable logging
    ORM::configure('logging', true);

    // Set up the dummy database connection
    $db = new MockPDO('sqlite::memory:');
    ORM::set_db($db);
  }

  public function tearDown()
  {
    ORM::configure('logging', false);
    ORM::set_db(null);
  }

  public function testNamespacedTableName()
  {
    Model::factory('Paris\Tests\Simple')->find_many();
    $expected = 'SELECT * FROM `paris_tests_simple`';
    self::assertEquals($expected, ORM::get_last_query());
    MustNotIgnoreNamespace::find_many();
    $expected = 'SELECT * FROM `paris_tests_must_not_ignore_namespace`';
    self::assertEquals($expected, ORM::get_last_query());
    Model::$short_table_names = true;
    MustNotIgnoreNamespace::find_many();
    $expected = 'SELECT * FROM `paris_tests_must_not_ignore_namespace`';
    self::assertEquals($expected, ORM::get_last_query());
    Model::$short_table_names = false;
    MustUseGlobalNamespaceConfig::find_many();
    $expected = 'SELECT * FROM `paris_tests_must_use_global_namespace_config`';
    self::assertEquals($expected, ORM::get_last_query());
    Model::$short_table_names = false;
    MustNotIgnoreNamespace::find_many();
    $expected = 'SELECT * FROM `paris_tests_must_not_ignore_namespace`';
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testIgnoredNamespaceTableName()
  {
    MustIgnoreNamespace::find_many();
    $expected = 'SELECT * FROM `must_ignore_namespace`';
    self::assertEquals($expected, ORM::get_last_query());
    Model::$short_table_names = true;
    MustIgnoreNamespace::find_many();
    $expected = 'SELECT * FROM `must_ignore_namespace`';
    self::assertEquals($expected, ORM::get_last_query());
    Model::$short_table_names = true;
    MustUseGlobalNamespaceConfig::find_many();
    $expected = 'SELECT * FROM `must_use_global_namespace_config`';
    self::assertEquals($expected, ORM::get_last_query());
    Model::$short_table_names = false;
    MustIgnoreNamespace::find_many();
    $expected = 'SELECT * FROM `must_ignore_namespace`';
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testModelWithCustomTable()
  {
    Model::factory('ModelWithCustomTable')->find_many();
    $expected = 'SELECT * FROM `custom_table`';
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testShortcut()
  {
    Simple::find_many();
    $expected = 'SELECT * FROM `paris_tests_simple`';
    self::assertEquals($expected, ORM::get_last_query());
  }

}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class Simple extends Model
{
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class ModelWithCustomTable extends Model
{
  public static $_table = 'custom_table';
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class MustIgnoreNamespace extends Model
{
  public static $_table_use_short_name = true;
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class MustNotIgnoreNamespace extends Model
{
  public static $_table_use_short_name = false;
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class MustUseGlobalNamespaceConfig extends Model
{
  public static $_table_use_short_name = null;
}
