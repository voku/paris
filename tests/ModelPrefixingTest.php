<?php

use paris\orm\Model;

/**
 * Class ModelPrefixingTest
 */
class ModelPrefixingTest extends PHPUnit_Framework_TestCase
{

  public function setUp()
  {
    // Set up the dummy database connection
    ORM::set_db(new MockPDO('sqlite::memory:'));

    // Enable logging
    ORM::configure('logging', true);

    Model::$auto_prefix_models = null;
  }

  public function tearDown()
  {
    ORM::configure('logging', false);
    ORM::set_db(null);

    Model::$auto_prefix_models = null;
  }

  public function testStaticPropertyExists()
  {
    self::assertClassHasStaticAttribute('auto_prefix_models', 'paris\orm\Model');
    self::assertInternalType('null', Model::$auto_prefix_models);
  }

  public function testSettingAndUnSettingStaticPropertyValue()
  {
    $model_prefix = 'My_Model_Prefix_';
    self::assertInternalType('null', Model::$auto_prefix_models);
    Model::$auto_prefix_models = $model_prefix;
    self::assertInternalType('string', Model::$auto_prefix_models);
    self::assertEquals($model_prefix, Model::$auto_prefix_models);
    Model::$auto_prefix_models = null;
    self::assertInternalType('null', Model::$auto_prefix_models);
  }

  public function testNoPrefixOnAutoTableName()
  {
    Model::$auto_prefix_models = null;
    Model::factory('Simple')->find_many();
    $expected = 'SELECT * FROM `simple`';
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testPrefixOnAutoTableName()
  {
    Model::$auto_prefix_models = 'MockPrefix_';
    Model::factory('Simple')->find_many();
    $expected = 'SELECT * FROM `mock_prefix_simple`';
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testPrefixOnAutoTableNameWithTableSpecified()
  {
    Model::$auto_prefix_models = 'MockPrefix_';
    Model::factory('TableSpecified')->find_many();
    $expected = 'SELECT * FROM `simple`';
    self::assertEquals($expected, ORM::get_last_query());
  }

}
