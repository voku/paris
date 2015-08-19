<?php

use paris\orm\Model;

/**
 * Class MultipleConnectionsTest
 */
class MultipleConnectionsTest extends PHPUnit_Framework_TestCase
{
  const ALTERNATE = 'alternate';

  public function setUp()
  {

    // Set up the dummy database connection
    ORM::set_db(new MockPDO('sqlite::memory:'));
    ORM::set_db(new MockDifferentPDO('sqlite::memory:'), self::ALTERNATE);

    // Enable logging
    ORM::configure('logging', true);
    ORM::configure('logging', true, self::ALTERNATE);
  }

  public function tearDown()
  {
    ORM::configure('logging', false);
    ORM::configure('logging', false, self::ALTERNATE);

    ORM::set_db(null);
    ORM::set_db(null, self::ALTERNATE);
  }

  public function testMultipleConnections()
  {
    $simple = Model::factory('Simple')->find_one(1);
    $statement = ORM::get_last_statement();
    self::assertInstanceOf('MockPDOStatement', $statement);
    self::assertTrue($simple instanceof Simple);
    self::assertTrue($simple instanceof Model);

    $simple = Model::factory('Simple', self::ALTERNATE); // Change the object's default connection
    $simple->find_one(1);
    $statement = ORM::get_last_statement();
    self::assertInstanceOf('MockDifferentPDOStatement', $statement);

    $temp = Model::factory('Simple', self::ALTERNATE)->find_one(1);
    $statement = ORM::get_last_statement();
    self::assertInstanceOf('MockDifferentPDOStatement', $statement);
    self::assertTrue($temp instanceof Simple);
    self::assertTrue($temp instanceof Model);
  }

  public function testCustomConnectionName()
  {
    $person3 = Model::factory('ModelWithCustomConnection')->find_one(1);
    $statement = ORM::get_last_statement();
    self::assertInstanceOf('MockDifferentPDOStatement', $statement);
    self::assertTrue($person3 instanceof ModelWithCustomConnection);
    self::assertTrue($person3 instanceof Model);
  }

}
