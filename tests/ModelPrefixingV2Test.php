<?php

namespace Paris\Tests {

  use idiorm\orm\ORM;
  use MockPDO;
  use paris\orm\Model;
  use PHPUnit_Framework_TestCase;

  /** @noinspection PhpMultipleClassesDeclarationsInOneFile */
  class ModelPrefixingTest53 extends PHPUnit_Framework_TestCase
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

    public function testNoPrefixOnAutoTableName()
    {
      Model::$auto_prefix_models = null;
      Model::factory('\Tests\Simple')->find_many();
      $expected = 'SELECT * FROM `tests_simple`';
      self::assertEquals($expected, ORM::get_last_query());
    }

    public function testPrefixOnAutoTableName()
    {
      Model::$auto_prefix_models = '\\Tests\\';
      Model::factory('Simple')->find_many();
      $expected = 'SELECT * FROM `tests_simple`';
      self::assertEquals($expected, ORM::get_last_query());
    }

    public function testPrefixOnAutoTableNameWithTableSpecified()
    {
      Model::$auto_prefix_models = '\\Tests\\';
      Model::factory('TableSpecified')->find_many();
      $expected = 'SELECT * FROM `simple`';
      self::assertEquals($expected, ORM::get_last_query());
    }

    public function testNamespacePrefixSwitching()
    {
      Model::$auto_prefix_models = '\\Tests\\';
      Model::factory('TableSpecified')->find_many();
      $expected = 'SELECT * FROM `simple`';
      self::assertEquals($expected, ORM::get_last_query());

      Model::$auto_prefix_models = '\\Tests2\\';
      Model::factory('TableSpecified')->find_many();
      $expected = 'SELECT * FROM `simple`';
      self::assertEquals($expected, ORM::get_last_query());
    }

    public function testPrefixOnHasManyThroughRelation()
    {
      Model::$auto_prefix_models = '\\Tests3\\';
      /* @var $book \Book */
      $book = Model::factory('Book')->find_one(1);
      $authors = $book->authors()->find_many();
      $expected = "SELECT `prefix_author`.* FROM `prefix_author` JOIN `prefix_authorbook` ON `prefix_author`.`id` = `prefix_authorbook`.`prefix_author_id` WHERE `prefix_authorbook`.`prefix_book_id` = '1'";
      self::assertEquals($expected, ORM::get_last_query());
      self::assertEquals(true, $authors[0] instanceof Model);
    }

    public function testPrefixOnHasManyThroughRelationWithCustomIntermediateModelAndKeyNames()
    {
      Model::$auto_prefix_models = '\\Tests3\\';
      /* @var $book2 \BookTwo */
      $book2 = Model::factory('BookTwo')->find_one(1);
      $authors2 = $book2->authors()->find_many();
      $expected = "SELECT `prefix_author`.* FROM `prefix_author` JOIN `prefix_authorbook` ON `prefix_author`.`id` = `prefix_authorbook`.`custom_author_id` WHERE `prefix_authorbook`.`custom_book_id` = '1'";
      self::assertEquals($expected, ORM::get_last_query());
      self::assertEquals(true, $authors2[0] instanceof Model);
    }
  }
}

namespace Tests {

  use paris\orm\Model;

  /** @noinspection PhpMultipleClassesDeclarationsInOneFile */
  class Simple extends Model
  {
  }

  /** @noinspection PhpMultipleClassesDeclarationsInOneFile */
  class TableSpecified extends Model
  {
    public static $_table = 'simple';
  }
}
namespace Tests2 {

  use paris\orm\Model;

  /** @noinspection PhpMultipleClassesDeclarationsInOneFile */
  class Simple extends Model
  {
  }

  /** @noinspection PhpMultipleClassesDeclarationsInOneFile */
  class TableSpecified extends Model
  {
    public static $_table = 'simple';
  }
}
namespace Tests3 {

  use paris\orm\Model;

  /** @noinspection PhpMultipleClassesDeclarationsInOneFile */
  class Author extends Model
  {
    public static $_table = 'prefix_author';
  }

  /** @noinspection PhpMultipleClassesDeclarationsInOneFile */
  class AuthorBook extends Model
  {
    public static $_table = 'prefix_authorbook';
  }

  /** @noinspection PhpMultipleClassesDeclarationsInOneFile */
  class Book extends Model
  {
    public static $_table = 'prefix_book';

    /**
     * @return \paris\orm\ORMWrapper
     */
    public function authors()
    {
      return $this->has_many_through('Author');
    }
  }

  /** @noinspection PhpMultipleClassesDeclarationsInOneFile */
  class BookTwo extends Model
  {
    public static $_table = 'prefix_booktwo';

    /**
     * @return \paris\orm\ORMWrapper
     */
    public function authors()
    {
      return $this->has_many_through('Author', 'AuthorBook', 'custom_book_id', 'custom_author_id');
    }
  }
}
